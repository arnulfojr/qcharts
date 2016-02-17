<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 12/18/15
 * Time: 4:07 PM
 */

namespace QCharts\CoreBundle\Service\ServiceInterface;

use QCharts\CoreBundle\Entity\QueryRequest;
use Symfony\Component\Form\FormInterface;

interface QueryFormFactoryInterface
{
    /**
     * @return FormInterface
     */
    public function getForm();
    
    /**
     * @param string $type
     */
    public function setType($type);
    
    /**
     * @return string
     */
    public function getType();
    
    /**
     * @return QueryRequest
     */
    public function getQueryRequest();
    
    /**
     * @param QueryRequest $queryRequest
     */
    public function setQueryRequest(QueryRequest $queryRequest);
    
}