<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketAttachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function index(Ticket $ticket)
    {
        $attachments = $ticket->attachments()->orderBy('created_at')->get();
        return response()->json($attachments);
    }

    public function store(Request $request, Ticket $ticket)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
        $assignment = $ticket->assignments()->create($request->only(['user_id']));
        return response()->json($assignment, 201);
    }
    public function destroy(TicketAttachment $attachment)
    {
        $attachment->delete();
        return response()->json(['message' => 'Attachment deleted successfully'], 200);
    }

}
