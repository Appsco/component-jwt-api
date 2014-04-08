<?php

namespace BWC\Component\JwtApiBundle\Tests\DependencyInjection\Compiler;

use BWC\Component\JwtApiBundle\DependencyInjection\Compiler\AddMethodsToManagerPass;
use BWC\Component\JwtApiBundle\Method\Directions;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class AddMethodsToManagerPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateMethod()
    {
        $compiler = new AddMethodsToManagerPass();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $containerBuilder->setDefinition('bwc_component_jwt_api.handler.method', new Definition(''));

        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => $direction = Directions::REQUEST,
            'method' => $methodName = 'acme-method-one'
        ));
        $containerBuilder->setDefinition($methodId = 'acme.method.one', $methodDefinition);

        $compiler->process($containerBuilder);

        $methodHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.method');
        $arrMethodCalls = $methodHandler->getMethodCalls();

        $this->assertCount(1, $arrMethodCalls);
        $this->assertEquals($arrMethodCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrMethodCalls[0][1][0]);
        $this->assertEquals($arrMethodCalls[0][1][0], 'bwc_component_jwt_api.method.filter.acme-method-one.composite');

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.method.filter.acme-method-one.composite'));
        $composite = $containerBuilder->getDefinition('bwc_component_jwt_api.method.filter.acme-method-one.composite');
        $arrCompositeCalls = $composite->getMethodCalls();

        $this->assertCount(1, $arrCompositeCalls);
        $this->assertEquals($arrCompositeCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrCompositeCalls[0][1][0]);
        $this->assertEquals($arrCompositeCalls[0][1][0], 'bwc_component_jwt_api.method.filter.acme-method-one');


        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.method.filter.acme-method-one'));
        $filter = $containerBuilder->getDefinition('bwc_component_jwt_api.method.filter.acme-method-one');
        $inner = $filter->getArgument(0);


        $this->assertEquals($methodDefinition, $inner);
    }


    /**
     * @test
     */
    public function shouldPreDecorate()
    {
        $compiler = new AddMethodsToManagerPass();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $containerBuilder->setDefinition('bwc_component_jwt_api.handler.method', new Definition(''));

        $decoratorFoo = new Definition($decoratorFooClass = 'decorator\foo');
        $decoratorFoo->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.foo'));
        $containerBuilder->setDefinition($decoratorFooId = 'acme.decorator.foo', $decoratorFoo);

        $decoratorBar = new Definition($decoratorBarClass = 'decorator\bar');
        $decoratorBar->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.bar'));
        $containerBuilder->setDefinition($decoratorBarId = 'acme.decorator.bar', $decoratorBar);


        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => $direction = Directions::REQUEST,
            'method' => $methodName = 'acme-method-one'
        ));
        $methodDefinition->addTag('bwc_component_jwt_api.pre', array('decorator'=>'acme.foo', 'priority'=>50));
        $methodDefinition->addTag('bwc_component_jwt_api.pre', array('decorator'=>'acme.bar', 'priority'=>10));
        $containerBuilder->setDefinition($methodId = 'acme.method.one', $methodDefinition);

        $compiler->process($containerBuilder);

        $methodHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.method');
        $arrMethodCalls = $methodHandler->getMethodCalls();

        $this->assertCount(1, $arrMethodCalls);
        $this->assertEquals($arrMethodCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrMethodCalls[0][1][0]);
        $this->assertEquals($arrMethodCalls[0][1][0], 'bwc_component_jwt_api.method.filter.acme-method-one.composite');

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.method.filter.acme-method-one.composite'));
        $composite = $containerBuilder->getDefinition('bwc_component_jwt_api.method.filter.acme-method-one.composite');
        $arrCompositeCalls = $composite->getMethodCalls();

        $this->assertCount(3, $arrCompositeCalls);

        foreach ($arrCompositeCalls as $call) {
            $this->assertEquals('addContextHandler', $call[0]);
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $call[1][0]);
        }

        $this->assertEquals($decoratorBarId, (string)$arrCompositeCalls[0][1][0]);
        $this->assertEquals($decoratorFooId, (string)$arrCompositeCalls[1][1][0]);
        $this->assertEquals('bwc_component_jwt_api.method.filter.acme-method-one', (string)$arrCompositeCalls[2][1][0]);
    }


    /**
     * @test
     */
    public function shouldPostDecorate()
    {
        $compiler = new AddMethodsToManagerPass();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $containerBuilder->setDefinition('bwc_component_jwt_api.handler.method', new Definition(''));

        $decoratorFoo = new Definition($decoratorFooClass = 'decorator\foo');
        $decoratorFoo->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.foo'));
        $containerBuilder->setDefinition($decoratorFooId = 'acme.decorator.foo', $decoratorFoo);

        $decoratorBar = new Definition($decoratorBarClass = 'decorator\bar');
        $decoratorBar->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.bar'));
        $containerBuilder->setDefinition($decoratorBarId = 'acme.decorator.bar', $decoratorBar);


        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => $direction = Directions::REQUEST,
            'method' => $methodName = 'acme-method-one'
        ));
        $methodDefinition->addTag('bwc_component_jwt_api.post', array('decorator'=>'acme.foo', 'priority'=>50));
        $methodDefinition->addTag('bwc_component_jwt_api.post', array('decorator'=>'acme.bar', 'priority'=>10));
        $containerBuilder->setDefinition($methodId = 'acme.method.one', $methodDefinition);

        $compiler->process($containerBuilder);

        $methodHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.method');
        $arrMethodCalls = $methodHandler->getMethodCalls();

        $this->assertCount(1, $arrMethodCalls);
        $this->assertEquals($arrMethodCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrMethodCalls[0][1][0]);
        $this->assertEquals($arrMethodCalls[0][1][0], 'bwc_component_jwt_api.method.filter.acme-method-one.composite');

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.method.filter.acme-method-one.composite'));
        $composite = $containerBuilder->getDefinition('bwc_component_jwt_api.method.filter.acme-method-one.composite');
        $arrCompositeCalls = $composite->getMethodCalls();

        $this->assertCount(3, $arrCompositeCalls);

        foreach ($arrCompositeCalls as $call) {
            $this->assertEquals('addContextHandler', $call[0]);
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $call[1][0]);
        }

        $this->assertEquals('bwc_component_jwt_api.method.filter.acme-method-one', (string)$arrCompositeCalls[0][1][0]);
        $this->assertEquals($decoratorBarId, (string)$arrCompositeCalls[1][1][0]);
        $this->assertEquals($decoratorFooId, (string)$arrCompositeCalls[2][1][0]);
    }


    /**
     * @test
     */
    public function shouldPrePostDecorate()
    {
        $compiler = new AddMethodsToManagerPass();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $containerBuilder->setDefinition('bwc_component_jwt_api.handler.method', new Definition(''));

        $decoratorFoo = new Definition($decoratorFooClass = 'decorator\foo');
        $decoratorFoo->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.foo'));
        $containerBuilder->setDefinition($decoratorFooId = 'acme.decorator.foo', $decoratorFoo);

        $decoratorBar = new Definition($decoratorBarClass = 'decorator\bar');
        $decoratorBar->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.bar'));
        $containerBuilder->setDefinition($decoratorBarId = 'acme.decorator.bar', $decoratorBar);

        $decoratorBaz = new Definition($decoratorBarClass = 'decorator\baz');
        $decoratorBaz->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.baz'));
        $containerBuilder->setDefinition($decoratorBazId = 'acme.decorator.baz', $decoratorBaz);


        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => $direction = Directions::REQUEST,
            'method' => $methodName = 'acme-method-one'
        ));
        $methodDefinition->addTag('bwc_component_jwt_api.post', array('decorator'=>'acme.foo', 'priority'=>50));
        $methodDefinition->addTag('bwc_component_jwt_api.post', array('decorator'=>'acme.bar', 'priority'=>10));
        $methodDefinition->addTag('bwc_component_jwt_api.pre', array('decorator'=>'acme.baz'));
        $containerBuilder->setDefinition($methodId = 'acme.method.one', $methodDefinition);

        $compiler->process($containerBuilder);

        $methodHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.method');
        $arrMethodCalls = $methodHandler->getMethodCalls();

        $this->assertCount(1, $arrMethodCalls);
        $this->assertEquals($arrMethodCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrMethodCalls[0][1][0]);
        $this->assertEquals($arrMethodCalls[0][1][0], 'bwc_component_jwt_api.method.filter.acme-method-one.composite');

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.method.filter.acme-method-one.composite'));
        $composite = $containerBuilder->getDefinition('bwc_component_jwt_api.method.filter.acme-method-one.composite');
        $arrCompositeCalls = $composite->getMethodCalls();

        $this->assertCount(4, $arrCompositeCalls);

        foreach ($arrCompositeCalls as $call) {
            $this->assertEquals('addContextHandler', $call[0]);
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $call[1][0]);
        }

        $this->assertEquals($decoratorBazId, (string)$arrCompositeCalls[0][1][0]);
        $this->assertEquals('bwc_component_jwt_api.method.filter.acme-method-one', (string)$arrCompositeCalls[1][1][0]);
        $this->assertEquals($decoratorBarId, (string)$arrCompositeCalls[2][1][0]);
        $this->assertEquals($decoratorFooId, (string)$arrCompositeCalls[3][1][0]);
    }



} 