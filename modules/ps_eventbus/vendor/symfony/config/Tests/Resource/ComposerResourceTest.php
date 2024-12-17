<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Config\Tests\Resource;

use ps_eventbus_v3_0_7\Composer\Autoload\ClassLoader;
use ps_eventbus_v3_0_7\PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Resource\ComposerResource;
class ComposerResourceTest extends TestCase
{
    public function testGetVendor()
    {
        $res = new ComposerResource();
        $r = new \ReflectionClass(ClassLoader::class);
        $found = \false;
        foreach ($res->getVendors() as $vendor) {
            if ($vendor && 0 === \strpos($r->getFileName(), $vendor)) {
                $found = \true;
                break;
            }
        }
        $this->assertTrue($found);
    }
    public function testSerializeUnserialize()
    {
        $res = new ComposerResource();
        $ser = \unserialize(\serialize($res));
        $this->assertTrue($res->isFresh(0));
        $this->assertTrue($ser->isFresh(0));
        $this->assertEquals($res, $ser);
    }
}
