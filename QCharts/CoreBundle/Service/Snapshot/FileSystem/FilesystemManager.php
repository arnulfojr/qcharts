<?php

namespace QCharts\CoreBundle\Service\Snapshot\FileSystem;


use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class FilesystemManager
 * @package QCharts\CoreBundle\Service\Snapshot\FileSystem
 */
class FilesystemManager
{
    use FileWriter;
    use FileReader;

    const QCHARTS_DIRECTORY_NAME = "qcharts";

    /** @var Filesystem $fileSystem */
    protected $fileSystem;

    /** @var array $paths */
    private $paths;

    /**
     * FilesystemManager constructor.
     * @param Filesystem $filesystem
     * @param array $paths
     */
    public function __construct(Filesystem $filesystem, array $paths)
    {
        $this->fileSystem = $filesystem;
        $this->paths = $paths;
    }

    public function init()
    {
        if (!$this->directoryExists())
        {
            $path = $this->getFullPath();
            $this->createDirectory($path);
        }
    }

    /**
     * @return array
     */
    static public function fopenModes()
    {
        return [
            "write" => "w",
            "write-read" => "w+",
            "append" => "a",
            "append-read" => "a+",
            "read" => "r",
            "read-write" => "r+"
        ];
    }

    /**
     *
     * If it receives a null value creates the base directory, if it receives a value then it appends the path to the
     * return value of the getFullPath() function
     *
     * @param null $dirName
     * @throws IOExceptionInterface
     */
    protected function createDirectory($dirName)
    {
        try
        {
            $this->fileSystem->mkdir($dirName);
        }
        catch (IOExceptionInterface $e)
        {
            throw $e;
        }
    }

    /**
     *
     * Appends the $dirName to the return value from the getFullPath() function and checks if it exists
     * @param $dirName
     * @return bool
     */
    public function exists($dirName)
    {
        return $this->fileSystem->exists("{$this->getFullPath()}/{$dirName}");
    }

    /**
     * @return bool
     */
    protected function directoryExists()
    {
        return $this->fileSystem->exists($this->getFullPath());
    }

    /**
     * @return string
     */
    public function getFullPath()
    {
        $base = FilesystemManager::QCHARTS_DIRECTORY_NAME;
        $path = "{$this->getSystemPath()}/{$base}";
        return $path;
    }

    /**
     * @return string
     */
    public function getSystemPath()
    {
        $path = $this->paths["snapshots"];
        return $path;
    }

    /**
     * @param $dirName
     */
    public function prepareDirectory($dirName)
    {
        if (!$this->exists("{$dirName}"))
        {
            $this->createDirectory("{$dirName}");
        }
    }

    /**
     * @param $filename
     * @param $content
     */
    public function dumpContents($filename, $content)
    {
        $this->fileSystem->dumpFile($filename, $content);
    }

    /**
     * @param $path
     * @param $oldName
     * @param $newName
     */
    public function renameFile($path, $oldName, $newName)
    {
        $this->fileSystem->rename("{$path}/{$oldName}", "{$path}/{$newName}");
    }

    /**
     * @param $path
     * @param $originFileName
     * @param $targetFileName
     * @throws IOException
     */
    protected function createSymlink($path, $originFileName, $targetFileName)
    {
        $origin = "{$path}/{$originFileName}";
        $target  ="{$path}/{$targetFileName}";
        $this->fileSystem->symlink($origin, $target);
    }

    /**
     * @param $path
     * @throws IOExceptionInterface
     */
    protected function removeFile($path)
    {
        $this->fileSystem->remove($path);
    }

}