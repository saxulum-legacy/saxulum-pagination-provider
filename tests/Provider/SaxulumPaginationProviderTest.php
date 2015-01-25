<?php

namespace Saxulum\Tests\PaginationProvider\Provider;

use Saxulum\PaginationProvider\Silex\Provider\SaxulumPaginationProvider;
use Silex\Application;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

class SaxulumPaginationProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProvider()
    {
        $app = new Application();

        $app->register(new UrlGeneratorServiceProvider());
        $app->register(new TwigServiceProvider());
        $app->register(new TranslationServiceProvider());
        $app->register(new SaxulumPaginationProvider());

        $extensions = $app['twig']->getExtensions();

        $this->assertInstanceOf(
            'Saxulum\PaginationProvider\Twig\PaginationExtension',
            $extensions['knp_pagination']
        );

        $listeners = $app['dispatcher']->getListeners();

        $this->assertInstanceOf(
            'Saxulum\PaginationProvider\Subscriber\SlidingPaginationSubscriber',
            $listeners['kernel.request'][2][0]
        );

        $this->assertInstanceOf(
            'Knp\Component\Pager\Event\Subscriber\Sortable\SortableSubscriber',
            $listeners['knp_pager.before'][0][0]
        );

        $this->assertInstanceOf(
            'Knp\Component\Pager\Event\Subscriber\Paginate\PaginationSubscriber',
            $listeners['knp_pager.before'][1][0]
        );

        $this->assertInstanceOf(
            'Saxulum\PaginationProvider\Subscriber\SlidingPaginationSubscriber',
            $listeners['knp_pager.pagination'][0][0]
        );

        $this->assertInstanceOf(
            'Knp\Component\Pager\Event\Subscriber\Paginate\PaginationSubscriber',
            $listeners['knp_pager.pagination'][1][0]
        );

        $paginator = $app['knp_paginator'];

        $this->assertNotEmpty($app['knp_paginator.options']);

        $this->assertInstanceOf('Knp\Component\Pager\Paginator', $paginator);}
}
