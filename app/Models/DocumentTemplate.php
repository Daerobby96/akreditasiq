<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class DocumentTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'kriteria_id',
        'created_by',
        'status',
        'variables',
        'metadata',
        'file_path',
        'preview_path',
        'usage_count',
        'last_used_at'
    ];

    protected $casts = [
        'variables' => 'array',
        'metadata' => 'array',
        'last_used_at' => 'datetime'
    ];

    /**
     * Get the kriteria this template belongs to
     */
    public function kriteria(): BelongsTo
    {
        return $this->belongsTo(Kriteria::class);
    }

    /**
     * Get the user who created this template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all versions of this template
     */
    public function versions(): HasMany
    {
        return $this->hasMany(TemplateVersion::class, 'template_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the current version of this template
     */
    public function currentVersion()
    {
        return $this->hasOne(TemplateVersion::class, 'template_id')->where('is_current', true);
    }

    /**
     * Get documents created from this template
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Dokumen::class, 'template_id');
    }

    /**
     * Check if template is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if template is draft
     */
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    /**
     * Publish the template
     */
    public function publish(): bool
    {
        return $this->update(['status' => 'published']);
    }

    /**
     * Archive the template
     */
    public function archive(): bool
    {
        return $this->update(['status' => 'archived']);
    }

    /**
     * Create a new version of the template
     */
    public function createVersion(array $data, ?int $userId = null): TemplateVersion
    {
        // Set all other versions as not current
        $this->versions()->update(['is_current' => false]);

        // Generate version number
        $latestVersion = $this->versions()->first();
        $nextVersion = $latestVersion
            ? $this->incrementVersionNumber($latestVersion->version_number)
            : '1.0.0';

        return $this->versions()->create([
            'created_by' => $userId ?? auth()->id(),
            'version_number' => $nextVersion,
            'content' => $data['content'] ?? null,
            'variables' => $data['variables'] ?? $this->variables,
            'change_log' => $data['change_log'] ?? null,
            'file_path' => $data['file_path'] ?? null,
            'is_current' => true
        ]);
    }

    /**
     * Increment version number (semantic versioning)
     */
    protected function incrementVersionNumber(string $currentVersion): string
    {
        $parts = explode('.', $currentVersion);

        if (count($parts) >= 3) {
            $parts[2] = (int) $parts[2] + 1; // Patch version increment
        } else {
            $parts[] = 1;
        }

        return implode('.', $parts);
    }

    /**
     * Get template file URL
     */
    public function getFileUrl(): ?string
    {
        return $this->file_path ? Storage::url($this->file_path) : null;
    }

    /**
     * Get preview image URL
     */
    public function getPreviewUrl(): ?string
    {
        return $this->preview_path ? Storage::url($this->preview_path) : null;
    }

    /**
     * Record template usage
     */
    public function recordUsage(): void
    {
        $this->increment('usage_count');
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Get template variables with defaults
     */
    public function getVariables(): array
    {
        return $this->variables ?? [];
    }

    /**
     * Extract variables from template content
     */
    public function extractVariablesFromContent(string $content): array
    {
        preg_match_all('/\{\{(\w+)\}\}/', $content, $matches);

        $variables = [];
        if (!empty($matches[1])) {
            foreach (array_unique($matches[1]) as $variable) {
                $variables[$variable] = [
                    'name' => $variable,
                    'label' => ucwords(str_replace('_', ' ', $variable)),
                    'type' => 'text',
                    'required' => true,
                    'default' => ''
                ];
            }
        }

        return $variables;
    }

    /**
     * Fill template with data
     */
    public function fillTemplate(array $data): string
    {
        $content = $this->currentVersion?->content ?? '';

        if (empty($content)) {
            return '';
        }

        // Replace variables in content
        foreach ($data as $key => $value) {
            $content = str_replace("{{{$key}}}", $value, $content);
        }

        // Remove any unfilled variables
        $content = preg_replace('/\{\{\w+\}\}/', '[BELUM DIISI]', $content);

        return $content;
    }

    /**
     * Get usage statistics
     */
    public function getUsageStats(): array
    {
        return [
            'total_usage' => $this->usage_count,
            'last_used' => $this->last_used_at?->diffForHumans(),
            'document_count' => $this->documents()->count(),
            'version_count' => $this->versions()->count()
        ];
    }
}
