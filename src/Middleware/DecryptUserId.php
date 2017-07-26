<?php

/*
 * This file is part of the bqrd openapi middleware package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bqrd\OpenApi\Middleware;

use Closure;

class DecryptUserId
{
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
        if ($request->input('UserId')) {
            $request['UserId'] = bdecrypt_user($request->input('UserId'))[1];
        }

        return $next($request);
    }
}
