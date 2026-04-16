<?php

namespace App\Imports;

use App\Models\BankTransaction;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BankStatementImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new BankTransaction([
            'emetteur' => $row['emetteur'] ?? $row['nom'] ?? '',
            'montant' => $row['montant'] ?? 0,
            'date' => $row['date'] ?? now(),
            'reference' => $row['reference'] ?? uniqid(),
            'statut' => 'non_trouve'
        ]);
    }
}