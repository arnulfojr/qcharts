<?php


namespace QCharts\FrontendBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class FrontendExtension extends Extension
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

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__."/../Resources/config"));
        $loader->load('services.yml');

        //var_dump($config);

        $container->setParameter('qcharts.frontend.fos_user_ui', $config["fos_user_ui"]);

    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return "qcharts_frontend";
    }

}