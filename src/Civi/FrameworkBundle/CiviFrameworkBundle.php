<?php

namespace Civi\FrameworkBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Civi\FrameworkBundle\DependencyInjection\Compiler\ControllerResolverPass;

class CiviFrameworkBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ControllerResolverPass());
    }
}
