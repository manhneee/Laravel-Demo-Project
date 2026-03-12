<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Admin dashboard: global ticket counts by status, user counts, recent activity.
     */
    public function index()
    {
        $byStatus = Ticket::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statuses = ['open', 'in_progress', 'pending', 'resolved', 'closed'];
        $counts = [];
        foreach ($statuses as $s) {
            $counts[$s] = (int) ($byStatus[$s] ?? 0);
        }
        $counts['total'] = array_sum($counts);

        $usersTotal = User::count();
        $recentTickets = Ticket::with(['priority', 'createdBy', 'assignedUser'])
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'counts_by_status' => $counts,
            'users_total' => $usersTotal,
            'recent_tickets' => $recentTickets,
        ]);
    }
}
