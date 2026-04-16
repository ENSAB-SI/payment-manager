@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Paiements</h1>
        <a href="{{ route('payments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">+ Nouveau paiement</a>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ $payment->student?->nom_complet ?? 'Non associé' }}</td>
                    <td class="px-6 py-4">{{ number_format($payment->montant, 0, ',', ' ') }} MAD</td>
                    <td class="px-6 py-4">{{ $payment->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ ucfirst($payment->mode) }}</td>
                    <td class="px-6 py-4">{{ $payment->reference }}</td>
                    <td class="px-6 py-4"><span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">{{ $payment->statut }}</span></td>
                    <td class="px-6 py-4">
                        <a href="{{ route('payments.edit', $payment) }}" class="text-blue-600 mr-3">Modifier</a>
                        <form action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-600" onclick="return confirm('Supprimer ce paiement ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection