<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'nom', 'prenom', 'cin', 'code_unique', 'filiere', 'niveau',
        'annee', 'statut_paiement', 'montant_total', 'montant_paye',
        'reste_a_payer', 'email', 'telephone'
    ];

    protected $attributes = [
        'montant_total' => 0,
        'montant_paye' => 0,
        'reste_a_payer' => 0,
        'statut_paiement' => 'non_paye'
    ];

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function bankTransactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }

    public function getNomCompletAttribute(): string
    {
        return $this->prenom ? "{$this->prenom} {$this->nom}" : $this->nom;
    }

    public function updatePaymentStatus(): void
    {
        $totalPaye = $this->payments()->where('statut', 'valide')->sum('montant');
        $this->montant_paye = $totalPaye;
        $this->reste_a_payer = $this->montant_total - $totalPaye;

        if ($this->reste_a_payer <= 0) {
            $this->statut_paiement = 'paye';
        } elseif ($totalPaye > 0) {
            $this->statut_paiement = 'partiel';
        } else {
            $this->statut_paiement = 'non_paye';
        }

        $this->save();
    }

    public static function generateUniqueCode($niveau, $filiere, $annee): string
    {
        $lastStudent = self::where('code_unique', 'like', "ENSA-{$annee}-{$niveau}-{$filiere}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->code_unique, -2));
            $newNumber = str_pad($lastNumber + 1, 2, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '01';
        }

        return "ENSA-{$annee}-{$niveau}-{$filiere}{$newNumber}";
    }
}