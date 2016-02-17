<?php
/**
 * Created by PhpStorm.
 * User: tsp-admin
 * Date: 1/19/16
 * Time: 11:08 AM
 */

namespace QCharts\CoreBundle\Entity\User;


interface QChartsSubjectInterface
{
    /**
     * @return string
     */
    public function getUsername();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return mixed
     */
    public function getId();

    /**
     * @return mixed
     */
    public function getRoles();
}