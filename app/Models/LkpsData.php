<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LkpsData extends Model
{
    protected $guarded = [];

    protected $casts = [
        'data_values' => 'array'
    ];

    public function table()
    {
        return $this->belongsTo(LamTable::class, 'lam_table_id');
    }

    public function prodi()
    {
        return $this->belongsTo(Prodi::class);
    }
}
