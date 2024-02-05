<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class Email extends Mailable
{
    use Queueable, SerializesModels;

    public $attachments;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($content)
    {
        $this->attachments = [];

        $this->documents = $content['documents'];
        $this->content = $content;

        foreach($this->documents as $document) {
            $file = Storage::drive('s3')->temporaryUrl($document->file, now()->addMinute());
            $this->attachments[] = [
                'file' => $file,
                'options' => [
                    'as' => $document->title,
                ],
            ];
        }
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            from: new Address($this->content['from_address'], 'Application Name'),
            replyTo: [
                new Address("address@domain.com", 'Application Name'),
            ],
            subject: $this->content['subject'],
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mail =  $this->markdown('system.emails.generic-email')
            ->from($this->content['from_address'])
            ->to($this->content['to_address'])
            ->cc($this->content['cc_address'])
            ->bcc($this->content['bcc_address'])
            ->subject($this->content['subject'])
            ->with('content', $this->content['body']);

        foreach($this->attachments as $attachment) {
            $mail->attach($attachment['file']);
        }

        return $mail;
    }
}
