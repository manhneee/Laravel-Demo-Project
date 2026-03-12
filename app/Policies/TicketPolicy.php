<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TicketPolicy
{
    /**
     * Determine whether the user can view any tickets (list).
     * Actual list is scoped in controller: agents only see tickets assigned to them.
     */
    public function viewAny(): bool
    {
        Auth::user();
        return true;
    }

    /**
     * Determine whether the user can view the ticket.
     * Agent: tickets assigned to them OR created by them.
     */
    public function view(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isAgent()) {
            return (int) $ticket->assigned_user_id === (int) $user->id
                || (int) $ticket->created_by_id === (int) $user->id;
        }
        return (int) $ticket->created_by_id === (int) $user->id;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the ticket.
     * Agent: only their assigned tickets (assigned_user_id === user). Cannot assign others.
     */
    public function update(User $user, Ticket $ticket): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isAgent()) {

            // Agent can update their own tickets
            return (int) $ticket->assigned_user_id === (int) $user->id
                || (int) $ticket->created_by_id === (int) $user->id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the ticket.
     */
    public function delete(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can assign an agent to the ticket.
     * Only admins may change assigned_user_id.
     */
    public function assignAgent(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can add a comment (same as view).
     */
    public function comment(User $user, Ticket $ticket): bool
    {
        return $this->view($user, $ticket);
    }
}
