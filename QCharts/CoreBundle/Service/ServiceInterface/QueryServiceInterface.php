<?php

namespace QCharts\CoreBundle\Service\ServiceInterface;

use QCharts\CoreBundle\Entity\User\QChartsSubjectInterface;
use QCharts\CoreBundle\Entity\User\User;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;

interface QueryServiceInterface
{
    /**
     * @return mixed
     */
    public function getAllQueries();

    /**
     * @param Form $form
     * @param QChartsSubjectInterface $user
     * @return mixed
     */
    public function add(Form $form, QChartsSubjectInterface $user);

    /**
     * @param FormInterface $form
     * @param QChartsSubjectInterface $user
     * @param $queryId
     * @return mixed
     */
    public function edit(FormInterface $form, QChartsSubjectInterface $user, $queryId);

    /**
     * @param $queryId
     * @return void
     */
    public function delete($queryId);

    /**
     * @param $schemaName
     * @param string $connectionName
     * @return mixed
     */
    public function getTableNames($schemaName, $connectionName = '');

    /**
     * @param $query
     * @param int $limit
     * @return mixed
     */
    public function getResultsFromQuery($query, $limit = 0);

}