<?php

namespace App\Mail;

use App\Models\Solicitud;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudStatusChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $solicitud;

    /**
     * Create a new message instance.
     */
    public function __construct(Solicitud $solicitud)
    {
        $this->solicitud = $solicitud;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this
            ->subject("Tu solicitud #{$this->solicitud->id} cambió a “{$this->solicitud->estado}”")
            ->view('emails.solicitud_status_changed');
    }
}
