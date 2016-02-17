<?php

namespace QCharts\CoreBundle\Service\Snapshot;


use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Exception\InstanceNotFoundException;
use QCharts\CoreBundle\Exception\NotFoundException;
use QCharts\CoreBundle\Exception\OverlappingException;
use QCharts\CoreBundle\Exception\SnapshotException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Exception\WriteReadException;
use QCharts\CoreBundle\Service\FetchingStrategy\StrategyFactory;
use QCharts\CoreBundle\Service\QueryValidatorService;
use QCharts\CoreBundle\Service\Snapshot\FileSystem\SnapshotManager;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Finder\SplFileInfo;

class SnapshotService
{

    const FILE_DATE_FORMAT = "d, M, Y h:i:s";
    const FRIENDLY_FILE_NAME = 'h:i T, d M Y';
    const LIVE_MODE = 0;

    /** @var SnapshotManager $snapshotManager */
    private $snapshotManager;
    /** @var QueryValidatorService $queryValidator */
    private $queryValidator;

    public function __construct(
        SnapshotManager $snapshotManager,
        QueryValidatorService $queryValidatorService
    ) {
        $this->snapshotManager = $snapshotManager;
        $this->queryValidator = $queryValidatorService;
    }

    /**
     * @return QueryValidatorService
     */
    public function getQueryValidator()
    {
        return $this->queryValidator;
    }

    /**
     * @param QueryRequest $queryRequest
     * @param array $results
     * @throws TypeNotValidException
     * @throws WriteReadException
     */
    public function writeSnapshot(QueryRequest $queryRequest, array $results)
    {
        if ($queryRequest->getConfig()->getIsCached() == SnapshotService::LIVE_MODE)
        {
            return;
        }

        $this->snapshotManager->init();
        $this->snapshotManager->setQueryRequest($queryRequest);
        try
        {
            $this->snapshotManager->writeSnapshot($results);
        }
        catch (WriteReadException $e)
        {
            throw $e;
        }
        catch (IOException $e)
        {
            throw $e;
        }
    }

    /**
     * @param QueryRequest $queryRequest
     * @return mixed
     * @throws OverlappingException
     * @throws WriteReadException
     */
    public function updateSnapshot(QueryRequest $queryRequest)
    {
        // Mode 0 is the live mode
        $strategyFactory = new StrategyFactory();
        $strategyFactory->setQueryValidator($this->queryValidator);
        $strategy = $strategyFactory->createStrategy(SnapshotService::LIVE_MODE);
        $results = $strategy->getResults($queryRequest);
        $duration = $strategy->getDuration();

        $this->snapshotManager->init();
        $this->snapshotManager->setQueryRequest($queryRequest);

        try
        {
            $this->snapshotManager->writeSnapshot($results);
        }
        catch (WriteReadException $e)
        {
            throw $e;
        }
        catch (IOException $e)
        {
            throw $e;
        }

        return $duration;

    }

    /**
     * @param QueryRequest $queryRequest
     * @return array
     * @throws \QCharts\CoreBundle\Exception\SnapshotException
     */
    public function getSnapshotsFormatted(QueryRequest $queryRequest)
    {
        $this->snapshotManager->init();
        $this->snapshotManager->setQueryRequest($queryRequest);
        $snapshots = $this->snapshotManager->getSnapshotNames();

        $friendlyFormat = array_map(function($s) {
            $s = substr($s, 0, -4); //remove the file type
            $date = new \DateTime();
            $date->setTimestamp($s);
            return $date->format(SnapshotService::FRIENDLY_FILE_NAME);
        }, $snapshots);

        $snapshots = array_map(function($s) {
            return substr($s, 0, -4);
        }, $snapshots);

        $results = array_combine($snapshots, $friendlyFormat);

        return $results;
    }

