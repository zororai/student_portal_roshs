<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\StudentApplication;

class ApplicationStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $recipientType;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(StudentApplication $application, $recipientType = 'guardian')
    {
        $this->application = $application;
        $this->recipientType = $recipientType;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $status = ucfirst($this->application->status);
        $subject = "Application Status Update - {$status}";
        
        return $this->subject($subject)
                    ->view('emails.application-status');
    }
}
