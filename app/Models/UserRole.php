<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserRole
 *
 * @package App\Models
 */
class UserRole extends Model
{
    use HasFactory;

    public $timestamps = false;
    public $incrementing = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'role_id'
    ];
}
