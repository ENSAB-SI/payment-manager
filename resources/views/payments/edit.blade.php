@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Modifier paiement</h1>
    <div class="bg-white rounded-lg shadow p-6 max-w-2xl">
        <form method="POST" action="{{ route('payments.update', $payment) }}">
            @csrf @method('PUT')
            <div class="space-y-4">
                <div>
                    <label>Étudiant</label>
                    <select name="student_id">
                        <option value="">-- Non associé --</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $payment->student_id == $student->id ? 'selected' : '' }}>{{ $student->nom_complet }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Montant (MAD)</label>
                    <input type="number" name="montant" step="0.01" value="{{ $payment->montant }}" required>
                </div>
                <div>
                    <label>Date</label>
                    <input type="date" name="date" value="{{ $payment->date->format('Y-m-d') }}" required>
                </div>
                <div>
                    <label>Mode</label>
                    <select name="mode" required>
                        <option value="virement" {{ $payment->mode == 'virement' ? 'selected' : '' }}>Virement</option>
                        <option value="espece" {{ $payment->mode == 'espece' ? 'selected' : '' }}>Espèce</option>
                        <option value="cheque" {{ $payment->mode == 'cheque' ? 'selected' : '' }}>Chèque</option>
                        <option value="carte" {{ $payment->mode == 'carte' ? 'selected' : '' }}>Carte</option>
                    </select>
                </div>
                <div>
                    <label>Référence</label>
                    <input type="text" name="reference" value="{{ $payment->reference }}" required>
                </div>
                <div>
                    <label>Nom du payeur</label>
                    <input type="text" name="nom_payeur" value="{{ $payment->nom_payeur }}">
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Mettre à jour</button>
                <a href="{{ route('payments.index') }}" class="ml-2 bg-gray-300 px-4 py-2 rounded-lg">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection