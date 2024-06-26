<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BantuanCategory extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function bantuan()
    {
        return $this->belongsTo(Bantuan::class);
    }
}
