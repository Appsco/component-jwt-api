<?php

namespace BWC\Component\JwtApiBundle\Tests\DependencyInjection;

use BWC\Component\JwtApiBundle\DependencyInjection\BWCComponentJwtApiExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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







} 