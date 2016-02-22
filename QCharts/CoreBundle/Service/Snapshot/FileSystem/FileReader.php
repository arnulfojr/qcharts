<?php

namespace QCharts\CoreBundle\Service\Snapshot\FileSystem;


use QCharts\CoreBundle\Exception\Messages\ExceptionMessage;
use QCharts\CoreBundle\Exception\WriteReadException;

trait FileReader
{
    /**
     * @param $path
     * @return array
     * @throws WriteReadException
     */
    public function readFile($path)
    {
        $results = [];
        $isHeader = true;
        $headers = [];

        $fileOpened = fopen($path, FilesystemManager::fopenModes()["read"]);
        if ($fileOpened !== false)
        {
            while (($row = fgetcsv($fileOpened)) !== false)
            {
                if ($isHeader)
                {
                    $isHeader = false;
                    $headers = $row;
                    continue;
                }
                $results[] = array_combine($headers, $row);
            }
            fclose($fileOpened);

            return $results;
        }

        throw new WriteReadException(ExceptionMessage::FILE_NOT_READABLE, 500);
    }
}