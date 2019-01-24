<?php

/*
 * This file is part of the openapi package.
 *
 * (c) 商城组<shop-rd@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bqrd\OpenApi\Middleware;

use Closure;

class ReadMasterMiddleware
{
    /**
     * writeInterface.
     *
     * @var mixed
     */
    public $writeInterface = [];

    /**
     * __construct.
     *
     *
     *
     * @return mixed
     */
    public function __construct()
    {

        $this->writeInterface = array_flip(config('master-route.list'));
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
        $act = $request->input('Act') ?: $request->input('method');
        $act = str_replace('.', '_', $act);
        if ($request->isMethod('post') && isset($this->writeInterface[$act])) {
            app('db')->setDefaultConnection('mysql::write');
        }

        $config = config('validation.' . str_replace('.', '_', $act));
        $module = 'unknow';
        if ($config) {
            list($rule, $service, $messages, $exchangeKeys, $responseKeys) = array_values($config);
            list($service, $act) = array_pad(explode('@', $service), 2, lcfirst($act));
            $module = explode('\\', $service)[2] ?? 'unknow';
        }

        app('db')->listen(function ($query) use ($act, $module) {
            $prepareSql = str_replace(['?', "\r\n", "\r", "\n"], ["'%s'", '', '', ''], $query->sql);
            $prepareSql = preg_replace('/:[0-9a-z_]+/i', "'%s'", $prepareSql);
            $sql = vsprintf($prepareSql, $query->bindings);
            $act = $act ? $act : 'unknow';
            app('log')->info(sprintf('module:%s act:%s sql: %s cost:%dms', $module, $act, $sql, $query->time));
        });

        return $next($request);
    }
}
