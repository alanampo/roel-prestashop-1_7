<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\DependencyInjection\Tests\Fixtures;

class Bar implements \Symfony\Component\DependencyInjection\Tests\Fixtures\BarInterface
{
    public $quz;
    public function __construct($quz = null, \ps_eventbus_v3_0_7\NonExistent $nonExistent = null, \Symfony\Component\DependencyInjection\Tests\Fixtures\BarInterface $decorated = null, array $foo = [])
    {
        $this->quz = $quz;
    }
    public static function create(\ps_eventbus_v3_0_7\NonExistent $nonExistent = null, $factory = null)
    {
    }
}
