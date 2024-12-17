<?php

namespace ps_eventbus_v3_0_7;

use Symfony\Component\DependencyInjection\ContainerBuilder;
$container = new ContainerBuilder();
$container->register('foo', 'ps_eventbus_v3_0_7\\FooClass\\Foo')->setDecoratedService('bar', 'bar.woozy')->setPublic(\true);
return $container;
