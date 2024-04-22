<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OauthClient
 *
 * @property-read int $id
 * @property-read int $user_id
 * @property-read string $name
 * @property-read string $secret
 * @property-read string $provider
 * @property-read string $redirect
 * @property-read int $personal_access_client
 * @property-read int $password_client
 * @property-read int $revoked
 * @property-read Carbon|null $created_at
 * @property-read Carbon|null $deleted_at
 *
 * @package App\Models
 */
class OauthClient extends Model
{
    /**
     * field table
     *
     * @var string[]
     */
    public $fillable = [
        'id',
        'user_id',
        'name',
        'secret',
        'provider',
        'redirect',
        'personal_access_client',
        'password_client',
        'revoked',
        'created_at',
        'updated_at'
    ];

    /**
     * @var string[]
     */
    public $hidden = [
        'secret',
        'password_client'
    ];
}
