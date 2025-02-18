<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use ps_eventbus_v3_0_7\App\FooService;
use Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype;
return function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $c) {
    $s = $c->services();
    $s->instanceof(Prototype\Foo::class)->property('p', 0)->call('setFoo', [ref('foo')])->tag('tag', ['k' => 'v'])->share(\false)->lazy()->configurator('c')->property('p', 1);
    $s->load(Prototype::class . '\\', '../Prototype')->exclude('../Prototype/*/*');
    $s->set('foo', FooService::class);
};
