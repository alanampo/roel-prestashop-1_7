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

/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @method InstanceofConfigurator instanceof(string $fqcn)
 */
class InstanceofConfigurator extends \Symfony\Component\DependencyInjection\Loader\Configurator\AbstractServiceConfigurator
{
    const FACTORY = 'instanceof';
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ShareTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
    /**
     * Defines an instanceof-conditional to be applied to following service definitions.
     *
     * @param string $fqcn
     *
     * @return self
     */
    protected final function setInstanceof($fqcn)
    {
        return $this->parent->instanceof($fqcn);
    }
}
