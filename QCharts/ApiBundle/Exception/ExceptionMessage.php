<?php


namespace QCharts\ApiBundle\Exception;

/**
 * Class ExceptionMessage
 * @package QCharts\ApiBundle\Exception
 */
class ExceptionMessage
{
    /**
     * @param $role
     * @return string
     */
    static public function CREDENTIALS_NOT_VALID($role)
    {
        return "The given credentials were not valid, the role '{$role}' is needed";
    }
}