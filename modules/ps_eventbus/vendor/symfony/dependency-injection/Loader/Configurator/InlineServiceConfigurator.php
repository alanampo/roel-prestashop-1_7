<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Symfony\Component\DependencyInjection\Definition;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class InlineServiceConfigurator extends \Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator
{
    const FACTORY = 'inline';
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ArgumentTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FactoryTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FileTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ParentTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }
}
