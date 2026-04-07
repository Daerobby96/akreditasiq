<?php

namespace App\Notifications;

use App\Models\Dokumen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowTransitionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Dokumen $document;
    protected string $oldStage;
    protected string $newStage;
    protected array $metadata;

    /**
     * Create a new notification instance.
     */
    public function __construct(Dokumen $document, string $oldStage, string $newStage, array $metadata = [])
    {
        $this->document = $document;
        $this->oldStage = $oldStage;
        $this->newStage = $newStage;
        $this->metadata = $metadata;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $stageLabels = [
            'upload' => 'Upload Dokumen',
            'ai_analysis' => 'Analisis AI',
            'review' => 'Review',
            'revision' => 'Revisi',
            'final_approval' => 'Persetujuan Final'
        ];

        return (new MailMessage)
            ->subject("Update Status Dokumen: {$this->document->nama_file}")
            ->greeting("Halo {$notifiable->name}!")
            ->line("Status dokumen **{$this->document->nama_file}** telah berubah.")
            ->line("**Kriteria:** {$this->document->kriteria->nama}")
            ->line("**Dari:** " . ($stageLabels[$this->oldStage] ?? ucfirst($this->oldStage)))
            ->line("**Ke:** " . ($stageLabels[$this->newStage] ?? ucfirst($this->newStage)))
            ->when(isset($this->metadata['reviewer_notes']), function ($mail) {
                return $mail->line("**Catatan Reviewer:** " . $this->metadata['reviewer_notes']);
            })
            ->action('Lihat Dokumen', route('data-dukung'))
            ->line('Terima kasih telah menggunakan Sistem Akreditasi Cerdas!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'document_id' => $this->document->id,
            'document_name' => $this->document->nama_file,
            'kriteria' => $this->document->kriteria->nama,
            'old_stage' => $this->oldStage,
            'new_stage' => $this->newStage,
            'transition_time' => now(),
            'metadata' => $this->metadata
        ];
    }
}