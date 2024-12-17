<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\DependencyInjection\Tests\Extension;

use ps_eventbus_v3_0_7\PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
class ExtensionTest extends TestCase
{
    /**
     * @dataProvider getResolvedEnabledFixtures
     */
    public function testIsConfigEnabledReturnsTheResolvedValue($enabled)
    {
        $extension = new \Symfony\Component\DependencyInjection\Tests\Extension\EnableableExtension();
        $this->assertSame($enabled, $extension->isConfigEnabled(new ContainerBuilder(), ['enabled' => $enabled]));
    }
    public function getResolvedEnabledFixtures()
    {
        return [[\true], [\false]];
    }
    public function testIsConfigEnabledOnNonEnableableConfig()
    {
        $this->expectException('Symfony\\Component\\DependencyInjection\\Exception\\InvalidArgumentException');
        $this->expectExceptionMessage('The config array has no \'enabled\' key.');
        $extension = new \Symfony\Component\DependencyInjection\Tests\Extension\EnableableExtension();
        $extension->isConfigEnabled(new ContainerBuilder(), []);
    }
}
class EnableableExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
    }
    public function isConfigEnabled(ContainerBuilder $container, array $config)
    {
        return parent::isConfigEnabled($container, $config);
    }
}
