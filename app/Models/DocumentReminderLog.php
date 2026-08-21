<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentReminderLog extends Model
{
    protected $fillable = [
        'document_id',
        'phone',
        'days_before',
        'status',
        'response',
    ];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
