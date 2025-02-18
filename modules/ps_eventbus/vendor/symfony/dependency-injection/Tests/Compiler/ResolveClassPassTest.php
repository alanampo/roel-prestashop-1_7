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
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Compiler\ResolveClassPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Tests\Fixtures\CaseSensitiveClass;
class ResolveClassPassTest extends TestCase
{
    /**
     * @dataProvider provideValidClassId
     */
    public function testResolveClassFromId($serviceId)
    {
        $container = new ContainerBuilder();
        $def = $container->register($serviceId);
        (new ResolveClassPass())->process($container);
        $this->assertSame($serviceId, $def->getClass());
    }
    public function provideValidClassId()
    {
        (yield ['ps_eventbus_v3_0_7\\Acme\\UnknownClass']);
        (yield [CaseSensitiveClass::class]);
    }
    /**
     * @dataProvider provideInvalidClassId
     */
    public function testWontResolveClassFromId($serviceId)
    {
        $container = new ContainerBuilder();
        $def = $container->register($serviceId);
        (new ResolveClassPass())->process($container);
        $this->assertNull($def->getClass());
    }
    public function provideInvalidClassId()
    {
        (yield [\stdClass::class]);
        (yield ['bar']);
        (yield ['\\DateTime']);
    }
    public function testNonFqcnChildDefinition()
    {
        $container = new ContainerBuilder();
        $parent = $container->register('ps_eventbus_v3_0_7\\App\\Foo', null);
        $child = $container->setDefinition('App\\Foo.child', new ChildDefinition('ps_eventbus_v3_0_7\\App\\Foo'));
        (new ResolveClassPass())->process($container);
        $this->assertSame('ps_eventbus_v3_0_7\\App\\Foo', $parent->getClass());
        $this->assertNull($child->getClass());
    }
    public function testClassFoundChildDefinition()
    {
        $container = new ContainerBuilder();
        $parent = $container->register('ps_eventbus_v3_0_7\\App\\Foo', null);
        $child = $container->setDefinition(self::class, new ChildDefinition('ps_eventbus_v3_0_7\\App\\Foo'));
        (new ResolveClassPass())->process($container);
        $this->assertSame('ps_eventbus_v3_0_7\\App\\Foo', $parent->getClass());
        $this->assertSame(self::class, $child->getClass());
    }
    public function testAmbiguousChildDefinition()
    {
        $this->expectException('Symfony\\Component\\DependencyInjection\\Exception\\InvalidArgumentException');
        $this->expectExceptionMessage('Service definition "App\\Foo\\Child" has a parent but no class, and its name looks like a FQCN. Either the class is missing or you want to inherit it from the parent service. To resolve this ambiguity, please rename this service to a non-FQCN (e.g. using dots), or create the missing class.');
        $container = new ContainerBuilder();
        $container->register('ps_eventbus_v3_0_7\\App\\Foo', null);
        $container->setDefinition('ps_eventbus_v3_0_7\\App\\Foo\\Child', new ChildDefinition('ps_eventbus_v3_0_7\\App\\Foo'));
        (new ResolveClassPass())->process($container);
    }
}
