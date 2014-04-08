<?php

namespace BWC\Component\JwtApiBundle\DependencyInjection\Compiler;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddContextHandlersPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     * @throws \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function process(ContainerBuilder $container)
    {
        // find all tagged context handlers
        $arrTaggedContextHandlers = $container->findTaggedServiceIds('bwc_component_jwt_api.handler');

        // index them for each tag by priority
        $arrHandlers = array();
        foreach ($arrTaggedContextHandlers as $id=>$attributes) {
            foreach ($attributes as $attr) {
                if (!isset($attr['priority'])) {
                    throw new InvalidConfigurationException(
                        sprintf("Service '%s' missing priority on bwc_component_jwt_api.handler tag", $id)
                    );
                }
                $priority = intval($attr['priority']);
                if ($priority < 1) {
                    throw new InvalidConfigurationException(
                        sprintf("Service '%s' has invalid priority '%s' on bwc_component_jwt_api.handler tag", $id, $attr['priority'])
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

} 