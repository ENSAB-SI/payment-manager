<?php

namespace App\Notifications;

use App\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceived extends Notification
{
    use Queueable;

    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Paiement enregistré - ENSA Berrechid')
            ->greeting('Bonjour ' . ($notifiable->nom_complet ?? 'Cher étudiant'))
            ->line('Un paiement de **' . number_format($this->payment->montant, 2) . ' MAD** a été enregistré.')
            ->line('Référence : ' . $this->payment->reference)
            ->line('Date : ' . $this->payment->date->format('d/m/Y'))
            ->action('Voir mes paiements', url('/payments'))
            ->line('Merci de votre confiance.');
    }
}