<?php

namespace App\Notifications;

use App\Models\Nota;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotaRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Nota $nota)
    {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Nota Ditolak - Perlu Revisi')
            ->line("Nota Anda telah ditolak oleh approver.")
            ->line("Nomor Nota: {$this->nota->nomor_nota ?? 'Digital'}")
            ->line("Tanggal: {$this->nota->tanggal_nota->format('d/m/Y')}")
            ->line("Alasan: {$this->nota->catatan_approver}")
            ->action('Lihat Nota', route('nota.show', $this->nota))
            ->line('Silakan revisi dan submit kembali.');
    }

    public function toArray($notifiable)
    {
        return [
            'nota_id' => $this->nota->id,
            'nomor_nota' => $this->nota->nomor_nota,
            'message' => 'Nota Anda ditolak. Alasan: ' . $this->nota->catatan_approver,
        ];
    }
}
