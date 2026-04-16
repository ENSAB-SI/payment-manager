<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'LIKE', "%{$search}%")
                  ->orWhere('prenom', 'LIKE', "%{$search}%")
                  ->orWhere('cin', 'LIKE', "%{$search}%")
                  ->orWhere('code_unique', 'LIKE', "%{$search}%");
            });
        }

        $students = $query->orderBy('created_at', 'desc')->get();

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'cin' => 'required|string|unique:students',
            'filiere' => 'required|string',
            'niveau' => 'required|string',
            'annee' => 'required|integer',
            'montant_total' => 'required|numeric|min:0',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string'
        ]);

        $validated['code_unique'] = Student::generateUniqueCode(
            $validated['niveau'],
            $validated['filiere'],
            $validated['annee']
        );

        $validated['reste_a_payer'] = $validated['montant_total'];

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Étudiant ajouté avec succès');
    }

    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'cin' => 'required|string|unique:students,cin,' . $student->id,
            'filiere' => 'required|string',
            'niveau' => 'required|string',
            'annee' => 'required|integer',
            'montant_total' => 'required|numeric|min:0',
            'email' => 'nullable|email',
            'telephone' => 'nullable|string'
        ]);

        $student->update($validated);
        $student->updatePaymentStatus();

        return redirect()->route('students.index')
            ->with('success', 'Étudiant modifié avec succès');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')
            ->with('success', 'Étudiant supprimé avec succès');
    }

    public function search(Request $request)
    {
        $query = Student::query();
        
        if ($request->q) {
            $query->where('nom', 'LIKE', "%{$request->q}%")
                  ->orWhere('prenom', 'LIKE', "%{$request->q}%")
                  ->orWhere('cin', 'LIKE', "%{$request->q}%")
                  ->orWhere('code_unique', 'LIKE', "%{$request->q}%");
        }
        
        return response()->json($query->limit(10)->get());
    }
    public function export()
{
    $students = Student::all();
    
    $filename = "etudiants_" . date('Y-m-d') . ".csv";
    
    $handle = fopen('php://temp', 'w+');
    
    // Ajouter l'en-tête UTF-8
    fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // En-têtes des colonnes
    fputcsv($handle, [
        'ID', 'Nom', 'Prénom', 'CIN', 'Code unique', 'Filière', 'Niveau',
        'Année', 'Montant total (MAD)', 'Montant payé (MAD)', 'Reste à payer (MAD)',
        'Statut', 'Email', 'Téléphone', 'Date d\'inscription'
    ], ';');
    
    // Données
    foreach($students as $student) {
        fputcsv($handle, [
            $student->id,
            $student->nom,
            $student->prenom,
            $student->cin,
            $student->code_unique,
            $student->filiere,
            $student->niveau,
            $student->annee,
            number_format($student->montant_total, 2, ',', ' '),
            number_format($student->montant_paye, 2, ',', ' '),
            number_format($student->reste_a_payer, 2, ',', ' '),
            $student->statut_paiement == 'paye' ? 'Payé' : ($student->statut_paiement == 'partiel' ? 'Partiel' : 'Non payé'),
            $student->email,
            $student->telephone,
            $student->created_at->format('d/m/Y H:i')
        ], ';');
    }
    
    rewind($handle);
    $csvContent = stream_get_contents($handle);
    fclose($handle);
    
    return response($csvContent, 200, [
        'Content-Type' => 'text/csv; charset=UTF-8',
        'Content-Disposition' => 'attachment; filename="' . $filename . '"',
    ]);
}
}