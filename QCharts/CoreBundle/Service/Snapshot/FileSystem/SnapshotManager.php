<?php

namespace QCharts\CoreBundle\Service\Snapshot\FileSystem;

use QCharts\CoreBundle\Entity\QueryRequest;
use QCharts\CoreBundle\Exception\SnapshotException;
use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Repository\Snapshot\SnapshotRepository;
use QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving\Saver\SaverInterface;
use QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving\SaverFactory;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use \DateTime;

/**
 *
 * This service should manage the snapshots concerning the QueryRequest
 *
 * Class SnapshotManager
 * @package QCharts\CoreBundle\Service\Snapshot\Filesystem
 */
class SnapshotManager extends SnapshotRepository
{
    const SNAPSHOT_DIRECTORY = "snapshots";
    const WRITING_FILE = 'writing';
    const FRESH_FILE = 'fresh';

    /** @var QueryRequest $queryRequest */
    private $queryRequest;

    /**
     * SnapshotManager constructor.
     * @param Filesystem $filesystem
     * @param array $paths
     */
    public function __construct(Filesystem $filesystem, array $paths)
    {
        parent::__construct($filesystem, $paths);
    }

    /**
     * @param QueryRequest|null $queryRequest
     */
    public function init(QueryRequest $queryRequest = null)
    {
        //check if this level exists
        parent::init();

        $this->setPath($this->getFullPath());

        (!is_null($queryRequest)) ? $this->setQueryRequest($queryRequest) : null;
        $this->prepareDirectory($this->getFullPath());
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        $dir = SnapshotManager::SNAPSHOT_DIRECTORY;
        $path = parent::getFullPath();
        return "{$path}/{$dir}";
    }

    /**
     * @return string
     */
    public static function createNameForSnapshot()
    {
        $date = new DateTime();
        $date = $date->getTimestamp();
        $fileName = "{$date}.csv";
        return $fileName;
    }

    /**
     * @param array $results
     * @throws TypeNotValidException
     * @throws \QCharts\CoreBundle\Exception\WriteReadException
     */
    public function writeSnapshot(array $results)
    {
        $this->prepareDirectory("{$this->getSnapshotsPath()}");
        $fileName = SnapshotManager::WRITING_FILE;
        $filePath = "{$this->getSnapshotsPath()}/{$fileName}";

        try
        {
            // save it!
            $this->writeFile($filePath, $results);

            /** @var SaverInterface $saver */
            $saver = SaverFactory::createSaver($this->queryRequest->getConfig()->getIsCached());

            $fileSiblings = $this->getSnapshotPaths();
            $options = ["siblings" => $fileSiblings];

            $saver->save($this->fileSystem, $this->getSnapshotsPath(), SnapshotManager::WRITING_FILE, $options);
        }
        catch (TypeNotValidException $e)
        {
            throw new TypeNotValidException("Type when creating the snapshot was not valid", 500, $e);
        }
        catch (IOException $e)
        {
            //creating symlink was not possible or mostly not probable the filename already exists in the same context
            throw $e;
        }
    }

    /**
     * @param $path
     * @return array
     */
    public function readSnapshot($path)
    {
        return $this->readFile($path);
    }

    /**
     * @param SplFileInfo $file
     * @throws SnapshotException
     */
    public function deleteSnapshot(SplFileInfo $file)
    {
        try
        {
            $this->removeFile($file->getRealPath());
        }
        catch (IOExceptionInterface $e)
        {
            throw new SnapshotException("File couldn't be deleted, {$e->getMessage()}", $e->getCode(), $e);
        }
    }

    /**
     * @return string
     */
    protected function getBasePath()
    {
        $name = SnapshotManager::SNAPSHOT_DIRECTORY;
        return "{$name}";
    }

    /**
     * @return string
     */
    protected function getSnapshotsPath()
    {
        return "{$this->getFullPath()}/{$this->queryRequest->getId()}";
    }

    /**
     * @return QueryRequest
     */
    public function getQueryRequest()
    {
        return $this->queryRequest;
    }

    /**
     * @param QueryRequest $queryRequest
     */
    public function setQueryRequest(QueryRequest $queryRequest)
    {
        $this->queryRequest = $queryRequest;
        $this->setPath($this->getSnapshotsPath());
    }

}