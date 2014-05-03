<?php

namespace Saxulum\PaginationProvider\Silex\Provider;

use Saxulum\PaginationProvider\Provider\SaxulumPaginationProvider as PimpleSaxulumPaginationProvider;
use Silex\Application;
use Silex\ServiceProviderInterface;

class SaxulumPaginationProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $saxulumPaginationProvider = new PimpleSaxulumPaginationProvider();
        $saxulumPaginationProvider->register($app);
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app) {}
}