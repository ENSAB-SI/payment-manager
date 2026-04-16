<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Payment;
use App\Models\BankTransaction;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index()
    {
        return view('search.index');
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $students = Student::where('nom', 'LIKE', "%{$query}%")
            ->orWhere('prenom', 'LIKE', "%{$query}%")
            ->orWhere('cin', 'LIKE', "%{$query}%")
            ->orWhere('code_unique', 'LIKE', "%{$query}%")
            ->get();
        
        $payments = Payment::with('student')
            ->where('reference', 'LIKE', "%{$query}%")
            ->orWhere('nom_payeur', 'LIKE', "%{$query}%")
            ->get();
        
        $transactions = BankTransaction::where('emetteur', 'LIKE', "%{$query}%")
            ->orWhere('reference', 'LIKE', "%{$query}%")
            ->get();
        
        return response()->json([
            'students' => $students,
            'payments' => $payments,
            'transactions' => $transactions
        ]);
    }
}