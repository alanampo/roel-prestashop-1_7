<?php

namespace Symfony\Component\Config\Tests\Fixtures\Resource;

if (!\class_exists(\Symfony\Component\Config\Tests\Fixtures\Resource\MissingClass::class)) {
    class ConditionalClass
    {
    }
}
