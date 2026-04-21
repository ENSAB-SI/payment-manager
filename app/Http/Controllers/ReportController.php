<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function revenueReport()
    {
        $students = Student::with('payments')->get();
        $totalPayments = Payment::sum('montant');
        $pdf = Pdf::loadView('reports.revenue', compact('students', 'totalPayments'));
        return $pdf->download('rapport_encaissements_'.date('Y-m-d').'.pdf');
    }

    public function studentReport(Student $student)
    {
        $pdf = Pdf::loadView('reports.student', compact('student'));
        return $pdf->download('releve_'.$student->code_unique.'.pdf');
    }
}