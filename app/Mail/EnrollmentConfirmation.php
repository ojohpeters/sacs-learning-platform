<?php

namespace App\Mail;

use App\Models\Enrollment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enrollment $enrollment) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You are enrolled in '.$this->enrollment->course->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.enrollment-confirmation',
        );
    }
}
