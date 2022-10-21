<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MessageFromWebsiteForm extends Mailable
{
    use Queueable, SerializesModels;

    private array $requestData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('info@newgamer.lt', 'New Gamer - Public Form')->view('emails.contactUsMessage', $this->requestData);
    }
}
