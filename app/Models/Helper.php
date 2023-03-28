<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Helper extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function helper_rating()
    {
        return $this->hasMany(HelperRating::class);
    }

    public function bantuan_order()
    {
        return $this->belongsTo(BantuanOrder::class);
    }
}
