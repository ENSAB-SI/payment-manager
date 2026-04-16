<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Payment;
use App\Models\BankTransaction;

class MatchingEngine
{
    public function matchPaymentWithStudent(Payment $payment): ?Student
    {
        // Niveau 1: Correspondance par code unique dans la reference
        $student = Student::where('code_unique', $payment->reference)->first();
        if ($student) return $student;

        // Niveau 2: Correspondance par reference exacte
        $student = Student::whereHas('payments', function($query) use ($payment) {
            $query->where('reference', $payment->reference);
        })->first();
        if ($student) return $student;

        // Niveau 3: Correspondance par nom + montant
        if ($payment->nom_payeur) {
            $student = Student::where('nom', 'LIKE', "%{$payment->nom_payeur}%")
                ->orWhere('prenom', 'LIKE', "%{$payment->nom_payeur}%")
                ->where('montant_total', '>=', $payment->montant)
                ->first();
            if ($student) return $student;
        }

        // Niveau 4: Correspondance par montant + date approximative
        $student = Student::where('montant_total', '>=', $payment->montant)
            ->whereDoesntHave('payments', function($query) use ($payment) {
                $query->whereMonth('date', $payment->date->month);
            })
            ->first();

        return $student;
    }

    public function matchBankTransaction(BankTransaction $transaction): array
    {
        $result = [
            'status' => 'non_trouve',
            'student' => null,
            'confidence' => 0
        ];

        // Niveau 1: Code unique dans la reference
        preg_match('/ENSA-\d{2}-[ML]\d-[A-Z]+\d{2}/', $transaction->reference, $matches);
        if (!empty($matches)) {
            $student = Student::where('code_unique', $matches[0])->first();
            if ($student) {
                $result['status'] = 'match';
                $result['student'] = $student;
                $result['confidence'] = 100;
                return $result;
            }
        }

        // Niveau 2: Reference correspond a un paiement
        $payment = Payment::where('reference', $transaction->reference)->first();
        if ($payment && $payment->student_id) {
            $result['status'] = 'match';
            $result['student'] = $payment->student;
            $result['confidence'] = 95;
            return $result;
        }

        // Niveau 3: Nom + montant
        $student = Student::where('nom', 'LIKE', "%{$transaction->emetteur}%")
            ->orWhere('prenom', 'LIKE', "%{$transaction->emetteur}%")
            ->where('reste_a_payer', '>=', $transaction->montant)
            ->first();

        if ($student) {
            $result['status'] = 'match';
            $result['student'] = $student;
            $result['confidence'] = 70;
            return $result;
        }

        // Niveau 4: Montant + date
        $students = Student::where('reste_a_payer', '>=', $transaction->montant)->get();
        
        if ($students->count() === 1) {
            $result['status'] = 'match';
            $result['student'] = $students->first();
            $result['confidence'] = 50;
        } elseif ($students->count() > 1) {
            $result['status'] = 'doute';
            $result['confidence'] = 30;
        }

        return $result;
    }

    public function runAutomaticMatching(): array
    {
        $results = [
            'match' => 0,
            'doute' => 0,
            'non_trouve' => 0
        ];

        $transactions = BankTransaction::where('statut', 'non_trouve')->get();

        foreach ($transactions as $transaction) {
            $match = $this->matchBankTransaction($transaction);
            
            $transaction->statut = $match['status'];
            if ($match['student']) {
                $transaction->student_id = $match['student']->id;
                
                // Creer un paiement automatique
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