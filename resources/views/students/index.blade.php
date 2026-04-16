@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Étudiants</h1>
        <div class="flex gap-3">
            <a href="{{ route('students.export') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Export CSV
            </a>
            <a href="{{ route('students.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                + Nouvel étudiant
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prénom</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CIN</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code unique</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Filière</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Niveau</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant payé</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reste</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ $student->nom }}</td>
                    <td class="px-6 py-4">{{ $student->prenom }}</td>
                    <td class="px-6 py-4">{{ $student->cin }}</td>
                    <td class="px-6 py-4">{{ $student->code_unique }}</td>
                    <td class="px-6 py-4">{{ $student->filiere }}</td>
                    <td class="px-6 py-4">{{ $student->niveau }}</td>
                    <td class="px-6 py-4">{{ number_format($student->montant_total, 0, ',', ' ') }} MAD</td>
                    <td class="px-6 py-4">{{ number_format($student->montant_paye, 0, ',', ' ') }} MAD</td>
                    <td class="px-6 py-4">{{ number_format($student->reste_a_payer, 0, ',', ' ') }} MAD</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($student->statut_paiement == 'paye') bg-green-100 text-green-800
                            @elseif($student->statut_paiement == 'partiel') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($student->statut_paiement) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('students.edit', $student) }}" class="text-blue-600 hover:text-blue-900 mr-3">Modifier</a>
                        <form action="{{ route('students.destroy', $student) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Supprimer cet étudiant ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    @if(count($students) == 0)
    <div class="text-center py-12 bg-white rounded-lg shadow mt-6">
        <p class="text-gray-500">Aucun étudiant enregistré</p>
        <a href="{{ route('students.create') }}" class="text-blue-600 hover:text-blue-800 mt-2 inline-block">Ajouter un étudiant</a>
    </div>
    @endif
</div>
@endsection