<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 2/4/16
 * Time: 1:58 PM
 */

namespace QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving;


use QCharts\CoreBundle\Service\Snapshot\FileSystem\Saving\Saver\SaverInterface;

interface SavingFactoryInterface
{

    /**
     * @param $mode
     * @return SaverInterface
     */
    public static function createSaver($mode);

}