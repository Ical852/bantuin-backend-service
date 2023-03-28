<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BantuanOrder extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function bantuan()
    {
        return $this->hasOne(Bantuan::class, 'id', 'bantuan_id');
    }

    public function helper()
    {
        return $this->hasOne(Helper::class, 'id', 'helper_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
