<?php

namespace QCharts\CoreBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

abstract class DynamicEntityManager
{
    /** @var EntityManagerInterface $em */
    private $em;

    public function getEntityManager()
    {
        return $this->em;
    }

    public function setEntityManager(EntityManagerInterface $entityManagerInterface)
    {
        $this->em = $entityManagerInterface;
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        /** @var Connection $con */
        $con = $this->em->getConnection();
        $con = $this->pingIt($con);
        return $con;
    }

    protected function pingIt(Connection $con)
    {
        if ($con->ping() === false)
        {
            $con->close();
            $con->connect();
        }
        return $con;
    }

}