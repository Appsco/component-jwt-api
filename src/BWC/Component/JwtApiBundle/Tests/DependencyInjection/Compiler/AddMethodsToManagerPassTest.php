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
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $containerBuilder->setDefinition('bwc_component_jwt_api.handler.method', new Definition(''));

        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => $direction = Directions::REQUEST,
            'method' => $methodName = 'acme-method-one'
        ));
        $containerBuilder->setDefinition($methodId = 'acme.method.one', $methodDefinition);


        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);


        $methodHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.method');
        $arrMethodCalls = $methodHandler->getMethodCalls();

        $this->assertCount(1, $arrMethodCalls);
        $this->assertEquals($arrMethodCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrMethodCalls[0][1][0]);
        $this->assertEquals('bwc_component_jwt_api.method.composite.acme_method_one.filter', (string)$arrMethodCalls[0][1][0]);

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.method.composite.acme_method_one.filter'));
        $filter = $containerBuilder->getDefinition('bwc_component_jwt_api.method.composite.acme_method_one.filter');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $filter->getArgument(0));
        $this->assertEquals('bwc_component_jwt_api.method.composite.acme_method_one', (string)$filter->getArgument(0));
        $this->assertEquals($direction, $filter->getArgument(1));
        $this->assertEquals($methodName, $filter->getArgument(2));

        $composite = $containerBuilder->getDefinition('bwc_component_jwt_api.method.composite.acme_method_one');
        $arrCompositeCalls = $composite->getMethodCalls();

        $this->assertCount(1, $arrMethodCalls);
        $this->assertEquals($arrCompositeCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrCompositeCalls[0][1][0]);
        $this->assertEquals($methodId, (string)$arrCompositeCalls[0][1][0]);
    }


    /**
     * @test
     */
    public function shouldPreDecorate()
    {
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


        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);


        $methodHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.method');
        $arrMethodCalls = $methodHandler->getMethodCalls();

        $this->assertCount(1, $arrMethodCalls);
        $this->assertEquals($arrMethodCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrMethodCalls[0][1][0]);
        $this->assertEquals('bwc_component_jwt_api.method.composite.acme_method_one.filter', (string)$arrMethodCalls[0][1][0]);

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.method.composite.acme_method_one.filter'));
        $filter = $containerBuilder->getDefinition('bwc_component_jwt_api.method.composite.acme_method_one.filter');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $filter->getArgument(0));
        $this->assertEquals('bwc_component_jwt_api.method.composite.acme_method_one', (string)$filter->getArgument(0));

        $composite = $containerBuilder->getDefinition('bwc_component_jwt_api.method.composite.acme_method_one');
        $arrCompositeCalls = $composite->getMethodCalls();

        $this->assertCount(3, $arrCompositeCalls);

        foreach ($arrCompositeCalls as $call) {
            $this->assertEquals('addContextHandler', $call[0]);
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $call[1][0]);
        }

        $this->assertEquals($decoratorBarId, (string)$arrCompositeCalls[0][1][0]);
        $this->assertEquals($decoratorFooId, (string)$arrCompositeCalls[1][1][0]);
        $this->assertEquals($methodId, (string)$arrCompositeCalls[2][1][0]);
    }


    /**
     * @test
     */
    public function shouldPostDecorate()
    {
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


        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);


        $methodHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.method');
        $arrMethodCalls = $methodHandler->getMethodCalls();

        $this->assertCount(1, $arrMethodCalls);
        $this->assertEquals($arrMethodCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrMethodCalls[0][1][0]);
        $this->assertEquals($arrMethodCalls[0][1][0], 'bwc_component_jwt_api.method.composite.acme_method_one.filter');

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.method.composite.acme_method_one.filter'));
        $filter = $containerBuilder->getDefinition('bwc_component_jwt_api.method.composite.acme_method_one.filter');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $filter->getArgument(0));
        $this->assertEquals('bwc_component_jwt_api.method.composite.acme_method_one', (string)$filter->getArgument(0));

        $composite = $containerBuilder->getDefinition('bwc_component_jwt_api.method.composite.acme_method_one');
        $arrCompositeCalls = $composite->getMethodCalls();

        $this->assertCount(3, $arrCompositeCalls);

        foreach ($arrCompositeCalls as $call) {
            $this->assertEquals('addContextHandler', $call[0]);
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $call[1][0]);
        }

        $this->assertEquals($methodId, (string)$arrCompositeCalls[0][1][0]);
        $this->assertEquals($decoratorBarId, (string)$arrCompositeCalls[1][1][0]);
        $this->assertEquals($decoratorFooId, (string)$arrCompositeCalls[2][1][0]);
    }


    /**
     * @test
     */
    public function shouldPrePostDecorate()
    {
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


        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);


        $methodHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.method');
        $arrMethodCalls = $methodHandler->getMethodCalls();

        $this->assertCount(1, $arrMethodCalls);
        $this->assertEquals($arrMethodCalls[0][0], 'addContextHandler');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arrMethodCalls[0][1][0]);
        $this->assertEquals($arrMethodCalls[0][1][0], 'bwc_component_jwt_api.method.composite.acme_method_one.filter');

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.method.composite.acme_method_one.filter'));
        $filter = $containerBuilder->getDefinition('bwc_component_jwt_api.method.composite.acme_method_one.filter');
        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $filter->getArgument(0));
        $this->assertEquals('bwc_component_jwt_api.method.composite.acme_method_one', (string)$filter->getArgument(0));

        $composite = $containerBuilder->getDefinition('bwc_component_jwt_api.method.composite.acme_method_one');
        $arrCompositeCalls = $composite->getMethodCalls();

        $this->assertCount(4, $arrCompositeCalls);

        foreach ($arrCompositeCalls as $call) {
            $this->assertEquals('addContextHandler', $call[0]);
            $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $call[1][0]);
        }

        $this->assertEquals($decoratorBazId, (string)$arrCompositeCalls[0][1][0]);
        $this->assertEquals($methodId, (string)$arrCompositeCalls[1][1][0]);
        $this->assertEquals($decoratorBarId, (string)$arrCompositeCalls[2][1][0]);
        $this->assertEquals($decoratorFooId, (string)$arrCompositeCalls[3][1][0]);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.foo' has more then one bwc_component_jwt_api.method tag
     */
    public function throwOnMethodServiceHavingMoreThenOneMethodTag()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => Directions::REQUEST,
            'method' => 'acme-method-one'
        ));
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => Directions::REQUEST,
            'method' => 'acme-method-two'
        ));
        $containerBuilder->setDefinition('acme.method.foo', $methodDefinition);

        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);
    }


    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.foo' missing method attribute in bwc_component_jwt_api.method tag
     */
    public function throwOnMethodServiceMissingMethodAttribute()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => Directions::REQUEST,
        ));
        $containerBuilder->setDefinition('acme.method.foo', $methodDefinition);

        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.foo' missing direction attribute in bwc_component_jwt_api.method tag
     */
    public function throwOnMethodServiceMissingDirectionAttribute()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'method' => 'acme-method',
        ));
        $containerBuilder->setDefinition('acme.method.foo', $methodDefinition);

        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);
    }



    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.foo' has invalid direction attribute value in bwc_component_jwt_api.method tag
     */
    public function throwOnMethodServiceHasInvalidDirectionAttribute()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => 'foo',
            'method' => 'acme-method',
        ));
        $containerBuilder->setDefinition('acme.method.foo', $methodDefinition);

        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.bar' declared as method 'acme-method' direction 'req' but service 'acme.method.foo' already registered for same method and direction
     */
    public function throwWhenMethodAlreadyDefined()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $methodOne = new Definition($class = 'method\class');
        $methodOne->addTag('bwc_component_jwt_api.method', array(
            'direction' => Directions::REQUEST,
            'method' => 'acme-method',
        ));
        $containerBuilder->setDefinition('acme.method.foo', $methodOne);

        $methodTwo = new Definition($class = 'method\class');
        $methodTwo->addTag('bwc_component_jwt_api.method', array(
            'direction' => Directions::REQUEST,
            'method' => 'acme-method',
        ));
        $containerBuilder->setDefinition('acme.method.bar', $methodTwo);

        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);
    }


    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.decorator.foo' missing decorator attribute on bwc_component_jwt_api.decorator tag
     */
    public function throwOnDecoratorMissingDecoratorAttribute()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $decorator = new Definition($decoratorFooClass = 'decorator\foo');
        $decorator->addTag('bwc_component_jwt_api.decorator', array());
        $containerBuilder->setDefinition($decoratorFooId = 'acme.decorator.foo', $decorator);

        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.decorator.bar' declared as decorator 'acme.baz' but service 'acme.decorator.foo' already registered with same decorator name
     */
    public function throwOnDecoratorAlreadyDefined()
    {
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $decoratorFoo = new Definition($decoratorFooClass = 'decorator\foo');
        $decoratorFoo->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.baz'));
        $containerBuilder->setDefinition($decoratorFooId = 'acme.decorator.foo', $decoratorFoo);

        $decoratorBar = new Definition($decoratorFooClass = 'decorator\bar');
        $decoratorBar->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.baz'));
        $containerBuilder->setDefinition($decoratorBarId = 'acme.decorator.bar', $decoratorBar);

        $compiler = new AddMethodsToManagerPass();
        $compiler->process($containerBuilder);
    }

} 