<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
/**
 * Compiler Pass Configuration.
 *
 * This class has a default configuration embedded.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class PassConfig
{
    const TYPE_AFTER_REMOVING = 'afterRemoving';
    const TYPE_BEFORE_OPTIMIZATION = 'beforeOptimization';
    const TYPE_BEFORE_REMOVING = 'beforeRemoving';
    const TYPE_OPTIMIZE = 'optimization';
    const TYPE_REMOVE = 'removing';
    private $mergePass;
    private $afterRemovingPasses = [];
    private $beforeOptimizationPasses = [];
    private $beforeRemovingPasses = [];
    private $optimizationPasses;
    private $removingPasses;
    public function __construct()
    {
        $this->mergePass = new \Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass();
        $this->beforeOptimizationPasses = [100 => [$resolveClassPass = new \Symfony\Component\DependencyInjection\Compiler\ResolveClassPass(), new \Symfony\Component\DependencyInjection\Compiler\ResolveInstanceofConditionalsPass(), new \Symfony\Component\DependencyInjection\Compiler\RegisterEnvVarProcessorsPass()], -1000 => [new \Symfony\Component\DependencyInjection\Compiler\ExtensionCompilerPass()]];
        $this->optimizationPasses = [[new \Symfony\Component\DependencyInjection\Compiler\ResolveChildDefinitionsPass(), new \Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass(), new \Symfony\Component\DependencyInjection\Compiler\RegisterServiceSubscribersPass(), new \Symfony\Component\DependencyInjection\Compiler\DecoratorServicePass(), new \Symfony\Component\DependencyInjection\Compiler\ResolveParameterPlaceHoldersPass(\false, \false), new \Symfony\Component\DependencyInjection\Compiler\ResolveFactoryClassPass(), new \Symfony\Component\DependencyInjection\Compiler\FactoryReturnTypePass($resolveClassPass), new \Symfony\Component\DependencyInjection\Compiler\CheckDefinitionValidityPass(), new \Symfony\Component\DependencyInjection\Compiler\ResolveNamedArgumentsPass(), new \Symfony\Component\DependencyInjection\Compiler\AutowireRequiredMethodsPass(), new \Symfony\Component\DependencyInjection\Compiler\ResolveBindingsPass(), new \Symfony\Component\DependencyInjection\Compiler\AutowirePass(\false), new \Symfony\Component\DependencyInjection\Compiler\ResolveTaggedIteratorArgumentPass(), new \Symfony\Component\DependencyInjection\Compiler\ResolveServiceSubscribersPass(), new \Symfony\Component\DependencyInjection\Compiler\ResolveReferencesToAliasesPass(), new \Symfony\Component\DependencyInjection\Compiler\ResolveInvalidReferencesPass(), new \Symfony\Component\DependencyInjection\Compiler\AnalyzeServiceReferencesPass(\true), new \Symfony\Component\DependencyInjection\Compiler\CheckCircularReferencesPass(), new \Symfony\Component\DependencyInjection\Compiler\CheckReferenceValidityPass(), new \Symfony\Component\DependencyInjection\Compiler\CheckArgumentsValidityPass(\false)]];
        $this->beforeRemovingPasses = [-100 => [new \Symfony\Component\DependencyInjection\Compiler\ResolvePrivatesPass()]];
        $this->removingPasses = [[new \Symfony\Component\DependencyInjection\Compiler\RemovePrivateAliasesPass(), new \Symfony\Component\DependencyInjection\Compiler\ReplaceAliasByActualDefinitionPass(), new \Symfony\Component\DependencyInjection\Compiler\RemoveAbstractDefinitionsPass(), new \Symfony\Component\DependencyInjection\Compiler\RepeatedPass([new \Symfony\Component\DependencyInjection\Compiler\AnalyzeServiceReferencesPass(), new \Symfony\Component\DependencyInjection\Compiler\InlineServiceDefinitionsPass(), new \Symfony\Component\DependencyInjection\Compiler\AnalyzeServiceReferencesPass(), new \Symfony\Component\DependencyInjection\Compiler\RemoveUnusedDefinitionsPass()]), new \Symfony\Component\DependencyInjection\Compiler\DefinitionErrorExceptionPass(), new \Symfony\Component\DependencyInjection\Compiler\CheckExceptionOnInvalidReferenceBehaviorPass(), new \Symfony\Component\DependencyInjection\Compiler\ResolveHotPathPass()]];
    }
    /**
     * Returns all passes in order to be processed.
     *
     * @return CompilerPassInterface[]
     */
    public function getPasses()
    {
        return \array_merge([$this->mergePass], $this->getBeforeOptimizationPasses(), $this->getOptimizationPasses(), $this->getBeforeRemovingPasses(), $this->getRemovingPasses(), $this->getAfterRemovingPasses());
    }
    /**
     * Adds a pass.
     *
     * @param CompilerPassInterface $pass A Compiler pass
     * @param string                $type The pass type
     *
     * @throws InvalidArgumentException when a pass type doesn't exist
     */
    public function addPass(\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface $pass, $type = self::TYPE_BEFORE_OPTIMIZATION)
    {
        if (\func_num_args() >= 3) {
            $priority = \func_get_arg(2);
        } else {
            if (__CLASS__ !== static::class) {
                $r = new \ReflectionMethod($this, __FUNCTION__);
                if (__CLASS__ !== $r->getDeclaringClass()->getName()) {
                    @\trigger_error(\sprintf('Method %s() will have a third `int $priority = 0` argument in version 4.0. Not defining it is deprecated since Symfony 3.2.', __METHOD__), \E_USER_DEPRECATED);
                }
            }
            $priority = 0;
        }
        $property = $type . 'Passes';
        if (!isset($this->{$property})) {
            throw new InvalidArgumentException(\sprintf('Invalid type "%s".', $type));
        }
        $passes =& $this->{$property};
        if (!isset($passes[$priority])) {
            $passes[$priority] = [];
        }
        $passes[$priority][] = $pass;
    }
    /**
     * Gets all passes for the AfterRemoving pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getAfterRemovingPasses()
    {
        return $this->sortPasses($this->afterRemovingPasses);
    }
    /**
     * Gets all passes for the BeforeOptimization pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getBeforeOptimizationPasses()
    {
        return $this->sortPasses($this->beforeOptimizationPasses);
    }
    /**
     * Gets all passes for the BeforeRemoving pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getBeforeRemovingPasses()
    {
        return $this->sortPasses($this->beforeRemovingPasses);
    }
    /**
     * Gets all passes for the Optimization pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getOptimizationPasses()
    {
        return $this->sortPasses($this->optimizationPasses);
    }
    /**
     * Gets all passes for the Removing pass.
     *
     * @return CompilerPassInterface[]
     */
    public function getRemovingPasses()
    {
        return $this->sortPasses($this->removingPasses);
    }
    /**
     * Gets the Merge pass.
     *
     * @return CompilerPassInterface
     */
    public function getMergePass()
    {
        return $this->mergePass;
    }
    public function setMergePass(\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface $pass)
    {
        $this->mergePass = $pass;
    }
    /**
     * Sets the AfterRemoving passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setAfterRemovingPasses(array $passes)
    {
        $this->afterRemovingPasses = [$passes];
    }
    /**
     * Sets the BeforeOptimization passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setBeforeOptimizationPasses(array $passes)
    {
        $this->beforeOptimizationPasses = [$passes];
    }
    /**
     * Sets the BeforeRemoving passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setBeforeRemovingPasses(array $passes)
    {
        $this->beforeRemovingPasses = [$passes];
    }
    /**
     * Sets the Optimization passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setOptimizationPasses(array $passes)
    {
        $this->optimizationPasses = [$passes];
    }
    /**
     * Sets the Removing passes.
     *
     * @param CompilerPassInterface[] $passes
     */
    public function setRemovingPasses(array $passes)
    {
        $this->removingPasses = [$passes];
    }
    /**
     * Sort passes by priority.
     *
     * @param array $passes CompilerPassInterface instances with their priority as key
     *
     * @return CompilerPassInterface[]
     */
    private function sortPasses(array $passes)
    {
        if (0 === \count($passes)) {
            return [];
        }
        \krsort($passes);
        // Flatten the array
        return \call_user_func_array('ps_eventbus_v3_0_7\\array_merge', $passes);
    }
}
