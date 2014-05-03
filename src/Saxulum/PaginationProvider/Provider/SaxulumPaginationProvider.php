<?php

namespace Saxulum\PaginationProvider\Provider;

use Knp\Component\Pager\Event\Subscriber\Paginate\PaginationSubscriber;
use Knp\Component\Pager\Event\Subscriber\Sortable\SortableSubscriber;
use Knp\Component\Pager\Paginator;
use Saxulum\PaginationProvider\Helper\Processor;
use Saxulum\PaginationProvider\Subscriber\SlidingPaginationSubscriber;
use Saxulum\PaginationProvider\Twig\PaginationExtension;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SaxulumPaginationProvider
{
    public function register(\Pimple $container)
    {
        $container['knp_paginator.default_options'] = array(
            'defaultPaginationTemplate' => '@SaxulumPaginationProvider/sliding.html.twig',
            'defaultSortableTemplate' => '@SaxulumPaginationProvider/sortable_link.html.twig',
            'defaultFiltrationTemplate' => '@SaxulumPaginationProvider/filtration.html.twig',
            'defaultPageRange' => 5,
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

        $container['knp_paginator'] = $container->share(function() use($container) {
            return new Paginator($container['dispatcher']);
        });

        $container['knp_paginator.processor'] = $container->share(function() use($container) {
            return new Processor(
                $container['url_generator'],
                $container['translator']
            );
        });

        $container['dispatcher'] = $container->share(
            $container->extend('dispatcher', function(EventDispatcherInterface $dispatcher) use ($container){

                $container['knp_paginator.options.initializer']();

                $slidingPaginationSubscriber = new SlidingPaginationSubscriber(
                    $container['knp_paginator.options']
                );

                $dispatcher->addListener('kernel.request', array(
                    $slidingPaginationSubscriber, 'onKernelRequest'
                ));

                $dispatcher->addSubscriber(new PaginationSubscriber());
                $dispatcher->addSubscriber(new SortableSubscriber());
                $dispatcher->addSubscriber($slidingPaginationSubscriber);

                return $dispatcher;
            })
        );

        $container['twig'] = $container->share(
            $container->extend('twig', function(\Twig_Environment $twig) use ($container){
                $twig->addExtension(new PaginationExtension($container['knp_paginator.processor']));

                return $twig;
            })
        );

        $container['twig.loader.filesystem'] = $container->share($container->extend('twig.loader.filesystem',
            function (\Twig_Loader_Filesystem $twigLoaderFilesystem) {
                $twigLoaderFilesystem->addPath(__DIR__. '/../Resources/views', 'SaxulumPaginationProvider');

                return $twigLoaderFilesystem;
            }
        ));
    }
}