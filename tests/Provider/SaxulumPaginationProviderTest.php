<?php

namespace Saxulum\Tests\PaginationProvider\Provider;

use Pimple\Container;
use Saxulum\PaginationProvider\Provider\SaxulumPaginationProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\KernelServiceProvider;
use Silex\Provider\RoutingServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\Routing\RouteCollection;

class SaxulumPaginationProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProvider()
    {
        $container = new Container();

        $container['routes'] = function () {
            return new RouteCollection();
        };

        $container->register(new KernelServiceProvider());
        $container->register(new RoutingServiceProvider());
        $container->register(new LocaleServiceProvider());
        $container->register(new TwigServiceProvider());
        $container->register(new TranslationServiceProvider());
        $container->register(new SaxulumPaginationProvider());

        $extensions = $container['twig']->getExtensions();

        $this->assertInstanceOf(
            'Saxulum\PaginationProvider\Twig\PaginationExtension',
            $extensions['knp_pagination']
        );

        $listeners = $container['dispatcher']->getListeners();

        $this->assertInstanceOf(
            'Saxulum\PaginationProvider\Subscriber\SlidingPaginationSubscriber',
            $listeners['kernel.request'][0][0]
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

        $paginator = $container['knp_paginator'];

        $this->assertNotEmpty($container['knp_paginator.options']);

        $this->assertInstanceOf('Knp\Component\Pager\Paginator', $paginator);}
}
