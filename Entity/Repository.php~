<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Repository
 *
 * @ORM\Table(name="ARII_REPOSITORY")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\RepositoryRepository")
 */
class Repository
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
   * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Enterprise")
   * @ORM\JoinColumn(nullable=true)
   */
  private $enterprise;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $db;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=32)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=32)
     */
    private $timezone;

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
     * @return Repository
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
     * Set description
     *
     * @param string $description
     * @return Repository
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
     * Set timezone
     *
     * @param string $timezone
     * @return Repository
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    
        return $this;
    }

    /**
     * Get timezone
     *
     * @return string 
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set enterprise
     *
     * @param \Arii\CoreBundle\Entity\Enterprise $enterprise
     * @return Repository
     */
    public function setEnterprise(\Arii\CoreBundle\Entity\Enterprise $enterprise = null)
    {
        $this->enterprise = $enterprise;
    
        return $this;
    }

    /**
     * Get enterprise
     *
     * @return \Arii\CoreBundle\Entity\Enterprise 
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * Set db
     *
     * @param \Arii\CoreBundle\Entity\Connection $db
     * @return Repository
     */
    public function setDb(\Arii\CoreBundle\Entity\Connection $db = null)
    {
        $this->db = $db;
    
        return $this;
    }

    /**
     * Get db
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getDb()
    {
        return $this->db;
    }
}