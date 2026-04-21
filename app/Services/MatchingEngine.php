<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Payment;
use App\Models\BankTransaction;
use Illuminate\Support\Str;

class MatchingEngine
{
    // تطبيع الاسم (إزالة التشكيل والمسافات)
    protected function normalizeName($name)
    {
        $name = Str::ascii($name);
        $name = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        return strtolower($name);
    }

    // حساب نسبة التشابه بين اسمين (0-100)
    protected function calculateNameSimilarity($name1, $name2)
    {
        similar_text($this->normalizeName($name1), $this->normalizeName($name2), $percent);
        return $percent;
    }

    // مطابقة متقدمة للدفعة
    public function advancedMatch(Payment $payment): ?Student
    {
        // المستوى 1: كود فريد في المرجع
        $student = Student::where('code_unique', $payment->reference)->first();
        if ($student) return $student;

        // المستوى 2: مرجع مطابق لدفعة سابقة
        $student = Student::whereHas('payments', function($q) use ($payment) {
            $q->where('reference', $payment->reference);
        })->first();
        if ($student) return $student;

        // المستوى 3: مطابقة ضبابية (fuzzy) للاسم + مبلغ
        if ($payment->nom_payeur) {
            $students = Student::all();
            $bestMatch = null;
            $bestScore = 0;
            foreach ($students as $s) {
                $score = $this->calculateNameSimilarity($payment->nom_payeur, $s->nom_complet);
                // مكافأة إذا كان المبلغ قريباً من المتبقي
                if (abs($s->reste_a_payer - $payment->montant) < 500) {
                    $score += 20;
                }
                if ($score > $bestScore && $score > 60) {
                    $bestScore = $score;
                    $bestMatch = $s;
                }
            }
            if ($bestMatch) return $bestMatch;
        }

        // المستوى 4: مبلغ وتاريخ تقريبي
        $possible = Student::where('reste_a_payer', '>=', $payment->montant)
            ->whereDoesntHave('payments', function($q) use ($payment) {
                $q->whereMonth('date', $payment->date->month);
            })
            ->get();
        if ($possible->count() == 1) return $possible->first();

        return null;
    }

    // مطابقة معاملات البنك
    public function matchBankTransaction(BankTransaction $transaction): array
    {
        $result = ['status' => 'non_trouve', 'student' => null, 'confidence' => 0];

        // كود فريد في المرجع
        preg_match('/ENSA-\d{2}-[ML]\d-[A-Z]+\d{2}/', $transaction->reference, $matches);
        if (!empty($matches)) {
            $student = Student::where('code_unique', $matches[0])->first();
            if ($student) {
                return ['status' => 'match', 'student' => $student, 'confidence' => 100];
            }
        }

        // مرجع مطابق لدفعة
        $payment = Payment::where('reference', $transaction->reference)->first();
        if ($payment && $payment->student_id) {
            return ['status' => 'match', 'student' => $payment->student, 'confidence' => 95];
        }

        // مطابقة اسم + مبلغ
        $student = Student::where('nom', 'LIKE', "%{$transaction->emetteur}%")
            ->orWhere('prenom', 'LIKE', "%{$transaction->emetteur}%")
            ->where('reste_a_payer', '>=', $transaction->montant)
            ->first();
        if ($student) {
            return ['status' => 'match', 'student' => $student, 'confidence' => 70];
        }

        // مبلغ وتاريخ
        $students = Student::where('reste_a_payer', '>=', $transaction->montant)->get();
        if ($students->count() === 1) {
            return ['status' => 'match', 'student' => $students->first(), 'confidence' => 50];
        } elseif ($students->count() > 1) {
            $result['status'] = 'doute';
            $result['confidence'] = 30;
        }

        return $result;
    }

    // تشغيل المطابقة التلقائية على كل المعاملات غير المطابقة
    public function runAutomaticMatching(): array
    {
        $results = ['match' => 0, 'doute' => 0, 'non_trouve' => 0];
        $transactions = BankTransaction::where('statut', 'non_trouve')->get();

        foreach ($transactions as $transaction) {
            $match = $this->matchBankTransaction($transaction);
            $transaction->statut = $match['status'];
            if ($match['student']) {
                $transaction->student_id = $match['student']->id;
                Payment::create([
                    'student_id' => $match['student']->id,
                    'montant' => $transaction->montant,
                    'date' => $transaction->date,
                    'mode' => 'virement',
                    'reference' => $transaction->reference,
                    'nom_payeur' => $transaction->emetteur,
                    'statut' => 'valide',
                    'est_auto_match' => true
                ]);
            }
            $transaction->save();
            $results[$match['status']]++;
        }
        return $results;
    }
}