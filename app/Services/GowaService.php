<?php

namespace App\Services;

use App\Models\NotificationLog;
use App\Models\WhatsappSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class GowaService
{
    /**
     * Send a WhatsApp message through the configured Gowa server.
     */
    public function send(string $phone, string $message, ?int $ticketId = null, string $event = 'manual'): bool
    {
        $settings = WhatsappSetting::current();
        $normalizedPhone = $this->normalizePhone($phone);

        if (! $settings->is_active || ! $settings->base_url || $normalizedPhone === null) {
            NotificationLog::create([
                'ticket_id' => $ticketId,
                'phone' => $normalizedPhone ?? $phone,
                'event' => $event,
                'status' => 'failed',
                'response' => 'Gowa belum dikonfigurasi/diaktifkan atau nomor tujuan kosong.',
            ]);

            return false;
        }

        $phone = $normalizedPhone;

        $url = rtrim($settings->base_url, '/').($settings->port ? ':'.$settings->port : '').'/send/message';

        try {
            $request = Http::withBasicAuth($settings->username ?? '', $settings->password ?? '')
                ->timeout(15);

            if ($settings->device_id) {
                $request = $request->withHeaders(['X-Device-Id' => $settings->device_id]);
            }

            $response = $request->post($url, [
                'phone' => $phone,
                'message' => $message,
            ]);

            $success = $response->successful();

            NotificationLog::create([
                'ticket_id' => $ticketId,
                'phone' => $phone,
                'event' => $event,
                'status' => $success ? 'sent' : 'failed',
                'response' => substr($response->body(), 0, 2000),
            ]);

            return $success;
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim notifikasi WhatsApp via Gowa: '.$e->getMessage());

            NotificationLog::create([
                'ticket_id' => $ticketId,
                'phone' => $phone,
                'event' => $event,
                'status' => 'failed',
                'response' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62'.$digits;
        }

        return $digits.'@s.whatsapp.net';
    }
}
