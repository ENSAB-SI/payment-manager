@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Recherche avancée</h1>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Rechercher par nom, CIN, code unique, référence..." class="w-full p-3 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div id="results" class="mt-6 space-y-6"></div>
    </div>
</div>

<script>
document.getElementById('searchInput').addEventListener('input', function() {
    let q = this.value.trim();
    if(q.length < 2) {
        document.getElementById('results').innerHTML = '';
        return;
    }
    
    fetch(`/search/api?q=${encodeURIComponent(q)}`)
        .then(res => res.json())
        .then(data => {
            let html = '';
            
            // Étudiants - cliquable vers page d'édition
            if(data.students && data.students.length) {
                html += '<div class="bg-gray-50 p-4 rounded-lg"><h3 class="font-bold text-lg mb-3 text-blue-800">📚 Étudiants</h3><ul class="space-y-2">';
                data.students.forEach(s => {
                    html += `<li class="border-b pb-2">
                                <a href="/students/${s.id}/edit" class="block hover:bg-blue-100 p-2 rounded transition">
                                    <div class="font-semibold">${s.nom_complet} (${s.code_unique})</div>
                                    <div class="text-sm text-gray-600">CIN: ${s.cin} | Filière: ${s.filiere} | Statut: ${s.statut_paiement}</div>
                                </a>
                             </li>`;
                });
                html += '</ul></div>';
            }
            
            // Paiements - cliquable vers page d'édition
            if(data.payments && data.payments.length) {
                html += '<div class="bg-gray-50 p-4 rounded-lg"><h3 class="font-bold text-lg mb-3 text-green-800">💰 Paiements</h3><ul class="space-y-2">';
                data.payments.forEach(p => {
                    html += `<li class="border-b pb-2">
                                <a href="/payments/${p.id}/edit" class="block hover:bg-green-100 p-2 rounded transition">
                                    <div class="font-semibold">${p.reference} - ${p.montant} MAD</div>
                                    <div class="text-sm text-gray-600">Étudiant: ${p.student?.nom_complet || 'Non associé'} | Date: ${new Date(p.date).toLocaleDateString('fr-FR')}</div>
                                </a>
                             </li>`;
                });
                html += '</ul></div>';
            }
            
            // Transactions bancaires - cliquable vers page des relevés
            if(data.transactions && data.transactions.length) {
                html += '<div class="bg-gray-50 p-4 rounded-lg"><h3 class="font-bold text-lg mb-3 text-purple-800">🏦 Transactions bancaires</h3><ul class="space-y-2">';
                data.transactions.forEach(t => {
                    html += `<li class="border-b pb-2">
                                <a href="/bank" class="block hover:bg-purple-100 p-2 rounded transition">
                                    <div class="font-semibold">${t.emetteur} - ${t.montant} MAD</div>
                                    <div class="text-sm text-gray-600">Référence: ${t.reference} | Date: ${new Date(t.date).toLocaleDateString('fr-FR')}</div>
                                </a>
                             </li>`;
                });
                html += '</ul></div>';
            }
            
            if (!html) {
                html = '<p class="text-gray-500 text-center py-8">Aucun résultat trouvé</p>';
            }
            
            document.getElementById('results').innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            document.getElementById('results').innerHTML = '<p class="text-red-500">Erreur lors de la recherche</p>';
        });
});
</script>
@endsection