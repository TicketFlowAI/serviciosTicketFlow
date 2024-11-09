<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractEndingNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $serviceData;
    protected $viewTemplate;
    protected $subjectLine;

    /**
     * Create a new message instance.
     *
     * @param array $serviceData Data related to the service
     * @param string $viewTemplate View template name
     * @param string $subjectLine Dynamic subject
     */
    public function __construct($serviceData, $viewTemplate, $subjectLine)
    {
        $this->serviceData = $serviceData;
        $this->viewTemplate = $viewTemplate;
        $this->subjectLine = $subjectLine;
    }

    /**
     * Get the message envelope with dynamic subject.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition with dynamic view.
     */
    public function content(): Content
    {
        return new Content(
            view: $this->viewTemplate,
            with: ['serviceData' => $this->serviceData], // Pass data to view
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
