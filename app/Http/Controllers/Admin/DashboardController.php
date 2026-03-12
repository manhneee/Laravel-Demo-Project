<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Label;
use App\Models\Priority;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Admin dashboard: system-wide ticket stats, entity counts, recent tickets.
     */
    public function index()
    {
        $byStatus = Ticket::query()
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->all();

        $recent = Ticket::query()
            ->with(['priority', 'createdBy', 'assignedUser', 'labels', 'categories'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'tickets_by_status' => $byStatus,
            'total_tickets' => array_sum($byStatus),
            'total_users' => User::query()->count(),
            'total_categories' => Category::query()->count(),
            'total_labels' => Label::query()->count(),
            'recent_tickets' => $recent,
        ]);
    }
}
