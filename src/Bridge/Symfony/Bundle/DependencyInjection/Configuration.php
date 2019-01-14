<?php

namespace Elao\CommandMigration\Bridge\Symfony\Bundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('elao_command_migration');

        $rootNode
            ->children()
                ->arrayNode('storage')
                    ->isRequired()
                    ->children()
                        ->enumNode('type')
                            ->values(['dbal'])
                            ->defaultValue('dbal')
                        ->end()
                        ->scalarNode('dsn')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('table_name')
                            ->defaultValue('command_migrations')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('migrations')
                    ->useAttributeAsKey('name')
                        ->arrayPrototype()
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
