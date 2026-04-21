<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use App\Services\MatchingEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PaymentController extends Controller
{
    protected $matchingEngine;

    public function __construct(MatchingEngine $matchingEngine)
    {
        $this->matchingEngine = $matchingEngine;
    }

    public function index()
    {
        $payments = Payment::with('student')->orderBy('date', 'desc')->get();
        return view('payments.index', compact('payments'));
    }

    public function create()
    {
        $students = Student::all();
        return view('payments.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'mode' => 'required|in:virement,espece,cheque,carte',
            'reference' => 'required|string|unique:payments',
            'nom_payeur' => 'nullable|string',
            'recu' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        if ($request->hasFile('recu')) {
            $path = $request->file('recu')->store('recus', 'public');
            $validated['recu_path'] = $path;
        }

        $payment = Payment::create($validated);

        // Matching automatique si pas d'étudiant sélectionné
        if (!$payment->student_id) {
            $matchedStudent = $this->matchingEngine->advancedMatch($payment);
            if ($matchedStudent) {
                $payment->student_id = $matchedStudent->id;
                $payment->est_auto_match = true;
                $payment->save();
            }
        }

        return redirect()->route('payments.index')
            ->with('success', 'Paiement enregistré avec succès');
    }

    public function edit(Payment $payment)
    {
        $students = Student::all();
        return view('payments.edit', compact('payment', 'students'));
    }

    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'student_id' => 'nullable|exists:students,id',
            'montant' => 'required|numeric|min:0',
            'date' => 'required|date',
            'mode' => 'required|in:virement,espece,cheque,carte',
            'reference' => 'required|string|unique:payments,reference,' . $payment->id,
            'nom_payeur' => 'nullable|string'
        ]);

        $payment->update($validated);

        return redirect()->route('payments.index')
            ->with('success', 'Paiement modifié avec succès');
    }

    public function destroy(Payment $payment)
    {
        if ($payment->recu_path) {
            Storage::disk('public')->delete($payment->recu_path);
        }
        $payment->delete();
        
        return redirect()->route('payments.index')
            ->with('success', 'Paiement supprimé avec succès');
    }
}