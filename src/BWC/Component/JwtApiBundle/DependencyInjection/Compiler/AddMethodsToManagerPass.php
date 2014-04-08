<?php

namespace BWC\Component\JwtApiBundle\DependencyInjection\Compiler;

use BWC\Component\JwtApiBundle\Method\Directions;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;


class AddMethodsToManagerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $arrAllTaggedMethods = $this->findAllMethods($container);

        $arrDecorators = $this->findAllDecorators($container);

        foreach ($arrAllTaggedMethods as $method=>$arr) {
            foreach ($arr as $direction=>$id) {
                $this->buildSingleMethod($method, $direction, $id, $container, $arrDecorators);
            }
        }



//        var_dump($container->getDefinition('bwc_component_jwt_api.jwt_manager'));
//
//        var_dump($container->getDefinition('bwc_component_jwt_api.method.filter.profile-info.composite'));
//
//        var_dump($container->getDefinition('appsco_my_ket_api.jwt.decorator.instance_shared_or_owned_by_profile'));
//
//        var_dump($container->getDefinition('bwc_component_jwt_api.method.filter.profile-info'));

    }

    /**
     * @param ContainerBuilder $container
     * @return array    method => direction => service_id
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    protected function findAllMethods(ContainerBuilder $container)
    {
        $arrTaggedMethods = $container->findTaggedServiceIds('bwc_component_jwt_api.method');

        $arrMethods = array();
        foreach ($arrTaggedMethods as $id=>$attributes) {
            if (count($attributes) > 1) {
                throw new InvalidConfigurationException(
                    sprintf("Service '%s' has more then one bwc_component_jwt_api.method tag", $id)
                );
            }

            $attr = array_shift($attributes);
            if (!isset($attr['method'])) {
                throw new InvalidConfigurationException(
                    sprintf("Service '%s' missing method attribute in bwc_component_jwt_api.method tag", $id)
                );
            }
            if (!isset($attr['direction'])) {
                throw new InvalidConfigurationException(
                    sprintf("Service '%s' missing direction attribute in bwc_component_jwt_api.method tag", $id)
                );
            }
            if (!Directions::isValid($attr['direction'])) {
                throw new InvalidConfigurationException(
                    sprintf("Service '%s' has invalid direction attribute value in bwc_component_jwt_api.method tag", $id)
                );
            }

            if (isset($arrMethods[$attr['method']][$attr['direction']])) {
                throw new InvalidConfigurationException(
                    sprintf("Service '%s' declared as method '%s' direction '%s' but service '%s' already registered for same method and direction",
                        $id,
                        $attr['method'],
                        $attr['direction'],
                        $arrMethods[$attr['method']][$attr['direction']]
                    )
                );
            }

            $arrMethods[$attr['method']][$attr['direction']] = $id;
        } // foreach tagged method

        return $arrMethods;
    }


    /**
     * @param ContainerBuilder $container
     * @return array    decorator_name => service_id
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    protected function findAllDecorators(ContainerBuilder $container)
    {
        $arrTaggedDecorators = $container->findTaggedServiceIds('bwc_component_jwt_api.decorator');

        $arrDecorators = array();

        foreach ($arrTaggedDecorators as $id=>$attributes) {
            foreach ($attributes as $attr) {
                if (!isset($attr['decorator'])) {
                    throw new InvalidConfigurationException(
                        sprintf("Service '%s' missing decorator attribute on bwc_component_jwt_api.decorator tag", $id)
                    );
                }

                $name = $attr['decorator'];

                if (isset($arrDecorators[$name])) {
                    throw new InvalidConfigurationException(
                        sprintf("Service '%s' declared as decorator '%s' but service '%s' already registered with same decorator name",
                            $id,
                            $name,
                            $arrDecorators[$name]
                        )
                    );
                }

                $arrDecorators[$name] = $id;
            }
        }

        return $arrDecorators;
    }


    /**
     * @param string $methodName
     * @param string $direction
     * @param string $methodServiceId
     * @param ContainerBuilder $container
     * @param array $arrDecorators   decorator_name => service_id
     */
    protected function buildSingleMethod($methodName, $direction, $methodServiceId, ContainerBuilder $container, array $arrDecorators)
    {
        $compositeId = $this->buildPrePostDecorators($methodName, $methodServiceId, $container, $arrDecorators);

        $filterId = $this->buildFilter($methodName, $direction, $compositeId, $container);

        //var_dump($container->getDefinition($filterId));
        //var_dump($container->getDefinition($compositeId));

        $methodHandler = $container->getDefinition('bwc_component_jwt_api.handler.method');
        $methodHandler->addMethodCall('addContextHandler', array(new Reference($filterId)));
    }

    /**
     * @param string $methodName
     * @param string $direction
     * @param string $innerServiceId
     * @param ContainerBuilder $container
     * @return string  The filter service id
     */
    protected function buildFilter($methodName, $direction, $innerServiceId, ContainerBuilder $container)
    {
        $filterId = $innerServiceId.'.filter';

        $filterDefinition = new DefinitionDecorator('bwc_component_jwt_api.handler.abstract.filter.direction_method');
        $filterDefinition->replaceArgument(0, new Reference($innerServiceId));
        $filterDefinition->replaceArgument(1, $direction);
        $filterDefinition->replaceArgument(2, $methodName);
        $container->setDefinition($filterId, $filterDefinition);

        return $filterId;
    }


    /**
     * @param string $methodName
     * @param string $methodServiceId
     * @param ContainerBuilder $container
     * @param array $arrDecorators decorator_name => service_id
     * @return string  composite service id
     */
    protected function buildPrePostDecorators($methodName, $methodServiceId, ContainerBuilder $container, array $arrDecorators)
    {
        list($pre, $post) = $this->getServicePrePostDecorators($methodServiceId, $container, $arrDecorators);

        $compositeId = 'bwc_component_jwt_api.method.composite.'.str_replace('-', '_', $methodName);

        $composite = new DefinitionDecorator('bwc_component_jwt_api.handler.abstract.composite');

        foreach ($pre as $decoratorServiceId) {
            $composite->addMethodCall('addContextHandler', array(new Reference($decoratorServiceId)));
        }

        $composite->addMethodCall('addContextHandler', array(new Reference($methodServiceId)));

        foreach ($post as $decoratorServiceId) {
            $composite->addMethodCall('addContextHandler', array(new Reference($decoratorServiceId)));
        }

        $container->setDefinition($compositeId, $composite);

        return $compositeId;
    }


    protected function getServicePrePostDecorators($methodServiceId, ContainerBuilder $container, array $arrDecorators)
    {
        $serviceDefinition = $container->getDefinition($methodServiceId);

        $pre = $this->getDecorators(
            $methodServiceId,
            'bwc_component_jwt_api.pre',
            $serviceDefinition->getTag('bwc_component_jwt_api.pre'),
            $arrDecorators
        );
        $post = $this->getDecorators(
            $methodServiceId,
            'bwc_component_jwt_api.post',
            $serviceDefinition->getTag('bwc_component_jwt_api.post'),
            $arrDecorators
        );

        return array($pre, $post);
    }

    protected function getDecorators($serviceId, $tagName, array $attributes, array $arrDecorators)
    {
        $result = array();

        foreach ($attributes as $attr) {
            if (!isset($attr['decorator'])) {
                throw new InvalidConfigurationException(
                    sprintf("Service '%s' missing decorator attribute on %s tag", $serviceId, $tagName)
                );
            }

            $decorator = $attr['decorator'];
            if (!isset($arrDecorators[$decorator])) {
                throw new InvalidConfigurationException(
                    sprintf("Decorator '%s' in tag '%s' of service '%s' is not defined",
                        $decorator,
                        $tagName,
                        $serviceId
                    )
                );
            }

            $priority = intval(@$attr['priority']);
            if ($priority < 1) {
                $priority = 999999;
            }

            $result[] = array($priority, $arrDecorators[$decorator]);
        }

        usort($result, function($a, $b) {
            if ($a[0] == $b[0]) {
                return 0;
            }
            return $a[0] < $b[0] ? -1 : 1;
        });

        $res = array();
        foreach ($result as $arr) {
            $res[] = $arr[1];
        }
        return $res;
    }


} 