<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/3/16
 * Time: 2:54 PM
 */

namespace QCharts\CoreBundle\Service\Snapshot\FileSystem;


use QCharts\CoreBundle\Exception\Messages\ExceptionMessage;
use QCharts\CoreBundle\Exception\WriteReadException;

trait FileWriter
{
    /**
     * @param $path
     * @param $content
     * @throws WriteReadException
     */
    public function writeFile($path, $content)
    {
        $fileOpened = fopen($path, FilesystemManager::fopenModes()["write"]);
        if ($fileOpened !== false)
        {
            fputcsv($fileOpened, array_keys($content[0]));
            foreach ($content as $row)
            {
                fputcsv($fileOpened, $row);
            }
            fclose($fileOpened);
            return;
        }

        throw new WriteReadException(ExceptionMessage::FILE_NOT_WRITABLE, 500);
    }

}