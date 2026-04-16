@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Enregistrer un paiement</h1>
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form method="POST" action="{{ route('payments.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label>Étudiant (optionnel)</label>
                    <select name="student_id" class="mt-1 block w-full border-gray-300 rounded-md">
                        <option value="">-- Sélectionner un étudiant --</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->nom_complet }} ({{ $student->code_unique }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Montant (MAD) *</label>
                    <input type="number" name="montant" step="0.01" required class="mt-1 block w-full border-gray-300 rounded-md">
                </div>
                <div>
                    <label>Date *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required>
                </div>
                <div>
                    <label>Mode *</label>
                    <select name="mode" required>
                        <option value="virement">Virement</option>
                        <option value="espece">Espèce</option>
                        <option value="cheque">Chèque</option>
                        <option value="carte">Carte</option>
                    </select>
                </div>
                <div>
                    <label>Référence *</label>
                    <input type="text" name="reference" required>
                </div>
                <div>
                    <label>Nom du payeur (si différent)</label>
                    <input type="text" name="nom_payeur">
                </div>
                <div>
                    <label>Reçu (PDF/Image)</label>
                    <input type="file" name="recu" accept=".jpg,.jpeg,.png,.pdf">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Enregistrer</button>
                <a href="{{ route('payments.index') }}" class="ml-2 bg-gray-300 px-4 py-2 rounded-lg">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection