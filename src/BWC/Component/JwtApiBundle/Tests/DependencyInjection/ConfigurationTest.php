<?php

namespace BWC\Component\JwtApiBundle\Tests\DependencyInjection;

use BWC\Component\JwtApiBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @_expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function shouldAllowNoConfiguration()
    {
        $configs = array();
        $this->processConfiguration($configs);
    }

    /**
     * @test
     */
    public function shouldAllowWithKeyProviderKeysOnly()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'key_provider' => array(
                    'keys' => array('11111')
                )
            )
        );

        $this->processConfiguration($configs);
    }

    /**
     * @test
     */
    public function shouldAllowWithKeyProviderIdOnly()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'key_provider' => array(
                    'id' => 'key_provider_service'
                )
            )
        );

        $this->processConfiguration($configs);
    }

    /**
     * @test
     */
    public function shouldBeEnabledByDefault()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'key_provider' => array(
                    'id' => 'key_provider_service'
                )
            )
        );

        $config = $this->processConfiguration($configs);

        $this->assertTrue($config['enabled']);
    }

    /**
     * @test
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidTypeException
     */
    public function throwWithKeyProviderKeysNotArray()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'key_provider' => array(
                    'keys' => '111'
                )
            )
        );

        $this->processConfiguration($configs);
    }

    /**
     * @test
     */
    public function shouldAllowBearerProvider()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'key_provider' => array(
                    'id' => 'key_provider_service'
                ),
                'bearer_provider' => 'bearer_provider_service'
            )
        );

        $this->processConfiguration($configs);
    }

    /**
     * @test
     */
    public function shouldAllowSubjectProvider()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'key_provider' => array(
                    'id' => 'key_provider_service'
                ),
                'subject_provider' => 'subject_provider_service'
            )
        );

        $this->processConfiguration($configs);
    }


    /**
     * @test
     */
    public function shouldDefaultBearerProvider()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'key_provider' => array(
                    'id' => 'key_provider_service'
                )
            )
        );

        $config = $this->processConfiguration($configs);

        $this->assertEquals(
            'bwc_component_jwt_api.bearer_provider.user_security_context',
            $config['bearer_provider']
        );
    }

    /**
     * @test
     */
    public function shouldDefaultSubjectProvider()
    {
        $configs = array(
            'bwc_component_jwt_api' => array(
                'key_provider' => array(
                    'id' => 'key_provider_service'
                )
            )
        );

        $config = $this->processConfiguration($configs);

        $this->assertEquals(
            'bwc_component_jwt_api.subject_provider.null',
            $config['subject_provider']
        );
    }


    /**
     * @param array $configs
     * @return array
     */
    protected function processConfiguration(array $configs)
    {
        $configuration = new Configuration();
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $configs);
    }

} 