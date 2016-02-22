<?php


namespace QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving\Saver;


use Symfony\Component\Filesystem\Filesystem;

interface SaverInterface
{

    /**
     * @param Filesystem $filesystem
     * @param $path
     * @param $fileName
     * @param array $options
     */
    public function save(Filesystem $filesystem, $path, $fileName, array $options = null);

}