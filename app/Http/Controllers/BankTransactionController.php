<?php

namespace App\Http\Controllers;

use App\Models\BankTransaction;
use App\Models\Student;
use App\Services\MatchingEngine;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\BankStatementImport;

class BankTransactionController extends Controller
{
    protected $matchingEngine;

    public function __construct(MatchingEngine $matchingEngine)
    {
        $this->matchingEngine = $matchingEngine;
    }

    public function index()
    {
        $transactions = BankTransaction::with('student')
            ->orderBy('date', 'desc')
            ->get();
        
        $stats = [
            'match' => BankTransaction::where('statut', 'match')->count(),
            'doute' => BankTransaction::where('statut', 'doute')->count(),
            'non_trouve' => BankTransaction::where('statut', 'non_trouve')->count()
        ];
        
        return view('bank.index', compact('transactions', 'stats'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv'
        ]);

        Excel::import(new BankStatementImport, $request->file('file'));

        return redirect()->route('bank.index')
            ->with('success', 'Relevé bancaire importé avec succès');
    }

    public function runMatching()
    {
        $results = $this->matchingEngine->runAutomaticMatching();
        
        return redirect()->route('bank.index')
            ->with('success', "Rapprochement terminé: {$results['match']} matchés, {$results['doute']} douteux, {$results['non_trouve']} non trouvés");
    }

    public function manualMatch(Request $request, BankTransaction $transaction)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id'
        ]);

        $transaction->student_id = $request->student_id;
        $transaction->statut = 'match';
        $transaction->save();

        // Creer le paiement correspondant
        \App\Models\Payment::create([
            'student_id' => $transaction->student_id,
            'montant' => $transaction->montant,
            'date' => $transaction->date,
            'mode' => 'virement',
            'reference' => $transaction->reference,
            'nom_payeur' => $transaction->emetteur,
            'statut' => 'valide',
            'est_auto_match' => true
        ]);

        return redirect()->route('bank.index')
            ->with('success', 'Transaction associée manuellement');
    }
}