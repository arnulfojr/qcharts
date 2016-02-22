<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/12/16
 * Time: 4:15 PM
 */

namespace QCharts\CoreBundle\Repository\Snapshot;


use QCharts\CoreBundle\Exception\NotFoundException;
use QCharts\CoreBundle\Exception\SnapshotException;
use QCharts\CoreBundle\Service\Snapshot\FileSystem\FilesystemManager;
use QCharts\CoreBundle\Service\Snapshot\FileSystem\SnapshotManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class SnapshotRepository extends FilesystemManager
{

    use FinderLooper;
    use FinderConfigurator;

    /** @var string $snapshotPath */
    private $snapshotPath;

    /**
     * SnapshotRepository constructor.
     * @param Filesystem $filesystem
     * @param array $paths
     * @param null $snapshotPath
     */
    public function __construct(Filesystem $filesystem, array $paths, $snapshotPath = null)
    {
        parent::__construct($filesystem, $paths);
        $this->snapshotPath = $snapshotPath;
    }

    public function init()
    {
        parent::init();
    }

    /**
     * @param $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->snapshotPath = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->snapshotPath;
    }


    /**
     * @param $snapshotName
     * @return string
     * @throws SnapshotException
     */
    public function getSnapshotPathFromName($snapshotName)
    {
        try
        {
            $finder = $this->createFinderForFiles($this->snapshotPath);
            $finder->name("{$snapshotName}.csv");
            /** @var SplFileInfo $file */
            $file = $this->getFirstMatch($finder);

            return $file->getRealPath();
        }
        catch (SnapshotException $e)
        {
            throw $e;
        }
        catch (NotFoundException $e)
        {
            throw new SnapshotException($e->getMessage(), $e->getCode(), $e);
        }
    }


    /**
     * @param $snapshotName
     * @return SplFileInfo
     * @throws SnapshotException
     */
    public function getSnapshotFile($snapshotName)
    {
        try
        {
            $snapshotName = ($snapshotName === SnapshotManager::FRESH_FILE) ?
                SnapshotManager::FRESH_FILE : "{$snapshotName}.csv";

            $finder = $this->createFinderForFiles($this->snapshotPath);
            $finder->name($snapshotName);

            /** @var SplFileInfo $file */
            $file = $this->getFirstMatch($finder);

            return $file;
        }
        catch (SnapshotException $e)
        {
            throw $e;
        }
        catch (NotFoundException $e)
        {
            throw new SnapshotException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return string
     * @throws SnapshotException
     */
    public function getFreshSnapshotFileName()
    {
        try
        {
            $finder = $this->createFinderForFiles($this->snapshotPath);
            $finder->name(SnapshotManager::FRESH_FILE);
            /** @var SplFileInfo $file */
            $file = $this->getFirstMatch($finder);
            return $file->getFilename();
        }
        catch (SnapshotException $e)
        {
            throw $e;
        }
        catch (NotFoundException $e)
        {
            throw new SnapshotException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return array
     * @throws SnapshotException
     */
    public function getSnapshotPaths()
    {
        try
        {
            $finder = $this->createFinderForFiles($this->snapshotPath);
            $finder->name('*.csv');
            $finder->sortByName();

            $files = $this->getList($finder);
            $paths = [];

            foreach($files as $file)
            {
                /** @var SplFileInfo $file */
                $paths[] = $file->getRealPath();
            }

            return $paths;
        }
        catch (SnapshotException $e)
        {
            throw $e;
        }
    }

    /**
     * @return array
     * @throws SnapshotException
     */
    public function getSnapshotNames()
    {
        try
        {
            $names = [];
            $finder = $this->createFinderForFiles($this->snapshotPath);
            $finder->name('*.csv');
            $finder->sortByName();
            $files = $this->getList($finder);

            foreach ($files as $file)
            {
                /** @var SplFileInfo $file */
                $names[] = $file->getFilename();
            }

            return $names;
        }
        catch (SnapshotException $e)
        {
            throw $e;
        }
    }

    /**
     * @return string
     * @throws SnapshotException
     */
    public function getFreshSnapshotPath()
    {
        try
        {
            $finder = $this->createFinderForFiles($this->snapshotPath);
            $finder->name(SnapshotManager::FRESH_FILE);
            /** @var SplFileInfo $file */
            $file = $this->getFirstMatch($finder);
            return $file->getRealPath();
        }
        catch (SnapshotException $e)
        {
            throw $e;
        }
        catch (NotFoundException $e)
        {
            throw new SnapshotException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return array
     * @throws SnapshotException
     */
    public function getSnapshots()
    {
        try
        {
            $finder = $this->createFinderForFiles($this->snapshotPath);
            $finder->name("*.csv");
            /** @var array $files */
            $files = $this->getList($finder);
            return $files;
        }
        catch (SnapshotException $e)
        {
            throw $e;
        }
        catch (NotFoundException $e)
        {
            throw new SnapshotException($e->getMessage(), $e->getCode(), $e);
        }
    }

}