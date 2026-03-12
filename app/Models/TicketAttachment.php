<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketAttachment extends Model
{
    use HasFactory;

    protected $fillable = ['ticket_id', 'file_path', 'original_name', 'mime_type', 'size'];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
    
}
