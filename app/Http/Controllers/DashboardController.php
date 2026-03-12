<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * User/Agent dashboard: my tickets by status + recent tickets.
     */
    public function index()
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $baseQuery = Ticket::query()
            ->forUser($user)
            ->with(['priority', 'createdBy', 'assignedUser', 'labels', 'categories']);

        $collection = (clone $baseQuery)->get();
        $byStatus = $collection->groupBy('status')->map->count()->all();
        $recent = (clone $baseQuery)->latest()->take(10)->get();
        $total = array_sum($byStatus);

        return response()->json([
            'tickets_by_status' => $byStatus,
            'total' => $total,
            'recent_tickets' => $recent,
        ]);
    }
}
