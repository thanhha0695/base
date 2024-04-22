<?php

namespace App\Services;

use App\Models\OauthClient;
use App\Supports\Authorized\AuthorInterface;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class OauthClientService
 *
 * @package App\Services
 */
class OauthClientService
{
    /**
     * fetch client
     *
     * @param AuthorInterface $author
     * @return OauthClient[]|Collection
     */
    public function fetchClient(AuthorInterface $author)
    {
        return OauthClient::query()
            ->select(['id', 'name'])
            ->get();
    }
}
