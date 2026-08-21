<?php

namespace App\Services;

use App\Models\Document;
use App\Models\GoogleDriveSetting;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class GoogleDriveService
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const ABOUT_URL = 'https://www.googleapis.com/drive/v3/about';

    private const SCOPE = 'https://www.googleapis.com/auth/drive';

    public function authorizationUrl(string $redirectUri): string
    {
        $settings = GoogleDriveSetting::current();

        $query = http_build_query([
            'client_id' => $settings->client_id,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => self::SCOPE,
            'access_type' => 'offline',
            'prompt' => 'consent',
        ]);

        return self::AUTH_URL.'?'.$query;
    }

    public function handleCallback(string $code, string $redirectUri): void
    {
        $settings = GoogleDriveSetting::current();

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'code' => $code,
            'client_id' => $settings->client_id,
            'client_secret' => $settings->client_secret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Gagal menukar kode otorisasi Google: '.$response->body());
        }

        $tokens = $response->json();

        if (empty($tokens['refresh_token'])) {
            throw new \RuntimeException('Google tidak mengembalikan refresh token. Cabut akses aplikasi ini di myaccount.google.com/permissions lalu coba hubungkan ulang.');
        }

        $settings->refresh_token = $tokens['refresh_token'];
        $settings->connected_email = $this->fetchConnectedEmail($tokens['access_token'] ?? null) ?? $settings->connected_email;
        $settings->save();
    }

    public function disconnect(): void
    {
        $settings = GoogleDriveSetting::current();
        $settings->refresh_token = null;
        $settings->connected_email = null;
        $settings->save();
    }

    public function disk(): ?FilesystemAdapter
    {
        $settings = GoogleDriveSetting::current();

        if (! $settings->isConnected()) {
            return null;
        }

        return Storage::build([
            'driver' => 'google',
            'clientId' => $settings->client_id,
            'clientSecret' => $settings->client_secret,
            'refreshToken' => $settings->refresh_token,
            'folder' => $settings->folder_id,
        ]);
    }

    public function testConnection(): array
    {
        $settings = GoogleDriveSetting::current();

        if (! $settings->isConnected()) {
            return ['success' => false, 'message' => 'Belum terhubung ke akun Google.'];
        }

        try {
            $this->disk()?->directories();

            return ['success' => true, 'message' => 'Koneksi ke Google Drive berhasil.'];
        } catch (Throwable $e) {
            Log::warning('Gagal menguji koneksi Google Drive: '.$e->getMessage());

            return ['success' => false, 'message' => 'Gagal terhubung ke Google Drive: '.$e->getMessage()];
        }
    }

    public function uploadDocument(Document $document): void
    {
        $settings = GoogleDriveSetting::current();

        if (! $settings->is_active || ! $settings->isConnected() || ! $document->file_path) {
            return;
        }

        try {
            $disk = $this->disk();

            if (! $disk || ! Storage::disk('public')->exists($document->file_path)) {
                return;
            }

            $contents = Storage::disk('public')->get($document->file_path);
            $remoteName = $document->id.'-'.($document->file_original_name ?: basename($document->file_path));
            $remotePath = 'reminder-dokumen/'.Str::of($remoteName)->replace(['/', '\\'], '-');

            $disk->put((string) $remotePath, $contents);

            $document->forceFill(['google_drive_synced_at' => now()])->saveQuietly();
        } catch (Throwable $e) {
            Log::warning('Gagal mengunggah dokumen ke Google Drive (document #'.$document->id.'): '.$e->getMessage());
        }
    }

    private function fetchConnectedEmail(?string $accessToken): ?string
    {
        if (! $accessToken) {
            return null;
        }

        try {
            $response = Http::withToken($accessToken)->get(self::ABOUT_URL, ['fields' => 'user']);

            return $response->json('user.emailAddress');
        } catch (Throwable $e) {
            Log::warning('Gagal mengambil info akun Google Drive: '.$e->getMessage());

            return null;
        }
    }
}
