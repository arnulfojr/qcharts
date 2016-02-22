<?php

namespace QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving\Saver;


use QCharts\CoreBundle\Service\Snapshot\FileSystem\SnapshotManager;
use Symfony\Component\Filesystem\Filesystem;

class TimeMachineSaver implements SaverInterface
{

    /**
     * @param Filesystem $filesystem
     * @param $path
     * @param $fileName
     * @param array|null $options
     */
    public function save(Filesystem $filesystem, $path, $fileName, array $options = null)
    {
        $origin = "{$path}/{$fileName}";

        $snapshotFileName = SnapshotManager::createNameForSnapshot();
        $target = "{$path}/{$snapshotFileName}";

        $freshFileName = SnapshotManager::FRESH_FILE;
        $freshFilePath = "{$path}/{$freshFileName}";

        $filesystem->rename($origin, $target);
        $filesystem->symlink($target, $freshFilePath);
    }

}