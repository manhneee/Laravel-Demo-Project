<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TicketActivityLog extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'user_id', 'action', 'description', 'old_values', 'new_values'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}