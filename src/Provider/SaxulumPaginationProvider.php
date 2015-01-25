<?php

namespace Saxulum\PaginationProvider\Provider;

use Knp\Component\Pager\Event\Subscriber\Paginate\PaginationSubscriber;
use Knp\Component\Pager\Event\Subscriber\Sortable\SortableSubscriber;
use Knp\Component\Pager\Paginator;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Saxulum\PaginationProvider\Helper\Processor;
use Saxulum\PaginationProvider\Subscriber\SlidingPaginationSubscriber;
use Saxulum\PaginationProvider\Twig\PaginationExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SaxulumPaginationProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['knp_paginator.default_options'] = array(
            'defaultPaginationOptions' => array(
                'pageParameterName' => 'page',
                'sortFieldParameterName' => 'sort',
                'sortDirectionParameterName' => 'direction',
                'filterFieldParameterName' => 'filterField',
                'filterValueParameterName' => 'filterValue',
                'distinct' => true,
            ),
            'subscriberOptions' => array(
                'defaultPaginationTemplate' => '@SaxulumPaginationProvider/sliding.html.twig',
                'defaultSortableTemplate' => '@SaxulumPaginationProvider/sortable_link.html.twig',
                'defaultFiltrationTemplate' => '@SaxulumPaginationProvider/filtration.html.twig',
                'defaultPageRange' => 5,
            )
        );

        $container['knp_paginator.options.initializer'] = $container->protect(function () use ($container) {
            static $initialized = false;

            if ($initialized) {
                return;
            }

            if (!isset($container['knp_paginator.options'])) {
                $container['knp_paginator.options'] = array();
            }

            $container['knp_paginator.options'] = array_replace_recursive(
                $container['knp_paginator.default_options'],
                $container['knp_paginator.options']
            );
        });

        $container['knp_paginator'] = function () use ($container) {
            $container['knp_paginator.options.initializer']();

            $paginator = new Paginator($container['dispatcher']);
            $paginator->setDefaultPaginatorOptions(
                $container['knp_paginator.options']['defaultPaginationOptions']
            );

            return $paginator;
        };

        $container['knp_paginator.processor'] = function () use ($container) {
            return new Processor(
                $container['url_generator'],
                $container['translator']
            );
        };

        $container['dispatcher'] = $container->extend('dispatcher', function (EventDispatcherInterface $dispatcher) use ($container) {
            $container['knp_paginator.options.initializer']();

            $slidingPaginationSubscriber = new SlidingPaginationSubscriber(
                $container['knp_paginator.options']['subscriberOptions']
            );

            $dispatcher->addListener('kernel.request', array(
                $slidingPaginationSubscriber, 'onKernelRequest'
            ));

            $dispatcher->addSubscriber(new PaginationSubscriber());
            $dispatcher->addSubscriber(new SortableSubscriber());
            $dispatcher->addSubscriber($slidingPaginationSubscriber);

            return $dispatcher;
        });

        $container['twig'] = $container->extend('twig', function (\Twig_Environment $twig) use ($container) {
            $twig->addExtension(new PaginationExtension($container['knp_paginator.processor']));

            return $twig;
        });

        $container['twig.loader.filesystem'] = $container->extend(
            'twig.loader.filesystem',
            function (\Twig_Loader_Filesystem $twigLoaderFilesystem) {
                $twigLoaderFilesystem->addPath(__DIR__. '/../Resources/views', 'SaxulumPaginationProvider');

                return $twigLoaderFilesystem;
            }
        );
    }
}
