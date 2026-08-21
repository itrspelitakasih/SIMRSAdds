<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Document extends Model
{
    protected $fillable = [
        'title',
        'document_type_id',
        'document_number',
        'issued_date',
        'expiry_date',
        'reminder_days_before',
        'reminder_phone',
        'file_path',
        'file_original_name',
        'google_drive_synced_at',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'issued_date' => 'date',
            'expiry_date' => 'date',
            'google_drive_synced_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function reminderLogs(): HasMany
    {
        return $this->hasMany(DocumentReminderLog::class)->latest();
    }

    public function reminderThresholds(): array
    {
        return collect(explode(',', (string) $this->reminder_days_before))
            ->map(fn ($value) => trim($value))
            ->filter(fn ($value) => $value !== '')
            ->map(fn ($value) => (int) $value)
            ->values()
            ->all();
    }

    public function daysUntilExpiry(): ?int
    {
        if (! $this->expiry_date) {
            return null;
        }

        return (int) Carbon::today()->diffInDays($this->expiry_date->copy()->startOfDay(), false);
    }

    public function expiryStatus(): string
    {
        $days = $this->daysUntilExpiry();

        if ($days === null) {
            return 'none';
        }

        if ($days < 0) {
            return 'expired';
        }

        if ($days <= 7) {
            return 'critical';
        }

        if ($days <= 30) {
            return 'warning';
        }

        return 'ok';
    }
}
