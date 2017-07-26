<?php

/*
 * This file is part of the bqrd openapi middleware package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bqrd\OpenApi;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Laravel\Lumen\Auth\Authorizable;

class User implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * validToken.
     *
     * @param mixed $parameters
     * @static
     *
     * @return mixed
     */
    public static function validToken($parameters)
    {
        $appId = $parameters->get('x-api-proxy-app-id', 'default');
        $sign = $parameters->get('Sign', $parameters->get('sign'));
        $secret = config('auth.secret');
        if (isset($secret[$appId]) && $token = $secret[$appId]) {
            return Liugj\Helpers\validate($parameters, $sign, $token) ? new self() : null;
        }
    }
}