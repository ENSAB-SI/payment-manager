<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Payment;
use App\Models\BankTransaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalEtudiants = Student::count();
        $totalEncaisse = Payment::where('statut', 'valide')->sum('montant');
        $totalFrais = Student::sum('montant_total');
        $resteAPayer = $totalFrais - $totalEncaisse;
        $nonIdentifies = BankTransaction::where('statut', 'non_trouve')->count();

        // Encaissements par filiere
        $encaissementsFiliere = Payment::where('statut', 'valide')
            ->join('students', 'payments.student_id', '=', 'students.id')
            ->select('students.filiere', DB::raw('SUM(payments.montant) as total'))
            ->groupBy('students.filiere')
            ->get();

        // Statuts de paiement
        $statuts = Student::select('statut_paiement', DB::raw('count(*) as total'))
            ->groupBy('statut_paiement')
            ->get();

        // Paiements recents
        $paiementsRecents = Payment::with('student')
            ->where('statut', 'valide')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'totalEtudiants', 'totalEncaisse', 'resteAPayer', 'nonIdentifies',
            'encaissementsFiliere', 'statuts', 'paiementsRecents'
        ));
    }
}