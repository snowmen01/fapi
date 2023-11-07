<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    const TABLE = 'categories';
    protected $table = self::TABLE;
    protected $fillable = ['name', 'category_id', 'description', 'slug', 'active'];
    public $timestamps = true;

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
