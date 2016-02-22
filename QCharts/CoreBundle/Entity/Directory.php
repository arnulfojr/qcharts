<?php

namespace QCharts\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DirectoryAdapter
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="QCharts\CoreBundle\Repository\DirectoryRepository")
 */
class Directory
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var Directory
     *
     * @ORM\ManyToOne(targetEntity="\QCharts\CoreBundle\Entity\Directory")
     */
    private $parent;

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
     * Set name
     *
     * @param string $name
     *
     * @return Directory
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param Directory $parent
     * @return $this
     */
    public function setParent(Directory $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Directory
     */
    public function getParent()
    {
        return $this->parent;
    }
}
