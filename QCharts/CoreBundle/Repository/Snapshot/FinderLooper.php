<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/12/16
 * Time: 4:21 PM
 */

namespace QCharts\CoreBundle\Repository\Snapshot;


use QCharts\CoreBundle\Exception\Messages\ExceptionMessage;
use QCharts\CoreBundle\Exception\NotFoundException;
use QCharts\CoreBundle\Exception\SnapshotException;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use \InvalidArgumentException;

trait FinderLooper
{
    /**
     * @param Finder $finder
     * @return SplFileInfo
     * @throws NotFoundException
     * @throws SnapshotException
     */
    protected function getFirstMatch(Finder $finder)
    {
        try
        {
            foreach ($finder as $file)
            {
                /** @var SplFileInfo $file */
                return $file;
            }
        }
        catch (InvalidArgumentException $e)
        {
            throw new SnapshotException(ExceptionMessage::QUERY_IS_NOT_FETCHING);
        }

        throw new NotFoundException(ExceptionMessage::INSTANCE_NOT_FOUND("The requested snapshot was not located"), 404);
    }

    /**
     * @param Finder $finder
     * @return array
     * @throws SnapshotException
     */
    protected function getList(Finder $finder)
    {
        try
        {
            $results = [];

            foreach ($finder as $file)
            {
                /** @var SplFileInfo $file */
                $results[] = $file;
            }

            return $results;
        }
        catch (InvalidArgumentException $e)
        {
            throw new SnapshotException(ExceptionMessage::QUERY_IS_NOT_FETCHING);
        }
    }

}