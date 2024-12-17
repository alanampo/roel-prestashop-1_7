<?php

namespace ps_eventbus_v3_0_7;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
$container = new ContainerBuilder();
$container->register('foo', 'FooClass')->addArgument(new Reference('bar'))->setPublic(\true);
$container->register('bar', 'BarClass')->setPublic(\true);
$container->compile();
return $container;
