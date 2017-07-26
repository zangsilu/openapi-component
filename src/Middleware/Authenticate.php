<?php

/*
 * This file is part of the bqrd openapi component package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bqrd\OpenApi\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;

    /**
     * Create a new middleware instance.
     *
     * Authenticate constructor.
     *
     * @param Auth $auth
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if ($this->auth->guard($guard)->guest()) {
            return response('Unauthorized.', 401);
        }

        unset($request['x-api-proxy-app-id']);
        unset($request['x-api-proxy-nonce']);
        unset($request['x-api-proxy-timestamp']);
        if ($request->has('Sign') || $request->has('sign')) {
            $request['Sign'] = md5('1');
        }

        return $next($request);
    }
}
