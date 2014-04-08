<?php

namespace BWC\Component\JwtApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
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
        $this->buildIssuerProvider($config['issuer_provider'], $container);
        $this->buildKeyProvider(isset($config['key_provider']) ? $config['key_provider'] : array(), $container);
        $this->buildBearerProvider($config, $container);
        $this->buildSubjectProvider($config, $container);
    }


    protected function buildIssuerProvider(array $config, ContainerBuilder $container)
    {
        if (isset($config['id'])) {
            $issuerProviderId = $config['id'];
        } else if (isset($config['issuer'])) {
            $issuerProviderId = 'bwc_component_jwt_api.issuer_provider.simple';
            $service = $container->getDefinition($issuerProviderId);
            $service->replaceArgument(0, array($config['issuer']));
        } else {
            throw new InvalidConfigurationException('bwc_component_jwt_api.issuer_provider must have either id or issuer option');
        }

        $handler = $container->getDefinition('bwc_component_jwt_api.handler.my_issuer_id');
        $handler->replaceArgument(0, new Reference($issuerProviderId));
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    protected function buildKeyProvider(array $config, ContainerBuilder $container)
    {
        if (isset($config['id'])) {
            // Service id specified for key provider
            $keyProvider = new Reference($config['id']);
        } else {
            // Simple key provider with keys in the config
            if (isset($config['keys'])) {
                if (is_array($config['keys'])) {
                    $simpleKeyProvider = $container->getDefinition('bwc_component_jwt_api.key_provider.simple');
                    foreach ($config['keys'] as $key) {
                        $simpleKeyProvider->addMethodCall('addKey', array($key));
                    }
                } else {
                    throw new InvalidConfigurationException('bwc_component_jwt_api.key_provider.keys must be array');
                }
            }

            $keyProvider = new Reference('bwc_component_jwt_api.key_provider.simple');
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


} 