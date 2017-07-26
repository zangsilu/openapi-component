<?php

/*
 * This file is part of the bqrd openapi middleware package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bqrd\OpenApi\Providers;

use Event;
use Illuminate\Database\DatabaseServiceProvider as ServiceProvider;
use Illuminate\Database\Events\StatementPrepared;

class DatabaseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent:: boot();

        Event::listen(StatementPrepared::class, function ($event) {
            $event->statement->setFetchMode(\PDO::FETCH_ASSOC);
        });

        app('db')->listen(function ($query) {
            $prepareSql = str_replace(['?', "\r\n", "\r", "\n"], ["'%s'", '', '', ''], $query->sql);
            $prepareSql = preg_replace('/:[0-9a-z_]+/i', "'%s'", $prepareSql);
            $sql = vsprintf($prepareSql, $query->bindings);
            app('log')->info(sprintf('sql:%s cost:%dms', $sql, $query->time));
        });
    }
}
