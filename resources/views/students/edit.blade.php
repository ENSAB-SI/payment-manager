@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Modifier étudiant</h1>
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form method="POST" action="{{ route('students.update', $student) }}">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>Nom *</label>
                    <input type="text" name="nom" value="{{ $student->nom }}" required class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="{{ $student->prenom }}" class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label>CIN *</label>
                    <input type="text" name="cin" value="{{ $student->cin }}" required class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label>Filière *</label>
                    <select name="filiere" required class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="GE" {{ $student->filiere == 'GE' ? 'selected' : '' }}>GE</option>
                        <option value="GI" {{ $student->filiere == 'GI' ? 'selected' : '' }}>GI</option>
                        <option value="GC" {{ $student->filiere == 'GC' ? 'selected' : '' }}>GC</option>
                        <option value="SARS" {{ $student->filiere == 'SARS' ? 'selected' : '' }}>SARS</option>
                    </select>
                </div>
                <div>
                    <label>Niveau *</label>
                    <select name="niveau" required class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="L1" {{ $student->niveau == 'L1' ? 'selected' : '' }}>L1</option>
                        <option value="L2" {{ $student->niveau == 'L2' ? 'selected' : '' }}>L2</option>
                        <option value="L3" {{ $student->niveau == 'L3' ? 'selected' : '' }}>L3</option>
                        <option value="M1" {{ $student->niveau == 'M1' ? 'selected' : '' }}>M1</option>
                        <option value="M2" {{ $student->niveau == 'M2' ? 'selected' : '' }}>M2</option>
                    </select>
                </div>
                <div>
                    <label>Année *</label>
                    <input type="number" name="annee" value="{{ $student->annee }}" required>
                </div>
                <div>
                    <label>Montant total (MAD) *</label>
                    <input type="number" name="montant_total" step="0.01" value="{{ $student->montant_total }}" required>
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value="{{ $student->email }}">
                </div>
                <div>
                    <label>Téléphone</label>
                    <input type="text" name="telephone" value="{{ $student->telephone }}">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Mettre à jour</button>
                <a href="{{ route('students.index') }}" class="ml-2 bg-gray-300 px-4 py-2 rounded-lg">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection