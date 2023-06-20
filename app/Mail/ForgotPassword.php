<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPassword extends Mailable
{
    use Queueable, SerializesModels;

    private $_code;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $code)
    {
        $this->_code = $code;
        $this->subject = __('auth.email_subject_forgot_password');
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): ForgotPassword
    {
        return $this->view('mails.forgot')->with('code', $this->_code);
    }
}
