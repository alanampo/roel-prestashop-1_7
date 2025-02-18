<?php

namespace ps_eventbus_v3_0_7;

require_once __DIR__ . '/../includes/classes.php';
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
$container = new ContainerBuilder();
$container->register('foo', 'FooClass')->addArgument(new Reference('bar'))->setPublic(\true);
return $container;
