<?php

namespace QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving;


use QCharts\CoreBundle\Exception\TypeNotValidException;
use QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving\Saver\SaverInterface;
use QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving\Saver\SnapshotSaver;
use QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving\Saver\TimeMachineSaver;

class SaverFactory implements SavingFactoryInterface
{

    /**
     * @param $mode
     * @return SaverInterface
     * @throws TypeNotValidException
     */
    public static function createSaver($mode)
    {
        switch ($mode)
        {
            case "Snapshot":
            case 1:
                return new SnapshotSaver();
            break;
            case "Time Machine":
            case 2:
                return new TimeMachineSaver();
            break;
            default:
                throw new TypeNotValidException("Saving Method was not valid", 500);
                break;
        }
    }
}