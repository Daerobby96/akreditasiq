<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LamTable extends Model
{
    protected $guarded = [];

    public function columns()
    {
        return $this->hasMany(LamTableColumn::class)->orderBy('sort_order');
    }
}
