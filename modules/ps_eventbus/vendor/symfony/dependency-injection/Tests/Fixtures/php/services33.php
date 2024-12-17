<?php

namespace ps_eventbus_v3_0_7;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
/**
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 *
 * @final since Symfony 3.3
 */
class ProjectServiceContainer extends Container
{
    private $parameters = [];
    private $targetDirs = [];
    public function __construct()
    {
        $this->services = [];
        $this->normalizedIds = ['ps_eventbus_v3_0_7\\bar\\foo' => 'ps_eventbus_v3_0_7\\Bar\\Foo', 'ps_eventbus_v3_0_7\\foo\\foo' => 'ps_eventbus_v3_0_7\\Foo\\Foo'];
        $this->methodMap = ['ps_eventbus_v3_0_7\\Bar\\Foo' => 'getFooService', 'ps_eventbus_v3_0_7\\Foo\\Foo' => 'getFoo2Service'];
        $this->aliases = [];
    }
    public function getRemovedIds()
    {
        return ['Psr\\Container\\ContainerInterface' => \true, 'Symfony\\Component\\DependencyInjection\\ContainerInterface' => \true];
    }
    public function compile()
    {
        throw new LogicException('You cannot compile a dumped container that was already compiled.');
    }
    public function isCompiled()
    {
        return \true;
    }
    public function isFrozen()
    {
        @\trigger_error(\sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Use the isCompiled() method instead.', __METHOD__), \E_USER_DEPRECATED);
        return \true;
    }
    /**
     * Gets the public 'Bar\Foo' shared service.
     *
     * @return \Bar\Foo
     */
    protected function getFooService()
    {
        return $this->services['Bar\\Foo'] = new \ps_eventbus_v3_0_7\Bar\Foo();
    }
    /**
     * Gets the public 'Foo\Foo' shared service.
     *
     * @return \Foo\Foo
     */
    protected function getFoo2Service()
    {
        return $this->services['Foo\\Foo'] = new \ps_eventbus_v3_0_7\Foo\Foo();
    }
}
