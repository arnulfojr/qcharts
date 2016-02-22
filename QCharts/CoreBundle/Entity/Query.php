<?php

namespace QCharts\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Query
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Query
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="query", type="text")
     */
    private $query;

    /**
     *
     * @var string
     *
     * @ORM\Column(name="query_html", type="text")
     */
    private $queryHTML;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetimetz")
     */
    private $dateCreated;

    /**
     * Query constructor.
     */
    public function __construct()
    {
        $this->dateCreated = new \DateTime('now');
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set query
     *
     * @param string $query
     *
     * @return Query
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return Query
     */
    public function setDateCreated($dateCreated = null)
    {
        if (is_null($dateCreated))
        {
            $this->dateCreated = new \DateTime('now');
            return $this;
        }

        $this->dateCreated = $dateCreated;
        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set queryHTML
     *
     * @param string $queryHTML
     *
     * @return Query
     */
    public function setQueryHTML($queryHTML)
    {
        $this->queryHTML = $queryHTML;

        return $this;
    }

    /**
     * Get queryHTML
     *
     * @return string
     */
    public function getQueryHTML()
    {
        return $this->queryHTML;
    }
}
