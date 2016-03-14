<?php


namespace QCharts\ApiBundle\Exception;


class ExceptionMessage
{
    static public function CREDENTIALS_NOT_VALID($role)
    {
        return "The given credentials were not valid, the role '{$role}' is needed";
    }
}