<?php

namespace BWC\Component\JwtApi\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;


class JwtHandlerCompilerPass implements CompilerPassInterface
{
    /** @var  string */
    protected $serviceId;

    /** @var string  */
    protected $tagId;



    /**
     * @param string $serviceId
     * @param string $tagId
     */
    public function __construct($serviceId = 'jwt.handler', $tagId = 'jwt.handler')
    {
        $this->serviceId = $serviceId;
        $this->tagId = $tagId;
    }



    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition($this->serviceId)) {
            return;
        }

        $definition = $container->getDefinition($this->serviceId);

        $taggedServices = $container->findTaggedServiceIds($this->tagId);

        foreach ($taggedServices as $id => $attributes) {
            foreach ($attributes as $attr) {
                $definition->addMethodCall('addHandler',
                        array(
                                $attr['type'],
                                new Reference($id)
                        )
                );
            }
        }
    }

}
