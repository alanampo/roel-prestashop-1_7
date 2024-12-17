<?php

namespace ps_eventbus_v3_0_7\Container14;

use Symfony\Component\DependencyInjection\ContainerBuilder;
/*
 * This file is included in Tests\Dumper\GraphvizDumperTest::testDumpWithFrozenCustomClassContainer
 * and Tests\Dumper\XmlDumperTest::testCompiledContainerCanBeDumped.
 */
if (!\class_exists('ps_eventbus_v3_0_7\\Container14\\ProjectServiceContainer')) {
    class ProjectServiceContainer extends ContainerBuilder
    {
    }
}
return new ProjectServiceContainer();
