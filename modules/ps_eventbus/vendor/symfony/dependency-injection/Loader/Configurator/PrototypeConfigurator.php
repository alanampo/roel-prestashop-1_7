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
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class PrototypeConfigurator extends \Symfony\Component\DependencyInjection\Loader\Configurator\AbstractServiceConfigurator
{
    const FACTORY = 'load';
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AbstractTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ArgumentTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutoconfigureTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\AutowireTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\BindTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\CallTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ConfiguratorTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\DeprecateTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\FactoryTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\LazyTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ParentTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PropertyTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\PublicTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\ShareTrait;
    use \Symfony\Component\DependencyInjection\Loader\Configurator\Traits\TagTrait;
    private $loader;
    private $resource;
    private $exclude;
    private $allowParent;
    public function __construct(\Symfony\Component\DependencyInjection\Loader\Configurator\ServicesConfigurator $parent, PhpFileLoader $loader, Definition $defaults, $namespace, $resource, $allowParent)
    {
        $definition = new Definition();
        if (!$defaults->isPublic() || !$defaults->isPrivate()) {
            $definition->setPublic($defaults->isPublic());
        }
        $definition->setAutowired($defaults->isAutowired());
        $definition->setAutoconfigured($defaults->isAutoconfigured());
        // deep clone, to avoid multiple process of the same instance in the passes
        $definition->setBindings(\unserialize(\serialize($defaults->getBindings())));
        $definition->setChanges([]);
        $this->loader = $loader;
        $this->resource = $resource;
        $this->allowParent = $allowParent;
        parent::__construct($parent, $definition, $namespace, $defaults->getTags());
    }
    public function __destruct()
    {
        parent::__destruct();
        if ($this->loader) {
            $this->loader->registerClasses($this->definition, $this->id, $this->resource, $this->exclude);
        }
        $this->loader = null;
    }
    /**
     * Excludes files from registration using a glob pattern.
     *
     * @param string $exclude
     *
     * @return $this
     */
    public final function exclude($exclude)
    {
        $this->exclude = $exclude;
        return $this;
    }
}
