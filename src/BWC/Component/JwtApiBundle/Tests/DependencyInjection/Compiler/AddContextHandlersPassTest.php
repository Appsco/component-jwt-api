<?php

namespace BWC\Component\JwtApiBundle\Tests\DependencyInjection\Compiler;

use BWC\Component\JwtApiBundle\DependencyInjection\Compiler\AddContextHandlersPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class AddContextHandlersPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldAddStandardHandlersToManager()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $containerBuilder->setDefinition('bwc_component_jwt_api.jwt_manager', new Definition(''));

        $handlerDefinition = new Definition('');
        $handlerDefinition->addTag('bwc_component_jwt_api.handler', array('priority'=>100));
        $containerBuilder->setDefinition('bwc_component_jwt_api.handler.one', $handlerDefinition);


        $compiler = new AddContextHandlersPass();
        $compiler->process($containerBuilder);

        $manager = $containerBuilder->getDefinition('bwc_component_jwt_api.jwt_manager');
        $arrCalls = $manager->getMethodCalls();

        $arrHandlers = array();
        foreach ($arrCalls as $arr) {
            if ($arr[0] == 'addContextHandler') {
                $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arr[1][0]);
                $arrHandlers[] = (string)$arr[1][0];
            }
        }

        $this->assertEquals($arrHandlers[0], 'bwc_component_jwt_api.handler.one');
    }


    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.handler.one' missing priority on bwc_component_jwt_api.handler tag
     */
    public function throwOnContextHandlerWithoutPriority()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $handlerDefinition = new Definition($class = 'method\class');
        $handlerDefinition->addTag('bwc_component_jwt_api.handler', array());
        $containerBuilder->setDefinition($methodId = 'acme.handler.one', $handlerDefinition);

        $compiler = new AddContextHandlersPass();
        $compiler->process($containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.handler.one' has invalid priority 'foo' on bwc_component_jwt_api.handler tag
     */
    public function throwOnContextHandlerWithInvalidPriority()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $handlerDefinition = new Definition($class = 'method\class');
        $handlerDefinition->addTag('bwc_component_jwt_api.handler', array('priority'=>'foo'));
        $containerBuilder->setDefinition($methodId = 'acme.handler.one', $handlerDefinition);

        $compiler = new AddContextHandlersPass();
        $compiler->process($containerBuilder);
    }



} 