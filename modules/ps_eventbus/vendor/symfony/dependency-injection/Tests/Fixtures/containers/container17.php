<?php

namespace ps_eventbus_v3_0_7;

use Symfony\Component\DependencyInjection\ContainerBuilder;
$container = new ContainerBuilder();
$container->register('foo', '%foo.class%')->setPublic(\true);
return $container;
