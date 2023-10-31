<?php

namespace App\Models;

use App\Helpers\Filterable;
use App\Models\Filters\CategoryFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory, Filterable;

    const TABLE = 'categories';
    protected $table = self::TABLE;
    protected $fillable = ['name', 'category_id', 'description', 'slug', 'active'];
    public $timestamps = true;

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function modelFilter(): ?string
    {
        return $this->provideFilter(CategoryFilter::class);
    }

    public function childrenCategories()
    {
        return $this->hasMany(Category::class)->with('categories');
    }
}
