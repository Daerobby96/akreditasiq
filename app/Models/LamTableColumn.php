<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LamTableColumn extends Model
{
    protected $guarded = [];

    public function table()
    {
        return $this->belongsTo(LamTable::class, 'lam_table_id');
    }

    public function parent()
    {
        return $this->belongsTo(LamTableColumn::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(LamTableColumn::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    public function getColspanAttribute()
    {
        if ($this->children->isEmpty()) {
            return 1;
        }
        return $this->children->sum(fn($child) => $child->colspan);
    }
}
