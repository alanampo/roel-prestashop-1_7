<?php

namespace ps_eventbus_v3_0_7;

use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\FrozenParameterBag;
/**
 * This class has been auto-generated
 * by the Symfony Dependency Injection Component.
 *
 * @final since Symfony 3.3
 */
class ProjectServiceContainer extends Container
{
    private $parameters = [];
    private $targetDirs = [];
    public function __construct()
    {
        $this->services = [];
        $this->normalizedIds = ['ps_eventbus_v3_0_7\\app\\bus' => 'ps_eventbus_v3_0_7\\App\\Bus', 'ps_eventbus_v3_0_7\\app\\db' => 'ps_eventbus_v3_0_7\\App\\Db', 'ps_eventbus_v3_0_7\\app\\handler1' => 'ps_eventbus_v3_0_7\\App\\Handler1', 'ps_eventbus_v3_0_7\\app\\handler2' => 'ps_eventbus_v3_0_7\\App\\Handler2', 'ps_eventbus_v3_0_7\\app\\processor' => 'ps_eventbus_v3_0_7\\App\\Processor', 'ps_eventbus_v3_0_7\\app\\registry' => 'ps_eventbus_v3_0_7\\App\\Registry', 'ps_eventbus_v3_0_7\\app\\schema' => 'ps_eventbus_v3_0_7\\App\\Schema'];
        $this->methodMap = ['ps_eventbus_v3_0_7\\App\\Bus' => 'getBusService', 'ps_eventbus_v3_0_7\\App\\Db' => 'getDbService', 'ps_eventbus_v3_0_7\\App\\Handler1' => 'getHandler1Service', 'ps_eventbus_v3_0_7\\App\\Handler2' => 'getHandler2Service', 'ps_eventbus_v3_0_7\\App\\Processor' => 'getProcessorService', 'ps_eventbus_v3_0_7\\App\\Registry' => 'getRegistryService', 'ps_eventbus_v3_0_7\\App\\Schema' => 'getSchemaService'];
        $this->privates = ['ps_eventbus_v3_0_7\\App\\Handler1' => \true, 'ps_eventbus_v3_0_7\\App\\Handler2' => \true, 'ps_eventbus_v3_0_7\\App\\Processor' => \true, 'ps_eventbus_v3_0_7\\App\\Registry' => \true, 'ps_eventbus_v3_0_7\\App\\Schema' => \true];
        $this->aliases = [];
    }
    public function getRemovedIds()
    {
        return ['ps_eventbus_v3_0_7\\App\\Handler1' => \true, 'ps_eventbus_v3_0_7\\App\\Handler2' => \true, 'ps_eventbus_v3_0_7\\App\\Processor' => \true, 'ps_eventbus_v3_0_7\\App\\Registry' => \true, 'ps_eventbus_v3_0_7\\App\\Schema' => \true, 'Psr\\Container\\ContainerInterface' => \true, 'Symfony\\Component\\DependencyInjection\\ContainerInterface' => \true];
    }
    public function compile()
    {
        throw new LogicException('You cannot compile a dumped container that was already compiled.');
    }
    public function isCompiled()
    {
        return \true;
    }
    public function isFrozen()
    {
        @\trigger_error(\sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Use the isCompiled() method instead.', __METHOD__), \E_USER_DEPRECATED);
        return \true;
    }
    /**
     * Gets the public 'App\Bus' shared service.
     *
     * @return \App\Bus
     */
    protected function getBusService()
    {
        $this->services['App\\Bus'] = $instance = new \ps_eventbus_v3_0_7\App\Bus(${($_ = isset($this->services['App\\Db']) ? $this->services['App\\Db'] : $this->getDbService()) && \false ?: '_'});
        $instance->handler1 = ${($_ = isset($this->services['App\\Handler1']) ? $this->services['App\\Handler1'] : $this->getHandler1Service()) && \false ?: '_'};
        $instance->handler2 = ${($_ = isset($this->services['App\\Handler2']) ? $this->services['App\\Handler2'] : $this->getHandler2Service()) && \false ?: '_'};
        return $instance;
    }
    /**
     * Gets the public 'App\Db' shared service.
     *
     * @return \App\Db
     */
    protected function getDbService()
    {
        $this->services['App\\Db'] = $instance = new \ps_eventbus_v3_0_7\App\Db();
        $instance->schema = ${($_ = isset($this->services['App\\Schema']) ? $this->services['App\\Schema'] : $this->getSchemaService()) && \false ?: '_'};
        return $instance;
    }
    /**
     * Gets the private 'App\Handler1' shared service.
     *
     * @return \App\Handler1
     */
    protected function getHandler1Service()
    {
        $a = ${($_ = isset($this->services['App\\Processor']) ? $this->services['App\\Processor'] : $this->getProcessorService()) && \false ?: '_'};
        if (isset($this->services['App\\Handler1'])) {
            return $this->services['App\\Handler1'];
        }
        return $this->services['App\\Handler1'] = new \ps_eventbus_v3_0_7\App\Handler1(${($_ = isset($this->services['App\\Db']) ? $this->services['App\\Db'] : $this->getDbService()) && \false ?: '_'}, ${($_ = isset($this->services['App\\Schema']) ? $this->services['App\\Schema'] : $this->getSchemaService()) && \false ?: '_'}, $a);
    }
    /**
     * Gets the private 'App\Handler2' shared service.
     *
     * @return \App\Handler2
     */
    protected function getHandler2Service()
    {
        $a = ${($_ = isset($this->services['App\\Processor']) ? $this->services['App\\Processor'] : $this->getProcessorService()) && \false ?: '_'};
        if (isset($this->services['App\\Handler2'])) {
            return $this->services['App\\Handler2'];
        }
        return $this->services['App\\Handler2'] = new \ps_eventbus_v3_0_7\App\Handler2(${($_ = isset($this->services['App\\Db']) ? $this->services['App\\Db'] : $this->getDbService()) && \false ?: '_'}, ${($_ = isset($this->services['App\\Schema']) ? $this->services['App\\Schema'] : $this->getSchemaService()) && \false ?: '_'}, $a);
    }
    /**
     * Gets the private 'App\Processor' shared service.
     *
     * @return \App\Processor
     */
    protected function getProcessorService()
    {
        $a = ${($_ = isset($this->services['App\\Registry']) ? $this->services['App\\Registry'] : $this->getRegistryService()) && \false ?: '_'};
        if (isset($this->services['App\\Processor'])) {
            return $this->services['App\\Processor'];
        }
        return $this->services['App\\Processor'] = new \ps_eventbus_v3_0_7\App\Processor($a, ${($_ = isset($this->services['App\\Db']) ? $this->services['App\\Db'] : $this->getDbService()) && \false ?: '_'});
    }
    /**
     * Gets the private 'App\Registry' shared service.
     *
     * @return \App\Registry
     */
    protected function getRegistryService()
    {
        $this->services['App\\Registry'] = $instance = new \ps_eventbus_v3_0_7\App\Registry();
        $instance->processor = [0 => ${($_ = isset($this->services['App\\Db']) ? $this->services['App\\Db'] : $this->getDbService()) && \false ?: '_'}, 1 => ${($_ = isset($this->services['App\\Bus']) ? $this->services['App\\Bus'] : $this->getBusService()) && \false ?: '_'}];
        return $instance;
    }
    /**
     * Gets the private 'App\Schema' shared service.
     *
     * @return \App\Schema
     */
    protected function getSchemaService()
    {
        $a = ${($_ = isset($this->services['App\\Db']) ? $this->services['App\\Db'] : $this->getDbService()) && \false ?: '_'};
        if (isset($this->services['App\\Schema'])) {
            return $this->services['App\\Schema'];
        }
        return $this->services['App\\Schema'] = new \ps_eventbus_v3_0_7\App\Schema($a);
    }
}
