<?php

namespace BWC\Component\JwtApiBundle;

use BWC\Component\JwtApiBundle\DependencyInjection\Compiler\AddContextHandlersPass;
use BWC\Component\JwtApiBundle\DependencyInjection\Compiler\AddMethodsToManagerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class BWCComponentJwtApiBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new AddMethodsToManagerPass());
        $container->addCompilerPass(new AddContextHandlersPass());
    }
} 