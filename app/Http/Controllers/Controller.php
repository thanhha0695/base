<?php

namespace App\Http\Controllers;

use App\Supports\Authorized\AuthorInterface;
use App\Supports\Traits\ResponseStatus;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, ResponseStatus;

    /**
     * author
     *
     * @return AuthorInterface
     * @throws AuthenticationException
     */
    protected function author(): AuthorInterface
    {
        $author = request()->attributes->get('author');
        if ($author === null) {
            throw new AuthenticationException('Unauthorized');
        }
        return $author;
    }
}
