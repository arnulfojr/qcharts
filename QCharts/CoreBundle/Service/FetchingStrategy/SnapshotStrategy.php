<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/3/16
 * Time: 4:44 PM
 */

namespace QCharts\CoreBundle\Service\FetchingStrategy;


use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Service\ServiceInterface\DynamicDependencies;
use QCharts\CoreBundle\Service\Snapshot\SnapshotService;

class SnapshotStrategy extends DynamicDependencies implements FetchingStrategyInterface
{

    /**
     * @param QueryRequest $queryRequest
     * @return array
     */
    public function getResults(QueryRequest $queryRequest)
    {
        /** @var SnapshotService $snapshotService */
        $snapshotService = $this->getDependencies()["SnapshotService"];

        $snapshotDecided = $this->decideSnapshot($queryRequest);

        return $snapshotService->readSnapshot($snapshotDecided);
    }

    /**
     * @param QueryRequest $queryRequest
     * @return string
     */
    protected function decideSnapshot(QueryRequest $queryRequest)
    {
        /** @var SnapshotService $snapshotService */
        $snapshotService = $this->getDependencies()["SnapshotService"];
        $snapshot = $this->getDependencies()["snapshot"];

        if (is_null($snapshot))
        {
            //return the fresh one
            return $snapshotService->getFreshSnapshot($queryRequest);
        }
        // return the specified snapshot
        return $snapshotService->getSnapshot($queryRequest, $snapshot);
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return "0";
    }
}