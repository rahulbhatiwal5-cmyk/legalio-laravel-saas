<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailRecoveryPassword;


class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
    public string $resetLink;
    /**
     * Create a new message instance.
     */
    public function __construct($resetLink)
    {
        $this->resetLink = $resetLink;
    }

    public function build()
    {
        // Fetch the email template
        $template = EmailRecoveryPassword::first(); // Get the first template (or you can filter based on specific logic)
        // dd($template);
        if (!$template) {
            throw new \Exception("Email template not found.");
        }

        return $this->view('emails.reset_password')
            // ->subject($template->subject)
            ->with([
                'subject'=>$template->subject,
                'heading' => $template->heading,
                'body' => $template->body,
                'buttonText' => $template->button_text,
                'footer' => $template->footer,
                'resetLink' => $this->resetLink
            ]);
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Password Mail',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
