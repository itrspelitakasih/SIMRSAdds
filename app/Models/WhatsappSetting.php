<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
    protected $fillable = [
        'base_url',
        'port',
        'username',
        'password',
        'device_id',
        'notify_admin_number',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'encrypted',
            'is_active' => 'boolean',
        ];
    }

    public static function current(): self
    {
        return self::first() ?? self::create([]);
    }
}
