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

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ServiceConfigurator extends \Symfony\Component\DependencyInjection\Loader\Configurator\AbstractServiceConfigurator
{
    const FACTORY = 'services';
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AbstractTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ArgumentTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutoconfigureTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ClassTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\DecorateTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\DeprecateTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FactoryTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FileTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ParentTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ShareTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\SyntheticTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
    private $container;
    private $instanceof;
    private $allowParent;
    public function __construct(ContainerBuilder $container, array $instanceof, $allowParent, \Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator $parent, Definition $definition, $id, array $defaultTags)
    {
        $this->container = $container;
        $this->instanceof = $instanceof;
        $this->allowParent = $allowParent;
        parent::__construct($parent, $definition, $id, $defaultTags);
    }
    public function __destruct()
    {
        parent::__destruct();
        $this->container->removeBindings($this->id);
        if (!$this->definition instanceof ChildDefinition) {
            $this->container->setDefinition($this->id, $this->definition->setInstanceofConditionals($this->instanceof));
        } else {
            $this->container->setDefinition($this->id, $this->definition);
        }
    }
}
