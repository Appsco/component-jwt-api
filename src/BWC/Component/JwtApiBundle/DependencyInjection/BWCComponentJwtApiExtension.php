<?php

namespace BWC\Component\JwtApiBundle\DependencyInjection;

use BWC\Component\JwtApiBundle\Method\Directions;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;


class BWCComponentJwtApiExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        if (!$config['enabled']) {
            return;
        }

        $loader->load('services.yml');

        $this->build($config, $container);
    }

    protected function build(array $config, ContainerBuilder $container)
    {
        $this->buildKeyProvider($config, $container);
        $this->buildBearerProvider($config, $container);
        $this->buildContextHandlers($config, $container);
        $this->buildAllMethods($container);
    }


    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function buildKeyProvider(array $config, ContainerBuilder $container)
    {
        if (isset($config['keys'])) {
            // Simple key provider with keys in the config
            $simpleKeyProvider = $container->getDefinition('bwc_component_jwt_api.key_provider.simple');
            foreach ($config['keys'] as $key) {
                $simpleKeyProvider->addMethodCall('addKey', array($key));
            }

            $keyProvider = new Reference('bwc_component_jwt_api.key_provider.simple');
        } else {
            // Service id specified for key provider
            $keyProvider = new Reference($config['id']);
        }

        $handler = $container->getDefinition('bwc_component_jwt_api.handler.key_provider');
        $handler->replaceArgument(0, $keyProvider);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function buildBearerProvider(array $config, ContainerBuilder $container)
    {
        $bearerProvider = new Reference($config['bearer_provider']);

        $handler = $container->getDefinition('bwc_component_jwt_api.handler.bearer_provider');
        $handler->replaceArgument(0, $bearerProvider);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     */
    protected function buildSubjectProvider(array $config, ContainerBuilder $container)
    {
        $subjectProvider = new Reference($config['subject_provider']);

        $handler = $container->getDefinition('bwc_component_jwt_api.handler.subject_provider');
        $handler->replaceArgument(0, $subjectProvider);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    protected function buildContextHandlers(array $config, ContainerBuilder $container)
    {
        // find all tagged context handlers
        $arrTaggedContextHandlers = $container->findTaggedServiceIds('bwc_component_jwt_api.handler');

        // index them for each tag by priority
        $arrHandlers = array();
        foreach ($arrTaggedContextHandlers as $id=>$attributes) {
            foreach ($attributes as $attr) {
                if (!isset($config['priority'])) {
                    throw new InvalidConfigurationException(
                        sprintf("Service '%s' missing priority on bwc_component_jwt_api.handler tag", $id)
                    );
                }
                $priority = intval($attr['priority']);
                if ($priority < 1) {
                    throw new InvalidConfigurationException(
                        sprintf("Service '%s' missing priority on bwc_component_jwt_api.handler tag", $id, $attr['priority'])
                    );
                }

                $arrHandlers[] = array($priority, $id);
            }
        }

        // sort index by priority
        usort($arrHandlers, function($a, $b) {
            if ($a[0] == $b[0]) {
                return 0;
            }
            return $a[0] < $b[0] ? -1 : 1;
        });

        // add them to manager
        $manager = $container->getDefinition('bwc_component_jwt_api.jwt_manager');
        foreach ($arrHandlers as $arr) {
            $manager->addMethodCall('addContextHandler', array(new Reference($arr[1])));
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function buildAllMethods(ContainerBuilder $container)
    {
        $arrAllTaggedMethods = $this->findAllMethods($container);

        $arrDecorators = $this->findAllDecorators($container);

        foreach ($arrAllTaggedMethods as $method=>$arr) {
            foreach ($arr as $direction=>$id) {
                $this->buildSingleMethod($method, $direction, $id, $container, $arrDecorators);
            }
        }
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
                        sprintf("Service '%s' missing name attribute on bwc_component_jwt_api.decorator tag", $id)
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
     * @param string $method
     * @param string $direction
     * @param string $id
     * @param ContainerBuilder $container
     * @param array $arrDecorators   decorator_name => service_id
     */
    protected function buildSingleMethod($method, $direction, $id, ContainerBuilder $container, array $arrDecorators)
    {
        $filterId = $this->buildFilter($method, $direction, $id, $container);

        $compositeId = $this->buildPrePostDecorators($filterId, $id, $container, $arrDecorators);

        $methodHandler = $container->getDefinition('bwc_component_jwt_api.handler.method');
        $methodHandler->addMethodCall('addContextHandler', array(new Reference($compositeId)));
    }

    /**
     * @param string $method
     * @param string $direction
     * @param string $id
     * @param ContainerBuilder $container
     * @return string  The filter service id
     */
    protected function buildFilter($method, $direction, $id, ContainerBuilder $container)
    {
        $filterId = 'bwc_component_jwt_api.method.filter.'.$method;

        $filterDefinition = new DefinitionDecorator(
            $container->getDefinition('bwc_component_jwt_api.handler.abstract.filter.direction_method')
        );
        $filterDefinition->replaceArgument(0, $container->getDefinition($id)); // inner - concrete method
        $filterDefinition->replaceArgument(1, $direction);
        $filterDefinition->replaceArgument(2, $method);
        $container->setDefinition($filterId, $filterDefinition);

        return $filterId;
    }


    /**
     * @param string $filterId
     * @param string $id
     * @param ContainerBuilder $container
     * @param array $arrDecorators   decorator_name => service_id
     * @return string  composite service id
     */
    protected function buildPrePostDecorators($filterId, $id, ContainerBuilder $container, array $arrDecorators)
    {
        list($pre, $post) = $this->getServicePrePostDecorators($id, $container, $arrDecorators);

        $compositeId = $filterId.'.composite';

        $composite = new DefinitionDecorator('bwc_component_jwt_api.handler.abstract.composite');

        foreach ($pre as $decoratorServiceId) {
            $composite->addMethodCall('addContextHandler', array(new Reference($decoratorServiceId)));
        }

        $composite->addMethodCall('addContextHandler', array(new Reference($filterId)));

        foreach ($post as $decoratorServiceId) {
            $composite->addMethodCall('addContextHandler', array(new Reference($decoratorServiceId)));
        }

        $container->setDefinition($compositeId, $composite);

        return $compositeId;
    }


    protected function getServicePrePostDecorators($id, ContainerBuilder $container, array $arrDecorators)
    {
        $serviceDefinition = $container->getDefinition($id);

        $pre = $this->getDecorators(
            $id,
            'bwc_component_jwt_api.pre',
            $serviceDefinition->getTag('bwc_component_jwt_api.pre'),
            $arrDecorators
        );
        $post = $this->getDecorators(
            $id,
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
            if (!$attr['decorator']) {
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