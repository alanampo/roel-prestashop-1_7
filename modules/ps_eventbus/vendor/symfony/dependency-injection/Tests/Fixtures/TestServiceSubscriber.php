<?php

namespace Symfony\Component\DependencyInjection\Tests\Fixtures;

use Symfony\Component\DependencyInjection\ServiceSubscriberInterface;
class TestServiceSubscriber implements ServiceSubscriberInterface
{
    public function __construct($container)
    {
    }
    public static function getSubscribedServices()
    {
        return [__CLASS__, '?' . \Symfony\Component\DependencyInjection\Tests\Fixtures\CustomDefinition::class, 'bar' => \Symfony\Component\DependencyInjection\Tests\Fixtures\CustomDefinition::class, 'baz' => '?' . \Symfony\Component\DependencyInjection\Tests\Fixtures\CustomDefinition::class];
    }
}
