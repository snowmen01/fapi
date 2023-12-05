<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    const TABLE = 'coupons';
    protected $table = self::TABLE;
    protected $fillable = ['name', 'code', 'type', 'value', 'count', 'expired_at', 'active', 'description'];
    public $timestamps = true;
}
