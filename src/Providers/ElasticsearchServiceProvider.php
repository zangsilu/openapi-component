<?php

/*
 * This file is part of the bqrd openapi component package.
 *
 * (c) liugj <liugj@boqii.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bqrd\OpenApi\Providers;

use Cviebrock\LaravelElasticsearch\Factory;
use Cviebrock\LaravelElasticsearch\LumenManager;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider.
 */
class ElasticsearchServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->singleton('elasticsearch.factory', function ($app) {
            return new Factory();
        });

        $app->singleton('elasticsearch', function ($app) {
            return new LumenManager($app, $app['elasticsearch.factory']);
        });

        $app->alias('elasticsearch', LumenManager::class);
    }
}
