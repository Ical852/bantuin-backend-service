<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelperRating extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];

    public function helper()
    {
        return $this->belongsTo(Helper::class);
    }

    public function bantuan()
    {
        return $this->hasOne(Bantuan::class, 'id', 'bantuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
