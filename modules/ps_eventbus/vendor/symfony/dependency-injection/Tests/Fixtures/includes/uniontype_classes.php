<?php

namespace Symfony\Component\DependencyInjection\Tests\Compiler;

class UnionScalars
{
    public function __construct(int|float $timeout)
    {
    }
}
class UnionClasses
{
    public function __construct(\Symfony\Component\DependencyInjection\Tests\Compiler\CollisionA|\Symfony\Component\DependencyInjection\Tests\Compiler\CollisionB $collision)
    {
    }
}
class UnionNull
{
    public function __construct(\Symfony\Component\DependencyInjection\Tests\Compiler\CollisionInterface|null $c)
    {
    }
}
