<?php

namespace Symfony\Component\DependencyInjection\Tests\Fixtures\Container33;

use Symfony\Component\DependencyInjection\ContainerBuilder;
$container = new ContainerBuilder();
$container->register(\ps_eventbus_v3_0_7\Foo\Foo::class)->setPublic(\true);
$container->register(\ps_eventbus_v3_0_7\Bar\Foo::class)->setPublic(\true);
return $container;
