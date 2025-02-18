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
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\DecoratorServicePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
class DecoratorServicePassTest extends TestCase
{
    public function testProcessWithoutAlias()
    {
        $container = new ContainerBuilder();
        $fooDefinition = $container->register('foo')->setPublic(\false);
        $fooExtendedDefinition = $container->register('foo.extended')->setPublic(\true)->setDecoratedService('foo');
        $barDefinition = $container->register('bar')->setPublic(\true);
        $barExtendedDefinition = $container->register('bar.extended')->setPublic(\true)->setDecoratedService('bar', 'bar.yoo');
        $this->process($container);
        $this->assertEquals('foo.extended', $container->getAlias('foo'));
        $this->assertFalse($container->getAlias('foo')->isPublic());
        $this->assertEquals('bar.extended', $container->getAlias('bar'));
        $this->assertTrue($container->getAlias('bar')->isPublic());
        $this->assertSame($fooDefinition, $container->getDefinition('foo.extended.inner'));
        $this->assertFalse($container->getDefinition('foo.extended.inner')->isPublic());
        $this->assertSame($barDefinition, $container->getDefinition('bar.yoo'));
        $this->assertFalse($container->getDefinition('bar.yoo')->isPublic());
        $this->assertNull($fooExtendedDefinition->getDecoratedService());
        $this->assertNull($barExtendedDefinition->getDecoratedService());
    }
    public function testProcessWithAlias()
    {
        $container = new ContainerBuilder();
        $container->register('foo')->setPublic(\true);
        $container->setAlias('foo.alias', new Alias('foo', \false));
        $fooExtendedDefinition = $container->register('foo.extended')->setPublic(\true)->setDecoratedService('foo.alias');
        $this->process($container);
        $this->assertEquals('foo.extended', $container->getAlias('foo.alias'));
        $this->assertFalse($container->getAlias('foo.alias')->isPublic());
        $this->assertEquals('foo', $container->getAlias('foo.extended.inner'));
        $this->assertFalse($container->getAlias('foo.extended.inner')->isPublic());
        $this->assertNull($fooExtendedDefinition->getDecoratedService());
    }
    public function testProcessWithPriority()
    {
        $container = new ContainerBuilder();
        $fooDefinition = $container->register('foo')->setPublic(\false);
        $barDefinition = $container->register('bar')->setPublic(\true)->setDecoratedService('foo');
        $bazDefinition = $container->register('baz')->setPublic(\true)->setDecoratedService('foo', null, 5);
        $quxDefinition = $container->register('qux')->setPublic(\true)->setDecoratedService('foo', null, 3);
        $this->process($container);
        $this->assertEquals('bar', $container->getAlias('foo'));
        $this->assertFalse($container->getAlias('foo')->isPublic());
        $this->assertSame($fooDefinition, $container->getDefinition('baz.inner'));
        $this->assertFalse($container->getDefinition('baz.inner')->isPublic());
        $this->assertEquals('qux', $container->getAlias('bar.inner'));
        $this->assertFalse($container->getAlias('bar.inner')->isPublic());
        $this->assertEquals('baz', $container->getAlias('qux.inner'));
        $this->assertFalse($container->getAlias('qux.inner')->isPublic());
        $this->assertNull($barDefinition->getDecoratedService());
        $this->assertNull($bazDefinition->getDecoratedService());
        $this->assertNull($quxDefinition->getDecoratedService());
    }
    public function testProcessMovesTagsFromDecoratedDefinitionToDecoratingDefinition()
    {
        $container = new ContainerBuilder();
        $container->register('foo')->setTags(['bar' => ['attr' => 'baz']]);
        $container->register('baz')->setTags(['foobar' => ['attr' => 'bar']])->setDecoratedService('foo');
        $this->process($container);
        $this->assertEmpty($container->getDefinition('baz.inner')->getTags());
        $this->assertEquals(['bar' => ['attr' => 'baz'], 'foobar' => ['attr' => 'bar']], $container->getDefinition('baz')->getTags());
    }
    /**
     * @group legacy
     */
    public function testProcessMergesAutowiringTypesInDecoratingDefinitionAndRemoveThemFromDecoratedDefinition()
    {
        $container = new ContainerBuilder();
        $container->register('parent')->addAutowiringType('Bar');
        $container->register('child')->setDecoratedService('parent')->addAutowiringType('Foo');
        $this->process($container);
        $this->assertEquals(['Bar', 'Foo'], $container->getDefinition('child')->getAutowiringTypes());
        $this->assertEmpty($container->getDefinition('child.inner')->getAutowiringTypes());
    }
    public function testProcessMovesTagsFromDecoratedDefinitionToDecoratingDefinitionMultipleTimes()
    {
        $container = new ContainerBuilder();
        $container->register('foo')->setPublic(\true)->setTags(['bar' => ['attr' => 'baz']]);
        $container->register('deco1')->setDecoratedService('foo', null, 50);
        $container->register('deco2')->setDecoratedService('foo', null, 2);
        $this->process($container);
        $this->assertEmpty($container->getDefinition('deco1')->getTags());
        $this->assertEquals(['bar' => ['attr' => 'baz']], $container->getDefinition('deco2')->getTags());
    }
    protected function process(ContainerBuilder $container)
    {
        $repeatedPass = new DecoratorServicePass();
        $repeatedPass->process($container);
    }
}
