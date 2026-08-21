<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    public const STATUSES = [
        'open' => 'Baru',
        'in_progress' => 'Diproses',
        'waiting_confirmation' => 'Menunggu Konfirmasi',
        'done' => 'Selesai',
        'rejected' => 'Ditolak',
    ];

    public const PRIORITIES = [
        'low' => 'Rendah',
        'medium' => 'Sedang',
        'high' => 'Tinggi',
    ];

    protected $fillable = [
        'ticket_code',
        'reporter_name',
        'reporter_phone',
        'unit_id',
        'category_id',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'started_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function reportAttachments(): HasMany
    {
        return $this->attachments()->where('type', 'report');
    }

    public function completionAttachments(): HasMany
    {
        return $this->attachments()->where('type', 'completion');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class)->orderBy('created_at');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }
}
