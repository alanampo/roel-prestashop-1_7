<?php

namespace Symfony\Component\DependencyInjection\Tests\Fixtures;

/**
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
class NamedArgumentsDummy
{
    public function __construct(\Symfony\Component\DependencyInjection\Tests\Fixtures\CaseSensitiveClass $c, $apiKey, $hostName)
    {
    }
    public function setApiKey($apiKey)
    {
    }
    public function setSensitiveClass(\Symfony\Component\DependencyInjection\Tests\Fixtures\CaseSensitiveClass $c)
    {
    }
}
