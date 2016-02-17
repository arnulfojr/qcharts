<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/12/16
 * Time: 4:46 PM
 */

namespace QCharts\CoreBundle\Repository\Snapshot;


use QCharts\CoreBundle\Exception\SnapshotException;
use Symfony\Component\Finder\Finder;
use \InvalidArgumentException;

trait FinderConfigurator
{
    /**
     * @param Finder $finder
     * @param $path
     * @return Finder
     */
    public function setFinderForFiles(Finder $finder, $path)
    {
        return $finder ->files()->in($path);
    }

    /**
     * @param $path
     * @return Finder
     * @throws SnapshotException
     */
    public function createFinderForFiles($path)
    {
        try
        {
            $finder = new Finder();
            $finder->files()->in($path);
            return $finder;
        }
        catch (InvalidArgumentException $e)
        {
            throw new SnapshotException($e->getMessage(), $e->getCode(), $e);
        }
    }

}