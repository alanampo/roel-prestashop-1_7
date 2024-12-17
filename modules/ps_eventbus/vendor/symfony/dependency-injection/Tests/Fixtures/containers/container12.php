<?php

namespace ps_eventbus_v3_0_7;

use Symfony\Component\DependencyInjection\ContainerBuilder;
$container = new ContainerBuilder();
$container->register('foo', 'ps_eventbus_v3_0_7\\FooClass\\Foo')->addArgument('foo<>&bar')->addTag('foo"bar\\bar', ['foo' => 'foo"barřž€'])->setPublic(\true);
return $container;
