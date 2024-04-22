<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Tool
 *
 * @property-read int $id
 * @property-read string $client_id
 * @property-read string $name
 * @property-read string|null $uri
 * @property-read int|null $parent_id
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $updated_at
 * @property-read Carbon|null deleted_at
 * @property-read string $icon
 * @property-read int $position
 * @property-read int $creator_id
 *
 * @property-read Collection|Role[] $roles
 *
 * @package App\Models
 */
class Tool extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * field table
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'client_id',
        'name',
        'uri',
        'parent_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'position',
        'icon',
        'creator_id',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permissions', 'tool_id', 'role_id');
    }
}
