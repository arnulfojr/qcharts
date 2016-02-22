<?php

namespace QCharts\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use QCharts\CoreBundle\Entity\User\QChartsSubjectInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\JoinColumns;

/**
 * QueryRequest
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="QCharts\CoreBundle\Repository\QueryRepository")
 */
class QueryRequest
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
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetimetz")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateLastModified", type="datetimetz")
     */
    private $dateLastModified;

    /**
     * @var string
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\OneToOne(targetEntity = "Query")
     * @ORM\JoinColumn(name="query_id", referencedColumnName="id")
     *
     */    
    private $query;

    /**
     *
     * @ORM\ManyToOne(targetEntity = "\QCharts\CoreBundle\Entity\User\QChartsSubjectInterface")
     * @var QChartsSubjectInterface
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity = "\QCharts\CoreBundle\Entity\User\QChartsSubjectInterface")
     * @var QChartsSubjectInterface
     */
    private $modifiedLastBy;
    /**
     *
     * @ORM\OneToOne(targetEntity = "ChartConfig")
     *
     */
    private $config;

    /**
     * @var Directory
     * @ORM\ManyToOne(targetEntity = "QCharts\CoreBundle\Entity\Directory")
     */
    private $directory;

    /**
     * @var string
     * @ORM\Column(name="cron_expr", type="string", length=255)
     */
    private $cronExpression;

    /**
     * @var QChartsSubjectInterface
     * @ORM\ManyToMany(targetEntity="\QCharts\CoreBundle\Entity\User\QChartsSubjectInterface")
     * @JoinTable(
     *     name="favorites_user",
     *     joinColumns={@JoinColumn(name="query_request_id", referencedColumnName="id")},
     *     inverseJoinColumns={@JoinColumn(name="user_id", referencedColumnName="id")}
     *     )
     */
    private $favoritedBy;

    /**
     * QueryRequest constructor.
     */
    public function __construct()
    {
        $this->favoritedBy = new ArrayCollection();
        $this->dateCreated = new \DateTime('now');
        $this->dateLastModified = new \DateTime('now');
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
     * Set title
     *
     * @param string $title
     *
     * @return QueryRequest
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     *
     * @return QueryRequest
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
     * Set query
     *
     * @param Query $query
     *
     * @return QueryRequest
     */
    public function setQuery(Query $query = null)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set createdBy
     *
     * @param \QCharts\CoreBundle\Entity\User\QChartsSubjectInterface $createdBy
     *
     * @return QueryRequest
     */
    public function setCreatedBy(QChartsSubjectInterface $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return QChartsSubjectInterface
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return QueryRequest
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set config
     *
     * @param ChartConfig $config
     *
     * @return QueryRequest
     */
    public function setConfig(ChartConfig $config = null)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return ChartConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set dateLastModified
     *
     * @param \DateTime $dateLastModified
     *
     * @return QueryRequest
     */
    public function setDateLastModified($dateLastModified = null)
    {
        if (is_null($dateLastModified))
        {
            $this->dateLastModified = new \DateTime('now');
            return $this;
        }

        $this->dateLastModified = $dateLastModified;
        return $this;
    }

    /**
     * Get dateLastModified
     *
     * @return \DateTime
     */
    public function getDateLastModified()
    {
        return $this->dateLastModified;
    }

    /**
     * Set modifiedLastBy
     *
     * @param QChartsSubjectInterface $modifiedLastBy
     *
     * @return QueryRequest
     */
    public function setModifiedLastBy(QChartsSubjectInterface $modifiedLastBy = null)
    {
        $this->modifiedLastBy = $modifiedLastBy;

        return $this;
    }

    /**
     * Get modifiedLastBy
     *
     * @return QChartsSubjectInterface
     */
    public function getModifiedLastBy()
    {
        return $this->modifiedLastBy;
    }

    /**
     * Set directory
     *
     * @param \QCharts\CoreBundle\Entity\Directory $directory
     *
     * @return QueryRequest
     */
    public function setDirectory(Directory $directory = null)
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Get directory
     *
     * @return \QCharts\CoreBundle\Entity\Directory
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set cronExpression
     *
     * @param string $cronExpression
     *
     * @return QueryRequest
     */
    public function setCronExpression($cronExpression = null)
    {
        $this->cronExpression = $cronExpression;

        return $this;
    }

    /**
     * Get cronExpression
     *
     * @return string
     */
    public function getCronExpression()
    {
        return $this->cronExpression;
    }

    /**
     * Add favorite
     *
     * @param \QCharts\CoreBundle\Entity\User\QChartsSubjectInterface $user
     *
     * @return QueryRequest
     */
    public function addFavoritedBy(QChartsSubjectInterface $user)
    {
        $this->favoritedBy[] = $user;

        return $this;
    }

    /**
     * Remove favorite
     *
     * @param QChartsSubjectInterface $user
     */
    public function removeFavoritedBy(QChartsSubjectInterface $user)
    {
        $this->favoritedBy->removeElement($user);
    }

    /**
     * Get favorites
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFavoritedBy()
    {
        return $this->favoritedBy;
    }
}
