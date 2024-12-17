<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use ps_eventbus_v3_0_7\App\BarService;
return function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $c) {
    $s = $c->services();
    $s->set(BarService::class)->args([inline('FooClass')]);
};
