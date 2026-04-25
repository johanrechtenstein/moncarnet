<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessage extends Mailable
{
    use Queueable, SerializesModels;

    public $data; // On stocke les infos ici

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
{
    // e() transforme les < en &lt;, donc le script devient du simple texte inoffensif
    $safeEmail = e($this->data['email']);
    $safeMessage = nl2br(e($this->data['message'])); // nl2br garde les retours à la ligne

    return $this->subject('Nouveau message de contact')
                ->html("<b>De :</b> $safeEmail <br><b>Message :</b><br> $safeMessage");
}

}
