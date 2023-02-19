<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bantuan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function bantuan_category()
    {
        return $this->hasOne(BantuanCategory::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
