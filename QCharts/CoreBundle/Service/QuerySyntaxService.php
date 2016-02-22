<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 12/10/15
 * Time: 9:38 AM
 */

namespace QCharts\CoreBundle\Service;


use QCharts\CoreBundle\Exception\OffLimitsException;
use QCharts\CoreBundle\Exception\ValidationFailedException;
use QCharts\CoreBundle\Validation\Validator\OffsetValidator;

class QuerySyntaxService
{

    /** @var LimitsService $limitService */
    private $limitService;

    public function __construct(LimitsService $limitsService)
    {
        $this->limitService = $limitsService;
    }

    /**
     * @param string $query
     * @param int $customLimit
     * @param int $offset
     * @return mixed|string
     */
    public function getLimitedQuery($query, $customLimit = 0, $offset = 0)
    {
        return $this->prepareQuerySyntax($query, $customLimit, $offset);
    }

    /**
     * @param string $query
     * @param int $customLimit
     * @param int $offset
     * @return mixed|string
     * @throws ValidationFailedException
     */
    public function prepareQuerySyntax($query, $customLimit = 0, $offset = 0)
    {
        $query = $this->removeSemicolon($query);
        $this->hasSemiColon($query);
        $query = $this->addLimit($query, $customLimit, $offset);
        return $query;
    }

    /**
     * @param $query
     * @return bool
     * @throws ValidationFailedException
     */
    public function hasSemiColon($query)
    {
        if (preg_match_all("/;/", $query) > 0)
        {
            throw new ValidationFailedException("Query has semicolon, please remove all semicolons in the Query.", 500);
        }

        return false;
    }

    /**
     * @param string $subject
     * @return string
     */
    public function removeLineFeeds($subject)
    {
        return str_replace("\n", ' ', $subject);
    }

    /**
     * @param $string
     * @return mixed
     */
    public function removeSemicolon($string)
    {
        if (preg_match_all("/;$/", $string) > 0)
        {
            $string = preg_replace('/;$/', '', $string);
        }
        return $string;
    }

    /**
     * @param string $query
     * @param int $limit
     * @param int $offset
     * @return string
     * @throws ValidationFailedException
     */
    public function addLimit($query, $limit = 0, $offset = 0)
    {
        $limits = $this->limitService->getLimits();
        if ($this->hasLimit($query))
        {
            $limit = $this->getLimitFromQuery($query);
            if ($limit <= 0 || $limits["row"] < $limit)
            {
                throw new ValidationFailedException("The written limit is off limits", 500);
            }
        }else {
            if ($limit <= 0 || $limits["row"] < $limit)
            {
                $limit = $limits["row"];
            }
            $offset = $this->getOffset($offset);
            $query = "({$query}) LIMIT {$limit} OFFSET {$offset};";
        }

        return $query;
    }

    /**
     * @param $offset
     * @return int
     */
    public function getOffset($offset)
    {
         try
         {
             $this->validateOffset($offset);
         }
         catch (OffLimitsException $e)
         {
             $offset = 0;
         }
         finally
         {
             return $offset;
         }
    }

    /**
     * @param $offset
     * @return bool
     * @throws \QCharts\CoreBundle\Exception\OffLimitsException
     */
    protected function validateOffset($offset)
    {
        $validator = new OffsetValidator();
        $validator->setObject($offset);
        return $validator->validate();
    }

    /**
     * @param $query
     * @return mixed
     */
    public function getLimitFromQuery($query)
    {
        $matches = [];
        preg_match_all('/(limit \d*\b)/i', $query, $matches);
        $matches = end($matches);
        $lastMatch = array_pop($matches);
        reset($matches);
        preg_match('/\d+/i', $lastMatch, $matches);
        return $matches[0];
    }

    /**
     * @param $query
     * @return bool
     */
    public function hasLimit($query)
    {
        return preg_match_all('/(limit \d*\b)/i', $query) > 0;
    }

}