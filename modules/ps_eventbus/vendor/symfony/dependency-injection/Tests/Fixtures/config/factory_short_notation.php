<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $c) {
    $c->services()->set('service', \stdClass::class)->factory('factory:method');
};
