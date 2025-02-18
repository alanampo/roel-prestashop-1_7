<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use ps_eventbus_v3_0_7\App\BarService;
return function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $c) {
    $c->services()->set('bar', 'Class1')->set(BarService::class)->abstract(\true)->lazy()->set('foo')->parent(BarService::class)->decorate('bar', 'b', 1)->args([ref('b')])->class('Class2')->file('file.php')->parent('bar')->parent(BarService::class);
};