    /**
     * @param QueryRequest $queryRequest
     * @param $snapshot
     * @return string
     * @throws TypeNotValidException
     * @throws SnapshotException
     */
    public function formatSnapshotName(QueryRequest $queryRequest, $snapshot = null)
    {

        if ($queryRequest->getConfig()->getIsCached() == 0) {
            throw new SnapshotException("Results are being call live", 500);
        }

        if (is_null($snapshot) || !is_numeric($snapshot))
        {
            $snapshots = $this->getSnapshotsFormatted($queryRequest);
            ksort($snapshots);
            $fresh = array_pop($snapshots);
            return $fresh;
        }

        $date = new \DateTime();
        $date = $date->setTimestamp($snapshot);
        return $date->format(SnapshotService::FRIENDLY_FILE_NAME);
    }

    /**
     * @param QueryRequest $queryRequest
     * @return string
     * @throws \QCharts\CoreBundle\Exception\NotFoundException
     */
    public function getFreshSnapshot(QueryRequest $queryRequest)
    {
        $this->snapshotManager->init();
        $this->snapshotManager->setQueryRequest($queryRequest);
        return $this->snapshotManager->getFreshSnapshotPath();
    }

    /**
     * @param QueryRequest $queryRequest
     * @return array
     */
    public function getSnapshots(QueryRequest $queryRequest)
    {
        $this->snapshotManager->init();
        $this->snapshotManager->setQueryRequest($queryRequest);
        return $this->snapshotManager->getSnapshotPaths();
    }

    /**
     * @param QueryRequest $queryRequest
     * @param $snapshot
     * @return string
     * @throws TypeNotValidException
     * @throws \QCharts\CoreBundle\Exception\NotFoundException
     * @throws \QCharts\CoreBundle\Exception\SnapshotException
     */
    public function getSnapshot(QueryRequest $queryRequest, $snapshot)
    {
        if (!is_numeric($snapshot))
        {
            throw new TypeNotValidException("The snapshot name was not as expected", 555);
        }
        $this->snapshotManager->init();
        $this->snapshotManager->setQueryRequest($queryRequest);
        return $this->snapshotManager->getSnapshotPathFromName($snapshot);
    }

    /**
     * @param $path
     * @return array
     * @throws WriteReadException
     */
    public function readSnapshot($path)
    {
        try
        {
            return $this->snapshotManager->readSnapshot($path);
        }
        catch (WriteReadException $e)
        {
            throw $e;
        }
    }

    /**
     * @param QueryRequest $qr
     * @param null $snapshot
     * @return \Symfony\Component\Finder\SplFileInfo
     * @throws InstanceNotFoundException
     * @throws SnapshotException
     */
    public function getSnapshotFile(QueryRequest $qr, $snapshot = null)
    {
        try
        {
            $this->snapshotManager->init();
            $this->snapshotManager->setQueryRequest($qr);

            if (is_null($snapshot))
            {
                $snapshot = $this->snapshotManager->getFreshSnapshotFileName();
            }

            return $this->snapshotManager->getSnapshotFile($snapshot);
        }
        catch (SnapshotException $e)
        {
            throw new SnapshotException("Error while fetching the file", $e->getCode(), $e);
        }
        catch (NotFoundException $e)
        {
            throw new InstanceNotFoundException("Snapshot was not found", 404, $e);
        }
    }

    /**
     * @param QueryRequest $queryRequest
     * @param SplFileInfo $snapshot
     * @throws SnapshotException
     */
    public function deleteSnapshot(QueryRequest $queryRequest, SplFileInfo $snapshot)
    {
        try
        {
            $this->snapshotManager->init();
            $this->snapshotManager->setQueryRequest($queryRequest);
            $this->snapshotManager->deleteSnapshot($snapshot);
        }
        catch (SnapshotException $e)
        {
            throw $e;
        }
    }

    /**
     * @param QueryRequest $queryRequest
     * @throws SnapshotException
     */
    public function cleanSnapshots(QueryRequest $queryRequest)
    {
        //delete all snapshots
        try
        {
            $this->snapshotManager->init();
            $this->snapshotManager->setQueryRequest($queryRequest);
            $snapshots = $this->snapshotManager->getSnapshots();
            foreach($snapshots as $snapshot)
            {
                /** @var SplFileInfo $snapshot */
                $this->snapshotManager->deleteSnapshot($snapshot);
            }
        }
        catch (SnapshotException $e)
        {
            throw $e;
        }
    }

}