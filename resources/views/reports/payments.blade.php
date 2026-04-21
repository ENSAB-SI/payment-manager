<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Rapport des paiements</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { color: #1e40af; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <h1>ENSA Berrechid - Rapport des paiements</h1>
    <p>Date : {{ date('d/m/Y H:i') }}</p>
    <table>
        <thead>
            <tr><th>Étudiant</th><th>Montant (MAD)</th><th>Date</th><th>Mode</th><th>Référence</th></tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
            <tr>
                <td>{{ $p->student?->nom_complet ?? 'Non associé' }}</td>
                <td>{{ number_format($p->montant, 2) }}</td>
                <td>{{ $p->date->format('d/m/Y') }}</td>
                <td>{{ ucfirst($p->mode) }}</td>
                <td>{{ $p->reference }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="total">Total des paiements : {{ number_format($total, 2) }} MAD</div>
</body>
</html>