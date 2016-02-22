<?php

namespace QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving\Saver;

use QCharts\CoreBundle\Service\Snapshot\FileSystem\SnapshotManager;
use Symfony\Component\Filesystem\Filesystem;

class SnapshotSaver implements SaverInterface
{

    /**
     * @param Filesystem $filesystem
     * @param $path
     * @param $fileName
     * @param array|null $options
     */
    public function save(Filesystem $filesystem, $path, $fileName, array $options = null)
    {
        // save the writing one to timestamp format,
        // change the Symlink to the new one,
        // and delete the old one
        $timeStampName = SnapshotManager::createNameForSnapshot();
        $freshFileName = SnapshotManager::FRESH_FILE;

        $originPath = "{$path}/{$fileName}";
        $targetPath = "{$path}/{$timeStampName}";
        $freshPath = "{$path}/{$freshFileName}";

        $filesystem->rename($originPath, $targetPath); // now the written file is on timestamp format

        $filesystem->symlink($targetPath, $freshPath);

        $filesystem->remove($options["siblings"]);
    }
}