<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Permission
 *
 * @property-read int $id
 * @property-read int $user_id
 * @property-read int $role_id
 * @property-read int $tool_id
 * @property-read string $action
 *
 * @package App\Models
 */
class Permission extends Model
{
    use HasFactory;

    /**
     * disable update_at and created_at
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * disable auto increment
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * field table
     *
     * @var string[]
     */
    protected $fillable = [
        'id',
        'user_id',
        'role_id',
        'tool_id',
        'action',
    ];
}
