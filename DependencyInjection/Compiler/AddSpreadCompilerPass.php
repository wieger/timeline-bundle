<?php

namespace Spy\TimelineBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class AddSpreadCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $alias            = $container->getAlias('spy_timeline.spread.deployer');
        $spreadDeployer   = $container->getDefinition((string) $alias);
        $spreadByPriority = [];

        foreach ($container->findTaggedServiceIds('spy_timeline.spread') as $id => $options) {
            $priority = array_key_exists('priority', $options[0]) ? $options[0]['priority'] : 0;

            $spreadByPriority[$priority][] = $container->getDefinition($id);
        }

        krsort($spreadByPriority);

        foreach ($spreadByPriority as $spreads) {
            $spreadDeployer->addMethodCall('addSpread', $spreads);
        }
    }
}
