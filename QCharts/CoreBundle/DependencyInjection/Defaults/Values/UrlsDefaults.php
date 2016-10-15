<?php

namespace QCharts\CoreBundle\DependencyInjection\Defaults\Values;

/**
 *
 * Contains the defaul values of the URL's
 *
 * Class UrlsDefaults
 * @package QCharts\CoreBundle\DependencyInjection\Defaults\Values
 */
class UrlsDefaults implements DefaultsInterface
{
    /**
     * Returns an array containing the default values for the urls
     * @return array
     */
    static function getValue()
    {
        return [
            "redirects" => [
                "login" => "/login",
                "logout" => "/logout",
                "user_profile" => "/profile",
            ],
        ];
    }
}