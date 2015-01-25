# saxulum-pagination-provider

**works with plain silex-php**

[![Build Status](https://api.travis-ci.org/saxulum/saxulum-pagination-provider.png?branch=master)](https://travis-ci.org/saxulum/saxulum-pagination-provider)
[![Total Downloads](https://poser.pugx.org/saxulum/saxulum-pagination-provider/downloads.png)](https://packagist.org/packages/saxulum/saxulum-pagination-provider)
[![Latest Stable Version](https://poser.pugx.org/saxulum/saxulum-pagination-provider/v/stable.png)](https://packagist.org/packages/saxulum/saxulum-pagination-provider)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/saxulum/saxulum-pagination-provider/badges/quality-score.png?s=6539e5892cc965ef82ac8ec929442c544a4e02a5)](https://scrutinizer-ci.com/g/saxulum/saxulum-pagination-provider/)

## Features

 * Does not require initializing specific adapters
 * Can be customized in any way needed, etc.: pagination view, event subscribers.
 * Possibility to add custom filtering, sorting functionality depending on request parameters.
 * Separation of concerns, paginator is responsible for generating the pagination view only, pagination view  * for representation purposes.

## Requirements

 * php: >=5.3.3,
 * knplabs/knp-components: ~1.2,>=1.2.5,
 * pimple/pimple: >=2.1,<4,
 * symfony/http-kernel: ~2.3,
 * symfony/translation: ~2.3,
 * twig/twig: ~1.2


## Installation

Through [Composer](http://getcomposer.org) as [saxulum/saxulum-pagination-provider][1].

## Configuration

```{.php}
$container->register(new Saxulum\PaginationProvider\Silex\Provider\SaxulumPaginationProvider, array(
    'knp_paginator.options' => array(
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
    )
));
```

## Usage

```{.php}
$container['knp_paginator']->paginate($target, 1, 10);
```

[1]: https://packagist.org/packages/saxulum/saxulum-pagination-provider