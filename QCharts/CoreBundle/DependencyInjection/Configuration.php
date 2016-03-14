<?php

namespace QCharts\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('core');

        $defaultValues = [
            "line"=>"Line",
            "spline"=>"SpLine",
            "area"=>"Area",
            "pie"=>"Pìe Chart",
            "bar"=>"Bar Chart",
            "column"=>"Column Chart",
            "table"=>"Table of Contents",
            "polar_line"=>"Polar Lines",
            "polar_area"=>"Polar Area",
            "polar_spline"=>"Polar SpLine",
            "polar_bar" => "Polar Bar",
            "polar_column" => "Polar Column"
        ];

//        $defaultLimits = [
//            "row"=>1000,
//            "time"=>1
//        ];

        $snapshotsPath = "/";

        $urls = [
            "redirects"=>[
                "login" => "/login",
                "logout"=>"/logout",
                "user_profile"=>"/profile"
            ]
        ];

        $default = [
            "paths" => [
                "snapshots" => $snapshotsPath
            ]
        ];

        $rootNode
            ->treatNullLike($default)
            ->children()
                ->arrayNode('paths')
                ->info("Paths used by QCharts")
                ->treatNullLike($default["paths"])
                    ->children()
                        ->scalarNode('snapshots')
                            ->defaultValue($snapshotsPath)
                            ->info('Absolute path to locate the queries snapshots')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('urls')
                ->info('URLs to use when redirects are needed, login redirect for example')
                ->treatNullLike($urls)
                    ->children()
                        ->arrayNode('redirects')
                            ->info('key: value, array syntax')
                            ->treatNullLike($urls["redirects"])
                                ->prototype('variable')
                                ->end()
                            ->defaultValue($urls["redirects"])
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('roles')
                    ->info('Required, Roles used for the authorization, role hierarchy is up to your implementation.')
                    ->children()
                        ->scalarNode('user')
                            ->info('User that can see results')
                        ->end()
                        ->scalarNode('admin')
                            ->info('User that can add/edit/delete queries')
                        ->end()
                        ->scalarNode('super_admin')
                            ->info('User that manages the users ans snapshots console')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('charts')
                    ->info('RECOMMENDED, The type of charts supported, format = [key]: [value to display]')
                    ->treatNullLike($defaultValues)
                    ->prototype('variable')
                    ->end()
                    ->defaultValue($defaultValues)
                ->end()
                ->arrayNode('limits')
                    ->info('The limits of the query execution')
                    ->children()
                        ->floatNode('time')
                            ->info('REQUIRED, The limit time a query execution has in seconds!')
                        ->end()
                        ->integerNode('row')
                            ->info('REQUIRED, Limiting the numbers of rows from the results')
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('allow_demo_users')
                    ->defaultFalse()
                    ->info('Allow access to anonymous visitors to access the application')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
