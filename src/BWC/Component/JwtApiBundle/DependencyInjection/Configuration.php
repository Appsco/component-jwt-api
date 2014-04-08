<?php

namespace BWC\Component\JwtApiBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bwc_component_jwt_api');

        $rootNode->children()
            ->booleanNode('enabled')->defaultTrue()->end()
            ->arrayNode('issuer_provider')
                ->children()
                    ->scalarNode('id')
                        ->cannotBeEmpty()
                        ->defaultValue('bwc_component_jwt_api.issuer_provider.simple')
                    ->end()
                    ->scalarNode('issuer')->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->arrayNode('key_provider')
                ->children()
                    ->arrayNode('keys')
                        ->cannotBeEmpty()
                        ->prototype('scalar')->end()
                    ->end()
                    ->scalarNode('id')->cannotBeEmpty()->end()
                ->end()
            ->end()
            ->scalarNode('bearer_provider')->defaultValue('bwc_component_jwt_api.bearer_provider.user_security_context')->end()
            ->scalarNode('subject_provider')->defaultValue('bwc_component_jwt_api.subject_provider.null')->end()
        ->end();

        return $treeBuilder;
    }

} 