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
}
