<?php

namespace ps_eventbus_v3_0_7;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
$container = new ContainerBuilder();
$container->register('foo', 'FooClass')->addArgument(new Definition('BarClass', [new Definition('BazClass')]))->setPublic(\true);
return $container;
