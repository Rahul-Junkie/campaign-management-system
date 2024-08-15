<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $username;
    public $emailBody;
    public $subject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($username, $emailBody, $subject)
    {
        $this->username = $username;
        $this->emailBody = $emailBody;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // Replace {{username}} with the actual username
        $emailContent = str_replace('{{username}}', $this->username, $this->emailBody);

        return $this->subject($this->subject) // Add subject here
            ->html($emailContent);
    }
}
