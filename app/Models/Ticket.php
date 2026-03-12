<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = ['created_by_id', 'title', 'description', 'assigned_user_id', 'priority_id', 'status'];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function priority(): BelongsTo
    {
        return $this->belongsTo(Priority::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_ticket')->withTimestamps();
    }

    public function labels(): BelongsToMany
    {
        return $this->belongsToMany(Label::class, 'label_ticket')->withTimestamps();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at');
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(TicketActivityLog::class)->orderByDesc('created_at');
    }

    public function scopeForUser($query, User $user): void
    {
        
        if ($user->isAdmin()) {
            return; // admin sees all
        }
        if ($user->isAgent()) {
            $query->where('assigned_user_id', $user->id);
        } else {
            $query->where('created_by_id', $user->id);
        }
    }
}

