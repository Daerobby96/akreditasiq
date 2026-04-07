<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Dokumen;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentMentionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Comment $comment;
    protected Dokumen $document;

    /**
     * Create a new notification instance.
     */
    public function __construct(Comment $comment, Dokumen $document)
    {
        $this->comment = $comment;
        $this->document = $document;
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
        return (new MailMessage)
            ->subject("Anda disebutkan dalam komentar di dokumen: {$this->document->nama_file}")
            ->greeting("Halo {$notifiable->name}!")
            ->line("{$this->comment->user->name} menyebutkan Anda dalam komentar pada dokumen **{$this->document->nama_file}**.")
            ->line("**Komentar:** {$this->comment->content}")
            ->action('Lihat Komentar', route('data-dukung'))
            ->line('Terima kasih telah berkolaborasi!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'document_id' => $this->document->id,
            'document_name' => $this->document->nama_file,
            'commenter_name' => $this->comment->user->name,
            'comment_content' => $this->comment->content,
            'type' => 'comment_mention',
            'created_at' => now()
        ];
    }
}