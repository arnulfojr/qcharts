<?php

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