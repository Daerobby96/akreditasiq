<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateVersion extends Model
{
    protected $fillable = [
        'template_id',
        'created_by',
        'version_number',
        'content',
        'variables',
        'change_log',
        'file_path',
        'is_current'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_current' => 'boolean'
    ];

    /**
     * Get the template this version belongs to
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }

    /**
     * Get the user who created this version
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this is the current version
     */
    public function isCurrent(): bool
    {
        return $this->is_current;
    }

    /**
     * Make this version the current one
     */
    public function makeCurrent(): bool
    {
        // Set all other versions of this template as not current
        $this->template->versions()->where('id', '!=', $this->id)->update(['is_current' => false]);

        return $this->update(['is_current' => true]);
    }

    /**
     * Get the previous version
     */
    public function getPreviousVersion()
    {
        return self::where('template_id', $this->template_id)
            ->where('created_at', '<', $this->created_at)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get the next version
     */
    public function getNextVersion()
    {
        return self::where('template_id', $this->template_id)
            ->where('created_at', '>', $this->created_at)
            ->orderBy('created_at', 'asc')
            ->first();
    }

    /**
     * Calculate diff with another version
     */
    public function getDiffWith(TemplateVersion $otherVersion): array
    {
        $diffs = [];

        if ($this->content !== $otherVersion->content) {
            $diffs['content'] = [
                'from' => $otherVersion->content,
                'to' => $this->content,
                'changed' => true
            ];
        }

        if ($this->variables !== $otherVersion->variables) {
            $diffs['variables'] = [
                'from' => $otherVersion->variables,
                'to' => $this->variables,
                'changed' => true
            ];
        }

        return $diffs;
    }
}
