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
        'msg_ticket_created_admin',
        'msg_ticket_created_reporter',
        'msg_ticket_completed',
        'msg_document_reminder',
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

    public static function defaultTemplates(): array
    {
        return [
            'msg_ticket_created_admin' => "*Tiket Baru Masuk*\n\n".
                "*Kode Tiket:* {kode_tiket}\n".
                "*Pelapor:* {pelapor}\n".
                "*Unit:* {unit}\n".
                "*Kategori:* {kategori}\n".
                "*Judul:* {judul}\n\n".
                'Mohon segera ditindaklanjuti.',
            'msg_ticket_created_reporter' => "*Tiket Anda Telah Diterima*\n\n".
                "*Kode Tiket:* {kode_tiket}\n\n".
                'Silakan simpan kode ini untuk melacak status tiket.',
            'msg_ticket_completed' => "*Tiket Selesai Dikerjakan*\n\n".
                "*Kode Tiket:* {kode_tiket}\n\n".
                'Terima kasih telah melaporkan melalui E-Tiket IT Rumah Sakit.',
            'msg_document_reminder' => "*Pengingat Pembaruan Dokumen*\n\n".
                "*Judul:* {judul}\n".
                "*Jenis:* {jenis}\n".
                "*No. Dokumen:* {no_dokumen}\n".
                "*Tanggal Berakhir:* {tanggal_berakhir}\n\n".
                "{status_line}\n\n".
                'Mohon segera diperbarui dan diunggah ulang ke sistem.',
        ];
    }

    public function defaultTemplate(string $key): string
    {
        return self::defaultTemplates()[$key] ?? '';
    }

    public function renderTemplate(string $key, array $replacements): string
    {
        $template = $this->{$key} ?: $this->defaultTemplate($key);

        $search = array_map(fn ($token) => '{'.$token.'}', array_keys($replacements));

        return str_replace($search, array_values($replacements), $template);
    }
}
