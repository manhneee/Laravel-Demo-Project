<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketActivityLog;
use Illuminate\Http\Request;

class LogsController extends Controller
{
    public function index()
    {
        $logs = TicketActivityLog::query()
            ->with(['ticket:id,title', 'user:id,name,email'])
            ->latest()
            ->limit(200)
            ->get();

        return response()->json($logs);
    }
    public function create(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'user_id' => 'required|exists:users,id',
            'action' => 'required|string|max:50',
            'description' => 'nullable|string|max:5000',
            'old_values' => 'nullable|json',
            'new_values' => 'nullable|json',
        ]);
        $log = TicketActivityLog::create($request->all());
        return response()->json($log, 201);
    }
    public function delete($id)
    {
        $log = TicketActivityLog::find($id);
        if (!$log) {
            return response()->json(['message' => 'Log not found'], 404);
        }
        $log->delete();
        return response()->json(['message' => 'Log deleted successfully'], 200);
    }
}
