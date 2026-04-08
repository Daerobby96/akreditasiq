<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Narasi extends Model
{
    protected $fillable = ['prodi_id', 'kriteria_id', 'content', 'status', 'assignee_id'];

    protected $casts = [
        'content' => 'array'
    ];

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function workflows()
    {
        return $this->morphMany(Workflow::class, 'trackable');
    }
}
