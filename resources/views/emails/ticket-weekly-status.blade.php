<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket status – {{ config('app.name') }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.5; color: #334155; margin: 0; padding: 0; background: #f1f5f9; }
        .container { max-width: 640px; margin: 24px auto; padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h1 { font-size: 1.25rem; color: #1e293b; margin: 0 0 20px 0; }
        .status-section { margin-bottom: 24px; }
        .status-heading { font-size: 0.9375rem; font-weight: 600; color: #475569; margin-bottom: 8px; padding-bottom: 4px; border-bottom: 1px solid #e2e8f0; text-transform: capitalize; }
        .status-heading .count { color: #64748b; font-weight: 500; }
        table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; }
        th { text-align: left; padding: 6px 8px; background: #f8fafc; color: #64748b; font-weight: 500; }
        td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; }
        tr:hover { background: #fafafa; }
        .footer { margin-top: 24px; font-size: 0.75rem; color: #94a3b8; }
        .empty { color: #94a3b8; font-style: italic; padding: 8px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>All tickets by status</h1>
        <p style="margin: 0 0 20px 0; color: #64748b; font-size: 0.875rem;">Summary of every ticket grouped by status.</p>

        @foreach($statusOrder as $status)
            @php
                $tickets = $ticketsByStatus->get($status, collect());
                $label = str_replace('_', ' ', $status);
            @endphp
            <div class="status-section">
                <div class="status-heading">{{ $label }} <span class="count">({{ $tickets->count() }})</span></div>
                @if($tickets->isNotEmpty())
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Priority</th>
                                <th>Created by</th>
                                <th>Assigned to</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $t)
                                <tr>
                                    <td>{{ $t->id }}</td>
                                    <td>{{ $t->title ?? '—' }}</td>
                                    <td>{{ $t->priority->name ?? '—' }}</td>
                                    <td>{{ $t->createdBy->name ?? '—' }}</td>
                                    <td>{{ $t->assignedUser->name ?? '—' }}</td>
                                    <td>{{ $t->updated_at ? $t->updated_at->format('M j, Y') : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">No tickets in this status.</div>
                @endif
            </div>
        @endforeach

        <div class="footer">{{ config('app.name') }} – Ticket system</div>
    </div>
</body>
</html>
