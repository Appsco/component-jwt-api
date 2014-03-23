<?php

namespace BWC\Component\JwtApi\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class MethodHandlerCompilerPass implements CompilerPassInterface
{
    /** @var  string */
    protected $methodHandlerServiceId;

    /** @var string  */
    protected $methodServiceTagId;

    /** @var  string */
    protected $constraintProvideTagId;

    /** @var  string */
    protected $constraintRequireTagId;

    /** @var string  */
    protected $compositeClass;


    /**
     * @param string $methodHandlerServiceId
     * @param string $methodServiceTagId
     * @param string $constraintProvideTagId
     * @param string $constraintRequireTagId
     * @param string $compositeClass
     */
    public function __construct(
            $methodHandlerServiceId = 'jwt.handler.method',
            $methodServiceTagId = 'jwt.handler.method',
            $constraintProvideTagId = 'jwt.handler.method.constraint.provide',
            $constraintRequireTagId = 'jwt.handler.method.constraint.require',
            $compositeClass = 'BWC\Component\JwtApi\Method\CompositeMethod'
    ) {
        $this->methodHandlerServiceId = $methodHandlerServiceId;
        $this->methodServiceTagId = $methodServiceTagId;
        $this->constraintProvideTagId = $constraintProvideTagId;
        $this->constraintRequireTagId = $constraintRequireTagId;
        $this->compositeClass = $compositeClass;
    }




    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->methodHandlerServiceId)) {
            return;
        }

        $constraintProviders = $this->findConstraintProviders($container);

        $methodHandlerService = $container->getDefinition($this->methodHandlerServiceId);

        $methodHandlerService->addMethodCall('setEventDispatcher', array($container->getDefinition('event_dispatcher')));

        $taggedServices = $container->findTaggedServiceIds($this->methodServiceTagId);

        foreach ($taggedServices as $id => $attributes) {
            $methodService = $container->getDefinition($id);
            if ($methodService->hasTag($this->constraintRequireTagId)) {
                $requiredConstraints = array();
                $arr = $methodService->getTag($this->constraintRequireTagId);
                foreach ($arr as $attr) {
                    $name = @$attr['constraint'];
                    if (!$name) {
                        throw new InvalidConfigurationException("Service '%s' did not specified required constraint provider name", $id);
                    }

                    if (!isset($constraintProviders[$name])) {
                        throw new InvalidConfigurationException("Service '%s' requires non existing constraint provider '%s'", $id, $name);
                    }

                    $requiredConstraints[$name] = $name;
                }

                $constrainedServiceId = $id.'.constrained';
                $constrainedService = new Definition($this->compositeClass);
                $container->setDefinition($constrainedServiceId, $constrainedService);

                foreach ($requiredConstraints as $name) {
                    foreach ($constraintProviders[$name] as $constraintProviderServiceId) {
                        $constrainedService->addMethodCall('addMethod', array(new Reference($constraintProviderServiceId)));
                    }
                }

                $constrainedService->addMethodCall('addMethod', array(new Reference($id)));

                $originalId = $id;
                $id = $constrainedServiceId;

            } // if requires constraints

            foreach ($attributes as $attr) {
                $methodHandlerService->addMethodCall('addMethod',
                        array(
                                $attr['method'],
                                new Reference($id)
                        )
                );
            }

        }
    }


    /**
     * @param ContainerBuilder $container
     * @return array
     * @throws InvalidConfigurationException
     */
    protected function findConstraintProviders(ContainerBuilder $container)
    {
        $constraintProviders = array();

        $taggedServices = $container->findTaggedServiceIds($this->constraintProvideTagId);

        foreach ($taggedServices as $id=>$attributes) {
            foreach ($attributes as $attr) {
                $name = @$attr['constraint'];
                if (!$name) {
                    throw new InvalidConfigurationException(sprintf("Missing name attribute of jwt method constraint provider '%s'", $id));
                }


                if (!isset($constraintProviders[$name])) {
                    $constraintProviders[$name] = array();
                }

                $constraintProviders[$name][] = $id;
            }
        }

        return $constraintProviders;
    }

}
