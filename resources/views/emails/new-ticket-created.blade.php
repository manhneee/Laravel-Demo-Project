<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Ticket #{{ $ticket->id }}</title>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #334155; margin: 0; padding: 0; background: #f1f5f9; }
        .container { max-width: 560px; margin: 24px auto; padding: 24px; background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h1 { font-size: 1.25rem; color: #1e293b; margin: 0 0 16px 0; }
        .meta { font-size: 0.875rem; color: #64748b; margin-bottom: 16px; }
        .description { margin: 16px 0; padding: 12px; background: #f8fafc; border-radius: 8px; white-space: pre-wrap; font-size: 0.875rem; }
        .btn { display: inline-block; padding: 10px 20px; background: #4f46e5; color: #fff !important; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 0.875rem; margin-top: 16px; }
        .btn:hover { background: #4338ca; }
        .footer { margin-top: 24px; font-size: 0.75rem; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>New ticket #{{ $ticket->id }}: {{ $ticket->title }}</h1>
        <div class="meta">
            Created {{ $ticket->created_at?->diffForHumans() ?? 'recently' }}
            @if($ticket->priority)
                · Priority: {{ $ticket->priority->name }}
            @endif
            @if($ticket->createdBy)
                · By {{ $ticket->createdBy->name }}
            @endif
        </div>
        @if($ticket->description)
            <div class="description">{{ Str::limit($ticket->description, 300) }}</div>
        @endif
        <a href="{{ $editUrl }}" class="btn">Edit ticket</a>
        <div class="footer">{{ config('app.name') }} – Ticket system</div>
    </div>
</body>
</html>
