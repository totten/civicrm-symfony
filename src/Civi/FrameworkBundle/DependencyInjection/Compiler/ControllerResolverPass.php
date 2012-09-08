<?php
namespace Civi\FrameworkBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class ControllerResolverPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
    /*
        $definition = $container->getDefinition('controller_resolver');
        $container->setDefinition('default.controller_resolver', $definition);
        $container->setAlias('controller_resolver', 'civi_framework.controller_resolver');
        */
    }
}
