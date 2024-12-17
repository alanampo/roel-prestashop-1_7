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
        $this->normalizedIds = ['ps_eventbus_v3_0_7\\tsantos\\serializer\\serializerinterface' => 'ps_eventbus_v3_0_7\\TSantos\\Serializer\\SerializerInterface'];
        $this->methodMap = ['tsantos_serializer' => 'getTsantosSerializerService'];
        $this->aliases = ['ps_eventbus_v3_0_7\\TSantos\\Serializer\\SerializerInterface' => 'tsantos_serializer'];
    }
    public function getRemovedIds()
    {
        return ['Psr\\Container\\ContainerInterface' => \true, 'Symfony\\Component\\DependencyInjection\\ContainerInterface' => \true];
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
     * Gets the public 'tsantos_serializer' shared service.
     *
     * @return \TSantos\Serializer\EventEmitterSerializer
     */
    protected function getTsantosSerializerService()
    {
        $a = new \ps_eventbus_v3_0_7\TSantos\Serializer\NormalizerRegistry();
        $b = new \ps_eventbus_v3_0_7\TSantos\Serializer\Normalizer\CollectionNormalizer();
        $c = new \ps_eventbus_v3_0_7\TSantos\Serializer\EventDispatcher\EventDispatcher();
        $c->addSubscriber(new \ps_eventbus_v3_0_7\TSantos\SerializerBundle\EventListener\StopwatchListener(new \Symfony\Component\Stopwatch\Stopwatch(\true)));
        $this->services['tsantos_serializer'] = $instance = new \ps_eventbus_v3_0_7\TSantos\Serializer\EventEmitterSerializer(new \ps_eventbus_v3_0_7\TSantos\Serializer\Encoder\JsonEncoder(), $a, $c);
        $b->setSerializer($instance);
        $d = new \ps_eventbus_v3_0_7\TSantos\Serializer\Normalizer\JsonNormalizer();
        $d->setSerializer($instance);
        $a->add(new \ps_eventbus_v3_0_7\TSantos\Serializer\Normalizer\ObjectNormalizer(new \ps_eventbus_v3_0_7\TSantos\SerializerBundle\Serializer\CircularReferenceHandler()));
        $a->add($b);
        $a->add($d);
        return $instance;
    }
}
