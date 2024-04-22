<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

/**
 * Class User
 *
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read string $phone_number
 * @property-read int $gender
 * @property-read Carbon|null $birthday
 * @property-read string|null $note
 * @property-read string|null $avatar
 * @property-read int $status
 * @property-read string $username
 * @property-read string $password
 * @property-read int $role_id
 * @property-read int $creator_id
 *
 * @property Role $role
 * @property-read Collection|Tool[] $tools
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'gender',
        'birthday',
        'note',
        'avatar',
        'status',
        'username',
        'password',
        'role_id',
        'creator_id',
    ];

    public $dates = [
        'deleted_at',
        'created_at',
        'updated_at'
    ];

    /**
     * hidden show when find model
     *
     * @var string[]
     */
    protected $hidden = [
        'password'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function tools()
    {
        return $this->belongsToMany(Tool::class, 'permissions', 'user_id', 'tool_id');
    }
}
