<?php

namespace App\Console\Commands;

use App\Models\Document;
use App\Models\DocumentReminderLog;
use App\Models\WhatsappSetting;
use App\Services\GowaService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendDocumentReminders extends Command
{
    protected $signature = 'documents:send-reminders';

    protected $description = 'Kirim pengingat WhatsApp untuk dokumen yang akan/telah jatuh tempo';

    public function handle(GowaService $gowa): int
    {
        $documents = Document::query()
            ->with('documentType')
            ->where('is_active', true)
            ->whereNotNull('expiry_date')
            ->get();

        $setting = WhatsappSetting::current();

        $sent = 0;

        foreach ($documents as $document) {
            $daysUntil = $document->daysUntilExpiry();

            if ($daysUntil === null) {
                continue;
            }

            $isOverdue = $daysUntil < 0;
            $matchesThreshold = in_array($daysUntil, $document->reminderThresholds(), true);

            if (! $isOverdue && ! $matchesThreshold) {
                continue;
            }

            $alreadySentToday = DocumentReminderLog::where('document_id', $document->id)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if ($alreadySentToday) {
                continue;
            }

            $phone = $document->reminder_phone ?: $setting->notify_admin_number;

            if (! $phone) {
                continue;
            }

            $message = $this->buildMessage($setting, $document);

            $success = $gowa->send($phone, $message, null, 'document_reminder');

            DocumentReminderLog::create([
                'document_id' => $document->id,
                'phone' => $phone,
                'days_before' => $daysUntil,
                'status' => $success ? 'sent' : 'failed',
                'response' => $success ? null : 'Gagal mengirim melalui Gowa.',
            ]);

            $sent++;
        }

        $this->info("Selesai. {$sent} pengingat dokumen terkirim.");

        return self::SUCCESS;
    }

    private function buildMessage(WhatsappSetting $setting, Document $document): string
    {
        return $setting->renderTemplate('msg_document_reminder', [
            'judul' => $document->title,
            'jenis' => $document->documentType->name ?? '-',
            'no_dokumen' => $document->document_number ?: '-',
            'tanggal_berakhir' => $document->expiry_date->translatedFormat('d F Y'),
            'status_line' => $document->reminderStatusLine(),
        ]);
    }
}
