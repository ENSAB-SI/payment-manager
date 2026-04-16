@extends('layouts.app')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold mb-6">Recherche avancée</h1>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="mb-4">
            <input type="text" id="searchInput" placeholder="Rechercher par nom, CIN, code unique, référence..." class="w-full p-3 border border-gray-300 rounded-lg">
        </div>
        <div id="results" class="mt-6"></div>
    </div>
</div>

<script>
    document.getElementById('searchInput').addEventListener('input', function() {
        let q = this.value;
        if(q.length < 2) return;
        fetch(`/search/api?q=${encodeURIComponent(q)}`)
            .then(res => res.json())
            .then(data => {
                let html = '';
                if(data.students.length) {
                    html += '<h3 class="font-bold mt-4">Étudiants</h3><ul>';
                    data.students.forEach(s => { html += `<li>${s.nom_complet} (${s.code_unique}) - ${s.filiere}</li>`; });
                    html += '</ul>';
                }
                if(data.payments.length) {
                    html += '<h3 class="font-bold mt-4">Paiements</h3><ul>';
                    data.payments.forEach(p => { html += `<li>${p.reference} - ${p.montant} MAD - ${p.student?.nom_complet || 'non associé'}</li>`; });
                    html += '</ul>';
                }
                if(data.transactions.length) {
                    html += '<h3 class="font-bold mt-4">Transactions bancaires</h3><ul>';
                    data.transactions.forEach(t => { html += `<li>${t.emetteur} - ${t.montant} MAD - ${t.reference}</li>`; });
                    html += '</ul>';
                }
                document.getElementById('results').innerHTML = html || '<p class="text-gray-500">Aucun résultat</p>';
            });
    });
</script>
@endsection