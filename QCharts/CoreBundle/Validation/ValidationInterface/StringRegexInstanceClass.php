<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/8/16
 * Time: 2:34 PM
 */

namespace QCharts\CoreBundle\Validation\ValidationInterface;


abstract class StringRegexInstanceClass
{
    public function stringHasRegexInstance(array $arrayOfStrings, $pattern)
    {
        return (count(preg_grep($pattern, $arrayOfStrings)) > 0);
    }

    public function wholeStringHasRegexInstance($string)
    {
        return preg_match_all("/;/", $string) > 0;
    }

}