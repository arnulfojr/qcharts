<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/5/16
 * Time: 3:05 PM
 */

namespace QCharts\CoreBundle\ResultFormatter;



interface UserFormatterInterface
{

    /**
     * @param array $users
     * @return array
     */
    public function formatUsers(array $users);

}