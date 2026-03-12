<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Mail\NewTicketCreated;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Ticket::class, 'ticket');
    }

    public function index()
    {
        $user = Auth::user();
        $tickets = Ticket::query()
            ->forUser($user)
            ->with(['priority', 'createdBy', 'assignedUser', 'labels', 'categories'])
            ->latest()
            ->get();

        return response()->json($tickets);
    }

    public function store(Request $request)
    {

        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'priority_id' => 'required|exists:priorities,id',
            'status' => 'required|in:open,in_progress,pending,resolved,closed',
        ];

        //Admin can assign user to ticket
        if (Auth::user()->isAdmin()) {
            $rules['assigned_user_id'] = 'nullable|exists:users,id';
        }
        $request->validate($rules);

        $data = $request->only(['title', 'description', 'priority_id', 'status']);
        $data['created_by_id'] = Auth::id();
        if (Auth::user()->isAdmin() && $request->filled('assigned_user_id')) {
            $data['assigned_user_id'] = $request->assigned_user_id;
        }

        $oldValues = null;
        $ticket = Ticket::create($data);
        
        
        if ($request->filled('labels')) {
            $ticket->labels()->sync($request->labels);
        }
        if ($request->filled('categories')) {
            $ticket->categories()->sync($request->categories);
        }
        $newValues = $ticket->only(['title', 'description', 'priority_id', 'status', 'assigned_user_id']);
        TicketActivityLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'created',
            'description' => 'Ticket created',
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Mail::to($admin->email)->send(new NewTicketCreated($ticket));
        }

        return response()->json($ticket->load(['priority', 'labels', 'categories', 'createdBy', 'assignedUser']), 201);
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['priority', 'labels', 'categories', 'createdBy', 'assignedUser', 'comments.user']);
        return response()->json($ticket);
    }

    public function storeComment(Request $request, Ticket $ticket)
    {
        $this->authorize('comment', $ticket);
        $request->validate(['body' => 'required|string|max:5000']);
        $comment = $ticket->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);
        $comment->load('user');
        return response()->json($comment, 201);
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'priority_id' => 'required|exists:priorities,id',
            'status' => 'required|in:open,in_progress,pending,resolved,closed',
        ];
        if ($user->isAdmin()) {
            $rules['assigned_user_id'] = 'nullable|exists:users,id';
        }
        $request->validate($rules);

        $data = $request->only(['title', 'description', 'priority_id', 'status']);

        if ($user->isAdmin() && $request->has('assigned_user_id')) {
            $this->authorize('assignAgent', $ticket);
            $data['assigned_user_id'] = $request->assigned_user_id;
        }

        $oldValues = $ticket->only(['title', 'description', 'priority_id', 'status', 'assigned_user_id']);

        $ticket->update($data);
        $newValues = $ticket->only(['title', 'description', 'priority_id', 'status', 'assigned_user_id']);
        if ($oldValues !== $newValues) {
            TicketActivityLog::create([
                'ticket_id' => $ticket->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'description' => 'Ticket updated',
                'old_values' => $oldValues,
                'new_values' => $newValues,
            ]);
        }
        if ($request->has('labels')) {
            $ticket->labels()->sync($request->labels ?? []);
        }
        if ($request->has('categories')) {
            $ticket->categories()->sync($request->categories ?? []);
        }
        return response()->json($ticket->fresh(['priority', 'labels', 'categories', 'createdBy', 'assignedUser']));
    }

    public function destroy(Ticket $ticket)
    {
        $oldValues = $ticket->only(['title', 'description', 'priority_id', 'status', 'assigned_user_id']);
       
        TicketActivityLog::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'action' => 'deleted',
            'description' => 'Ticket deleted',
            'old_values' => $oldValues,
            'new_values' => null,
        ]);
        $ticket->delete();
        return response()->json([
            'message' => 'Ticket deleted successfully',
        ], 200);
    }

    public function ticketLogs(Ticket $ticket)
    {
        $logs = $ticket->activityLogs()->orderBy('created_at')->get();
        return response()->json($logs);
    }

    public function ticketComments(Ticket $ticket)
    {
        $comments = $ticket->comments()->orderBy('created_at')->get();
        return response()->json($comments);
    }
    
    
}
