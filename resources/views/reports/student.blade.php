<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Relevé de paiement - {{ $student->nom_complet }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        h1 { color: #1e40af; }
        .info { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; }
    </style>
</head>
<body>
    <h1>ENSA Berrechid - Relevé de paiement</h1>
    <div class="info">
        <p><strong>Étudiant :</strong> {{ $student->nom_complet }}</p>
        <p><strong>Code unique :</strong> {{ $student->code_unique }}</p>
        <p><strong>CIN :</strong> {{ $student->cin }}</p>
        <p><strong>Filière :</strong> {{ $student->filiere }} - {{ $student->niveau }}</p>
        <p><strong>Montant total :</strong> {{ number_format($student->montant_total, 2) }} MAD</p>
        <p><strong>Montant payé :</strong> {{ number_format($student->montant_paye, 2) }} MAD</p>
        <p><strong>Reste à payer :</strong> {{ number_format($student->reste_a_payer, 2) }} MAD</p>
    </div>

    <h3>Historique des paiements</h3>
    <table>
        <thead>
            <tr><th>Date</th><th>Montant</th><th>Mode</th><th>Référence</th></tr>
        </thead>
        <tbody>
            @foreach($student->payments as $p)
            <tr>
                <td>{{ $p->date->format('d/m/Y') }}</td>
                <td>{{ number_format($p->montant, 2) }} MAD</td>
                <td>{{ ucfirst($p->mode) }}</td>
                <td>{{ $p->reference }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>