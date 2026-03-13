<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketWeeklyStatus extends Mailable
{
    use Queueable, SerializesModels;

    public $ticketsByStatus;

    public $statusOrder;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        $this->statusOrder = ['open', 'in_progress', 'pending', 'resolved', 'closed'];
        $tickets = Ticket::query()
            ->with(['priority', 'createdBy', 'assignedUser'])
            ->orderBy('status')
            ->orderByDesc('updated_at')
            ->get();
        $this->ticketsByStatus = $tickets->groupBy('status');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Ticket status - all tickets by status',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket-weekly-status',
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
