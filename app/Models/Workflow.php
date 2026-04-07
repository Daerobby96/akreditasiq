<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $fillable = [
        'trackable_id',
        'trackable_type',
        'from_status',
        'to_status',
        'user_id',
        'comment',
        'action',
        'old_value',
        'new_value',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function trackable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
