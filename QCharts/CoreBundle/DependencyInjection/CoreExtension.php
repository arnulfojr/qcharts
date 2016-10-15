<?php

namespace QCharts\CoreBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class CoreExtension extends Extension
{
    /**
     * Loads a specific configuration.
     *
     * @param array $configs An array of configuration values
     * @param ContainerBuilder $container A ContainerBuilder instance
     *
     * @throws \InvalidArgumentException When provided tag is not defined in this extension
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('qcharts.paths', $config["paths"]);
        $container->setParameter('qcharts.limits', $config["limits"]);
        $container->setParameter('qcharts.chart_types', $config["charts"]);
        $container->setParameter('qcharts.user_roles', $config["roles"]);
        $container->setParameter('qcharts.urls', $config["urls"]);
        $container->setParameter('qcharts.variables', $config);
        $container->setParameter('qcharts.allow_demo_users', $config["allow_demo_users"]);
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return "qcharts";
    }

}