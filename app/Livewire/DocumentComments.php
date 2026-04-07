<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Dokumen;
use App\Notifications\CommentMentionNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;

class DocumentComments extends Component
{
    use WithPagination;

    public Dokumen $document;
    public $newComment = '';
    public $editingCommentId = null;
    public $editingContent = '';
    public $replyingTo = null;
    public $replyContent = '';
    public $showResolved = false;

    protected $listeners = [
        'refreshComments' => '$refresh',
        'commentAdded' => '$refresh'
    ];

    protected $rules = [
        'newComment' => 'required|string|max:1000',
        'editingContent' => 'required|string|max:1000',
        'replyContent' => 'required|string|max:1000'
    ];

    public function mount(Dokumen $document)
    {
        $this->document = $document;
    }

    public function addComment()
    {
        $this->validate(['newComment' => 'required|string|max:1000']);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'document_id' => $this->document->id,
            'content' => $this->newComment,
            'parent_id' => null
        ]);

        // Parse mentions and send notifications
        $this->processMentions($comment);

        $this->newComment = '';
        $this->dispatch('commentAdded');
        session()->flash('message', 'Komentar berhasil ditambahkan.');
    }

    public function replyToComment($commentId)
    {
        $this->validate(['replyContent' => 'required|string|max:1000']);

        $parentComment = Comment::findOrFail($commentId);

        $reply = Comment::create([
            'user_id' => Auth::id(),
            'document_id' => $this->document->id,
            'content' => $this->replyContent,
            'parent_id' => $commentId
        ]);

        // Parse mentions and send notifications
        $this->processMentions($reply);

        $this->replyingTo = null;
        $this->replyContent = '';
        $this->dispatch('commentAdded');
        session()->flash('message', 'Balasan berhasil ditambahkan.');
    }

    public function startEditing($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Check if user can edit this comment
        if ($comment->user_id !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit komentar ini.');
            return;
        }

        $this->editingCommentId = $commentId;
        $this->editingContent = $comment->content;
    }

    public function updateComment()
    {
        $this->validate(['editingContent' => 'required|string|max:1000']);

        $comment = Comment::findOrFail($this->editingCommentId);

        if ($comment->user_id !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk mengedit komentar ini.');
            return;
        }

        $comment->update(['content' => $this->editingContent]);

        // Re-parse mentions
        $this->processMentions($comment);

        $this->editingCommentId = null;
        $this->editingContent = '';
        session()->flash('message', 'Komentar berhasil diperbarui.');
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        // Check if user can delete this comment
        if ($comment->user_id !== Auth::id()) {
            session()->flash('error', 'Anda tidak memiliki izin untuk menghapus komentar ini.');
            return;
        }

        // Soft delete or hard delete based on your preference
        $comment->delete();

        session()->flash('message', 'Komentar berhasil dihapus.');
    }

    public function toggleResolved($commentId)
    {
        $comment = Comment::findOrFail($commentId);

        if ($comment->is_resolved) {
            $comment->markAsUnresolved();
            session()->flash('message', 'Status komentar diubah menjadi belum terselesaikan.');
        } else {
            $comment->markAsResolved();
            session()->flash('message', 'Komentar berhasil ditandai sebagai terselesaikan.');
        }
    }

    public function startReply($commentId)
    {
        $this->replyingTo = $commentId;
        $this->replyContent = '';
    }

    public function cancelReply()
    {
        $this->replyingTo = null;
        $this->replyContent = '';
    }

    public function cancelEditing()
    {
        $this->editingCommentId = null;
        $this->editingContent = '';
    }

    protected function processMentions(Comment $comment)
    {
        $comment->parseMentions();

        // Send notifications to mentioned users
        foreach ($comment->mentionedUsers() as $user) {
            if ($user->id !== Auth::id()) {
                $user->notify(new CommentMentionNotification($comment, $this->document));
            }
        }
    }

    public function getComments()
    {
        $query = $this->document->comments()->with(['user', 'replies.user', 'resolvedBy']);

        if (!$this->showResolved) {
            $query->where('is_resolved', false);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function highlightMentions($content)
    {
        // Highlight @mentions in the content
        return preg_replace_callback('/@(\w+)/', function($matches) {
            $username = $matches[1];
            // You could check if the user exists here and style accordingly
            return '<span class="text-indigo-600 dark:text-indigo-400 font-medium">@' . $username . '</span>';
        }, $content);
    }

    public function render()
    {
        return view('livewire.document-comments', [
            'comments' => $this->document->topLevelComments()->get()
        ])->layout('layouts.app');
    }
}
