<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'student_id', 'montant', 'date', 'mode', 'reference',
        'recu_path', 'ocr_text', 'nom_payeur', 'statut', 'est_auto_match'
    ];

    protected $casts = [
        'date' => 'date',
        'est_auto_match' => 'boolean'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    protected static function booted()
    {
        static::created(function ($payment) {
            if ($payment->student_id && $payment->statut === 'valide') {
                $payment->student->updatePaymentStatus();
            }
        });

        static::updated(function ($payment) {
            if ($payment->student_id && $payment->statut === 'valide') {
                $payment->student->updatePaymentStatus();
            }
        });
    }
}