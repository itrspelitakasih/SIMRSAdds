<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppSetting extends Model
{
    protected $fillable = [
        'app_name',
        'logo_light',
        'logo_dark',
        'logo_icon',
    ];

    public static function current(): self
    {
        return self::first() ?? self::create([]);
    }

    public function logoLightUrl(): string
    {
        return $this->logo_light ? Storage::disk('public')->url($this->logo_light) : '/images/logo/logo.svg';
    }

    public function logoDarkUrl(): string
    {
        return $this->logo_dark ? Storage::disk('public')->url($this->logo_dark) : '/images/logo/logo-dark.svg';
    }

    public function logoIconUrl(): string
    {
        return $this->logo_icon ? Storage::disk('public')->url($this->logo_icon) : '/images/logo/logo-icon.svg';
    }
}
