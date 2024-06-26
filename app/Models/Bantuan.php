<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Bantuan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function getImageAttribute()
    {
        return url('') . Storage::url($this->attributes['image']);
    }

    public function bantuan_category()
    {
        return $this->hasOne(BantuanCategory::class, 'id', 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bantuan_order()
    {
        return $this->belongsTo(BantuanOrder::class);
    }
}
