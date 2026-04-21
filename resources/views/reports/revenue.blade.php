<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport des encaissements</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { color: #1e40af; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
        .total { font-size: 18px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <h1>ENSA Berrechid - Rapport des encaissements</h1>
    <p>Date : {{ date('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>Étudiant</th><th>Code unique</th><th>Filière</th><th>Total payé (MAD)</th><th>Reste (MAD)</th><th>Statut</th></tr>
        </thead>
        <tbody>
            @foreach($students as $s)
            <tr>
                <td>{{ $s->nom_complet }}</td>
                <td>{{ $s->code_unique }}</td>
                <td>{{ $s->filiere }}</td>
                <td>{{ number_format($s->montant_paye, 2) }}</td>
                <td>{{ number_format($s->reste_a_payer, 2) }}</td>
                <td>{{ ucfirst($s->statut_paiement) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total">Total encaissé : {{ number_format($totalPayments, 2) }} MAD</div>
</body>
</html>