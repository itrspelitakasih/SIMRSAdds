<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoogleDriveSetting extends Model
{
    protected $fillable = [
        'client_id',
        'client_secret',
        'refresh_token',
        'folder_id',
        'connected_email',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'client_secret' => 'encrypted',
            'refresh_token' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return self::first() ?? self::create([]);
    }

    public function isConnected(): bool
    {
        return ! empty($this->refresh_token);
    }
}
