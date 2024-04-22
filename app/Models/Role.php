<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Role
 *
 * @property-read int $id
 * @property-read int $manage_id
 * @property-read string $name
 * @property-read string $description
 * @property-read int $parent_id
 * @property-read int $creator_id
 *
 * @property-read Collection|Permission[] $permissions
 * @property-read User $manage
 * @package App\Models
 */
class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * field table
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'manage_id',
        'name',
        'description',
        'parent_id',
        'creator_id',
    ];

    public function tools()
    {
        return $this->belongsToMany(Tool::class, 'permissions', 'role_id', 'tool_id');
    }

    public function manage()
    {
        return $this->belongsTo(User::class, 'manage_id');
    }
}
