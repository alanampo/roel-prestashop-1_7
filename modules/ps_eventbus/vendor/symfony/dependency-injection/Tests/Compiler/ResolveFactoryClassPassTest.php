<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\DependencyInjection\Tests\Compiler;

use ps_eventbus_v3_0_7\PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Compiler\ResolveFactoryClassPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
class ResolveFactoryClassPassTest extends TestCase
{
    public function testProcess()
    {
        $container = new ContainerBuilder();
        $factory = $container->register('factory', 'ps_eventbus_v3_0_7\\Foo\\Bar');
        $factory->setFactory([null, 'create']);
        $pass = new ResolveFactoryClassPass();
        $pass->process($container);
        $this->assertSame(['ps_eventbus_v3_0_7\\Foo\\Bar', 'create'], $factory->getFactory());
    }
    public function testInlinedDefinitionFactoryIsProcessed()
    {
        $container = new ContainerBuilder();
        $factory = $container->register('factory');
        $factory->setFactory([(new Definition('ps_eventbus_v3_0_7\\Baz\\Qux'))->setFactory([null, 'getInstance']), 'create']);
        $pass = new ResolveFactoryClassPass();
        $pass->process($container);
        $this->assertSame(['ps_eventbus_v3_0_7\\Baz\\Qux', 'getInstance'], $factory->getFactory()[0]->getFactory());
    }
    public function provideFulfilledFactories()
    {
        return [[['ps_eventbus_v3_0_7\\Foo\\Bar', 'create']], [[new Reference('foo'), 'create']], [[new Definition('Baz'), 'create']]];
    }
    /**
     * @dataProvider provideFulfilledFactories
     */
    public function testIgnoresFulfilledFactories($factory)
    {
        $container = new ContainerBuilder();
        $definition = new Definition();
        $definition->setFactory($factory);
        $container->setDefinition('factory', $definition);
        $pass = new ResolveFactoryClassPass();
        $pass->process($container);
        $this->assertSame($factory, $container->getDefinition('factory')->getFactory());
    }
    public function testNotAnyClassThrowsException()
    {
        $this->expectException('Symfony\\Component\\DependencyInjection\\Exception\\RuntimeException');
        $this->expectExceptionMessage('The "factory" service is defined to be created by a factory, but is missing the factory class. Did you forget to define the factory or service class?');
        $container = new ContainerBuilder();
        $factory = $container->register('factory');
        $factory->setFactory([null, 'create']);
        $pass = new ResolveFactoryClassPass();
        $pass->process($container);
    }
}
