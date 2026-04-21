@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Tableau de bord</h1>
    
    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total étudiants</p>
                    <p class="text-3xl font-bold">{{ $totalEtudiants }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Total encaissé</p>
                    <p class="text-3xl font-bold text-green-600">{{ number_format($totalEncaisse, 0, ',', ' ') }} MAD</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Reste à payer</p>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($resteAPayer, 0, ',', ' ') }} MAD</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Non identifiés</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $nonIdentifies }}</p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Graphiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-700 mb-4">Encaissements par filière</h3>
            @if(count($encaissementsFiliere) > 0)
                <div class="space-y-3">
                    @foreach($encaissementsFiliere as $item)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>{{ $item->filiere }}</span>
                            <span class="font-semibold">{{ number_format($item->total, 0, ',', ' ') }} MAD</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $max = $encaissementsFiliere->max('total');
                                $percentage = $max > 0 ? ($item->total / $max) * 100 : 0;
                            @endphp
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucun encaissement enregistré</p>
            @endif
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-bold text-gray-700 mb-4">Statuts de paiement</h3>
            @if(count($statuts) > 0)
                <div class="space-y-3">
                    @foreach($statuts as $statut)
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span>
                                <span class="inline-block w-3 h-3 rounded-full 
                                    @if($statut->statut_paiement == 'paye') bg-green-500
                                    @elseif($statut->statut_paiement == 'partiel') bg-yellow-500
                                    @else bg-red-500 @endif">
                                </span>
                                {{ ucfirst($statut->statut_paiement) }}
                            </span>
                            <span class="font-semibold">{{ $statut->total }} étudiants</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            @php
                                $total = $statuts->sum('total');
                                $percentage = $total > 0 ? ($statut->total / $total) * 100 : 0;
                            @endphp
                            <div class="h-2 rounded-full 
                                @if($statut->statut_paiement == 'paye') bg-green-500
                                @elseif($statut->statut_paiement == 'partiel') bg-yellow-500
                                @else bg-red-500 @endif" 
                                style="width: {{ $percentage }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Aucun étudiant enregistré</p>
            @endif
        </div>
    </div>
    
    <!-- Derniers paiements -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-bold text-gray-700 mb-4">Derniers paiements</h3>
        @if(isset($paiementsRecents) && count($paiementsRecents) > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Étudiant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($paiementsRecents as $paiement)
                        <tr>
                            <td class="px-6 py-4">{{ $paiement->student?->nom_complet ?? 'Non associé' }}</td>
                            <td class="px-6 py-4 font-semibold text-green-600">{{ number_format($paiement->montant, 0, ',', ' ') }} MAD</td>
                            <td class="px-6 py-4">{{ $paiement->date->format('d/m/Y') }}</td>
                            <td class="px-6 py-4">{{ ucfirst($paiement->mode) }}</td>
                            <td class="px-6 py-4">{{ $paiement->reference }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <div class="bg-white p-4 rounded shadow">
        <canvas id="filiereChart" height="200"></canvas>
    </div>
    <div class="bg-white p-4 rounded shadow">
        <canvas id="statusChart" height="200"></canvas>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filiereCtx = document.getElementById('filiereChart').getContext('2d');
    const filiereData = @json($encaissementsFiliere);
    new Chart(filiereCtx, {
        type: 'bar',
        data: {
            labels: filiereData.map(i => i.filiere),
            datasets: [{
                label: 'Encaissements (MAD)',
                data: filiereData.map(i => i.total),
                backgroundColor: 'rgba(54, 162, 235, 0.5)'
            }]
        }
    });

    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusData = @json($statuts);
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: statusData.map(s => s.statut_paiement),
            datasets: [{
                data: statusData.map(s => s.total),
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444']
            }]
        }
    });
});
</script>
        @else
            <p class="text-gray-500 text-center py-8">Aucun paiement enregistré</p>
        @endif
    </div>
</div>
@endsection