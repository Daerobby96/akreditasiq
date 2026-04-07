<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'document_id',
        'parent_id',
        'content',
        'mentions',
        'attachments',
        'is_resolved',
        'resolved_at',
        'resolved_by'
    ];

    protected $casts = [
        'mentions' => 'array',
        'attachments' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime'
    ];

    /**
     * Get the user who made this comment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the document this comment belongs to
     */
    public function document(): BelongsTo
    {
        return $this->belongsTo(Dokumen::class, 'document_id');
    }

    /**
     * Get the parent comment (for threaded replies)
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * Get all replies to this comment
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->orderBy('created_at');
    }

    /**
     * Get the user who resolved this comment
     */
    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get all child comments recursively
     */
    public function allReplies(): HasMany
    {
        return $this->replies()->with('allReplies');
    }

    /**
     * Check if comment is a reply (has parent)
     */
    public function isReply(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Check if comment has replies
     */
    public function hasReplies(): bool
    {
        return $this->replies()->exists();
    }

    /**
     * Get the depth level of this comment in the thread
     */
    public function getDepth(): int
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    /**
     * Mark comment as resolved
     */
    public function markAsResolved(?int $userId = null): bool
    {
        return $this->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => $userId ?? Auth::id()
        ]);
    }

    /**
     * Mark comment as unresolved
     */
    public function markAsUnresolved(): bool
    {
        return $this->update([
            'is_resolved' => false,
            'resolved_at' => null,
            'resolved_by' => null
        ]);
    }

    /**
     * Parse mentions from content and store them
     */
    public function parseMentions(): void
    {
        preg_match_all('/@(\w+)/', $this->content, $matches);

        if (!empty($matches[1])) {
            $mentionedUsers = User::whereIn('name', $matches[1])
                ->orWhereIn('email', $matches[1])
                ->pluck('id')
                ->toArray();

            $this->update(['mentions' => $mentionedUsers]);
        }
    }

    /**
     * Get mentioned users
     */
    public function mentionedUsers()
    {
        if (empty($this->mentions)) {
            return collect();
        }

        return User::whereIn('id', $this->mentions)->get();
    }
}
