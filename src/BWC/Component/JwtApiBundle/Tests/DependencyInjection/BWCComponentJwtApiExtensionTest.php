<?php

namespace BWC\Component\JwtApiBundle\Tests\DependencyInjection;

use BWC\Component\JwtApiBundle\DependencyInjection\BWCComponentJwtApiExtension;
use BWC\Component\JwtApiBundle\Method\Directions;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class BWCComponentJwtApiExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldLoadExtensionWithIssuerProviderIssuerOnly()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                ),
            )
        );
        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.jwt_manager'));
    }

    /**
     * @test
     */
    public function shouldNotLoadServicesIfDisabled()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'enabled' => false
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension->load($configs, $containerBuilder);

        $this->assertFalse($containerBuilder->hasDefinition('bwc_component_jwt_api.jwt_manager'));
    }

    /**
     * @test
     */
    public function shouldLoadReceiver()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.receiver'));
    }

    /**
     * @test
     */
    public function shouldLoadSender()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.sender'));
    }

    /**
     * @test
     */
    public function shouldLoadManager()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension->load($configs, $containerBuilder);

        $this->assertTrue($containerBuilder->hasDefinition('bwc_component_jwt_api.jwt_manager'));
    }




    public function shouldAddExceptionStrategyToManager()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());
        $extension->load($configs, $containerBuilder);

        $manager = $containerBuilder->getDefinition('bwc_component_jwt_api.jwt_manager');
        $arrCalls = $manager->getMethodCalls();

        $exceptionStrategyCallCount = 0;
        foreach ($arrCalls as $arr) {
            if ($arr[0] == 'setExceptionStrategy') {
                $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $arr[1][0]);
                $this->assertEquals('bwc_component_jwt_api.exception_strategy', (string)$arr[1][0]);
                $exceptionStrategyCallCount++;
            }
        }

        $this->assertEquals(1, $exceptionStrategyCallCount);
    }



    /**
     * @test
     */
    public function shouldSetKeyProviderKeys()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                ),
                'key_provider' => array(
                    'keys' => $keys = array($key1 = '111', $key2 = '222')
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $extension->load($configs, $containerBuilder);

        $keyProviderHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.key_provider');
        $keyProviderReference = $keyProviderHandler->getArgument(0);

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $keyProviderReference);
        $this->assertEquals('bwc_component_jwt_api.key_provider.simple', (string)$keyProviderReference);

        $keyProvider = $containerBuilder->getDefinition('bwc_component_jwt_api.key_provider.simple');
        $arrMethodCalls = $keyProvider->getMethodCalls();

        $this->assertCount(2, $arrMethodCalls);

        $this->assertEquals('addKey', $arrMethodCalls[0][0]);
        $this->assertEquals($key1, $arrMethodCalls[0][1][0]);

        $this->assertEquals('addKey', $arrMethodCalls[1][0]);
        $this->assertEquals($key2, $arrMethodCalls[1][1][0]);
    }

    /**
     * @test
     */
    public function shouldSetKeyProviderId()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                ),
                'key_provider' => array(
                    'id' => $keyProviderServiceId = 'key.provider.service.id'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $extension->load($configs, $containerBuilder);

        $keyProviderHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.key_provider');
        $keyProviderReference = $keyProviderHandler->getArgument(0);

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $keyProviderReference);
        $this->assertEquals($keyProviderServiceId, (string)$keyProviderReference);
    }


    /**
     * @test
     */
    public function shouldSetSubjectProvider()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                ),
                'subject_provider' => $subjectProviderServiceId = 'subject.provider.service.id'
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $extension->load($configs, $containerBuilder);

        $subjectProviderHandler = $containerBuilder->getDefinition('bwc_component_jwt_api.handler.subject_provider');
        $subjectProviderReference = $subjectProviderHandler->getArgument(0);

        $this->assertInstanceOf('Symfony\Component\DependencyInjection\Reference', $subjectProviderReference);
        $this->assertEquals($subjectProviderServiceId, (string)$subjectProviderReference);
    }




    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.handler.one' has invalid priority 'foo' on bwc_component_jwt_api.handler tag
     */
    public function throwOnContextHandlerWithoutInvalidPriority()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $handlerDefinition = new Definition($class = 'method\class');
        $handlerDefinition->addTag('bwc_component_jwt_api.handler', array('priority'=>'foo'));
        $containerBuilder->setDefinition($methodId = 'acme.handler.one', $handlerDefinition);

        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.foo' has more then one bwc_component_jwt_api.method tag
     */
    public function throwOnMethodServiceHavingMoreThenOneMethodTag()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
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

        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.foo' missing method attribute in bwc_component_jwt_api.method tag
     */
    public function throwOnMethodServiceMissingMethodAttribute()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => Directions::REQUEST,
        ));
        $containerBuilder->setDefinition('acme.method.foo', $methodDefinition);

        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.foo' missing direction attribute in bwc_component_jwt_api.method tag
     */
    public function throwOnMethodServiceMissingDirectionAttribute()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'method' => 'acme-method',
        ));
        $containerBuilder->setDefinition('acme.method.foo', $methodDefinition);

        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.foo' has invalid direction attribute value in bwc_component_jwt_api.method tag
     */
    public function throwOnMethodServiceHasInvalidDirectionAttribute()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $methodDefinition = new Definition($class = 'method\class');
        $methodDefinition->addTag('bwc_component_jwt_api.method', array(
            'direction' => 'foo',
            'method' => 'acme-method',
        ));
        $containerBuilder->setDefinition('acme.method.foo', $methodDefinition);

        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.method.bar' declared as method 'acme-method' direction 'req' but service 'acme.method.foo' already registered for same method and direction
     */
    public function throwWhenMethodAlreadyDefined()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
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

        $extension->load($configs, $containerBuilder);
    }


    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.decorator.foo' missing decorator attribute on bwc_component_jwt_api.decorator tag
     */
    public function throwOnDecoratorMissingDecoratorAttribute()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $decorator = new Definition($decoratorFooClass = 'decorator\foo');
        $decorator->addTag('bwc_component_jwt_api.decorator', array());
        $containerBuilder->setDefinition($decoratorFooId = 'acme.decorator.foo', $decorator);

        $extension->load($configs, $containerBuilder);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Service 'acme.decorator.bar' declared as decorator 'acme.baz' but service 'acme.decorator.foo' already registered with same decorator name
     */
    public function throwOnDecoratorAlreadyDefined()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'issuer_provider' => array(
                    'issuer' => 'foo'
                )
            )
        );

        $extension = new BWCComponentJwtApiExtension();
        $containerBuilder = new ContainerBuilder(new ParameterBag());

        $decoratorFoo = new Definition($decoratorFooClass = 'decorator\foo');
        $decoratorFoo->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.baz'));
        $containerBuilder->setDefinition($decoratorFooId = 'acme.decorator.foo', $decoratorFoo);

        $decoratorBar = new Definition($decoratorFooClass = 'decorator\bar');
        $decoratorBar->addTag('bwc_component_jwt_api.decorator', array('decorator'=>'acme.baz'));
        $containerBuilder->setDefinition($decoratorBarId = 'acme.decorator.bar', $decoratorBar);

        $extension->load($configs, $containerBuilder);
    }

} 