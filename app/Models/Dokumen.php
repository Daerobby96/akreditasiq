<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dokumen extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'kriteria_id',
        'prodi_id',
        'template_id',
        'nama_file',
        'file_path',
        'versi',
        'status',
        'workflow_stage',
        'submitted_at',
        'reviewed_at',
        'approved_at',
        'reviewer_notes',
        'template_data',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'template_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function penilaian_ai()
    {
        return $this->hasMany(PenilaianAi::class);
    }

    public function workflows()
    {
        return $this->morphMany(Workflow::class, 'trackable');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at');
    }

    public function topLevelComments()
    {
        return $this->comments()->whereNull('parent_id')->with('replies');
    }

    public function versions()
    {
        return $this->hasMany(DocumentVersion::class)->orderBy('created_at', 'desc');
    }

    public function latestVersion()
    {
        return $this->hasOne(DocumentVersion::class)->latest();
    }

    public function template()
    {
        return $this->belongsTo(DocumentTemplate::class, 'template_id');
    }
}
