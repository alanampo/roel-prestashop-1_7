<?php

namespace Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype;

class Foo implements \Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\FooInterface, \Symfony\Component\DependencyInjection\Tests\Fixtures\Prototype\Sub\BarInterface
{
    public function __construct($bar = null)
    {
    }
    public function setFoo(self $foo)
    {
    }
}
