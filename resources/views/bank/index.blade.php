@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Rapprochement bancaire</h1>

    {{-- Statistiques --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-green-100 p-4 rounded-lg text-center"><strong class="text-green-800">Match</strong><br>{{ $stats['match'] }}</div>
        <div class="bg-yellow-100 p-4 rounded-lg text-center"><strong class="text-yellow-800">Doute</strong><br>{{ $stats['doute'] }}</div>
        <div class="bg-red-100 p-4 rounded-lg text-center"><strong class="text-red-800">Non trouvé</strong><br>{{ $stats['non_trouve'] }}</div>
    </div>

    {{-- Import --}}
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <form action="{{ route('bank.import') }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-4">
            @csrf
            <div class="flex-1">
                <label>Importer relevé (Excel/CSV)</label>
                <input type="file" name="file" accept=".xlsx,.csv" required class="block w-full border p-2">
            </div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Importer</button>
        </form>
    </div>

    {{-- Lancer matching automatique --}}
    <div class="mb-6">
        <form action="{{ route('bank.run-matching') }}" method="POST">
            @csrf
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">Lancer le rapprochement automatique</button>
        </form>
    </div>

    {{-- Liste des transactions --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y">
            <thead class="bg-gray-50">
                <tr><th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Émetteur</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Association</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $tx)
                <tr class="border-b">
                    <td class="px-6 py-4">{{ $tx->emetteur }}</td>
                    <td class="px-6 py-4">{{ number_format($tx->montant, 0, ',', ' ') }} MAD</td>
                    <td class="px-6 py-4">{{ $tx->date->format('d/m/Y') }}</td>
                    <td class="px-6 py-4">{{ $tx->reference }}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($tx->statut == 'match') bg-green-100 text-green-800
                            @elseif($tx->statut == 'doute') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ strtoupper($tx->statut) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($tx->statut != 'match')
                        <form action="{{ route('bank.manual-match', $tx) }}" method="POST" class="flex gap-2">
                            @csrf
                            <select name="student_id" required class="border rounded p-1">
                                <option value="">-- Associer --</option>
                                @foreach(\App\Models\Student::all() as $student)
                                <option value="{{ $student->id }}">{{ $student->nom_complet }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded text-sm">Associer</button>
                        </form>
                        @else
                        {{ $tx->student?->nom_complet ?? '—' }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection