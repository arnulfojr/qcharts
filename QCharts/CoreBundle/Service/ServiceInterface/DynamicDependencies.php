<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/3/16
 * Time: 5:07 PM
 */

namespace QCharts\CoreBundle\Service\ServiceInterface;


abstract class DynamicDependencies
{
    /** @var array $dependencies */
    private $dependencies;

    /**
     * @param array $dependencies
     */
    public function setDependencies(array $dependencies)
    {
        $this->dependencies = $dependencies;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    public function hasDependency($dependencyName)
    {
        return array_key_exists($dependencyName, $this->dependencies);
    }

}