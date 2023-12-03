<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    const TABLE = 'customers';
    protected $table = self::TABLE;
    protected $fillable = [
        'name',
        'phone',
        'dob',
        'province_id',
        'district_id',
        'ward_id',
        'address',
        'email',
        'password',
        'active'
    ];
    public $timestamps = true;

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
