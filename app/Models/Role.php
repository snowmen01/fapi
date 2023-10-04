<?php

namespace App\Models;

use App\Helpers\Filterable;
use App\Models\Filters\RoleFilter;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Role extends Model
{
    use HasFactory, Filterable, HasRoles;

    const TABLE = 'roles';
    protected $table = self::TABLE;
    protected $fillable = ['name', 'guard_name'];
    public $timestamps = true;

    public function modelFilter(): ?string
    {
        return $this->provideFilter(RoleFilter::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }
}
