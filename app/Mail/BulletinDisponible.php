<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BulletinDisponible extends Mailable
{
    use Queueable, SerializesModels;

    public $eleve;
    public $periode;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($eleve, $periode)
    {
        $this->eleve = $eleve;
        $this->periode = $periode;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Nouveau bulletin disponible - ' . $this->periode)
                    ->view('emails.bulletin-disponible');
    }
} 