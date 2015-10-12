<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Permission
 *
 * @ORM\Table(name="ARII_PERMISSION")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\PermissionRepository")
 */
class Permission
{
  /**
   * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Team")
   * @ORM\JoinColumn(nullable=true)
   */
  private $team;

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
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=255)
     */
    private $object;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_object", type="integer" )
     */
    private $id_object;

    /**
     * @var boolean
     *
     * @ORM\Column(name="R", type="boolean")
     */
    private $R;

    /**
     * @var boolean
     *
     * @ORM\Column(name="W", type="boolean")
     */
    private $W;

    /**
     * @var boolean
     *
     * @ORM\Column(name="X", type="boolean")
     */
    private $X;


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
     * @return Permission
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
     * Set descrption
     *
     * @param string $description
     * @return Permission
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
     * Set object
     *
     * @param string $object
     * @return Permission
     */
    public function setObject($object)
    {
        $this->object = $object;
    
        return $this;
    }

    /**
     * Get object
     *
     * @return string 
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set R
     *
     * @param boolean $r
     * @return Permission
     */
    public function setR($r)
    {
        $this->R = $r;
    
        return $this;
    }

    /**
     * Get R
     *
     * @return boolean 
     */
    public function getR()
    {
        return $this->R;
    }

    /**
     * Set W
     *
     * @param boolean $w
     * @return Permission
     */
    public function setW($w)
    {
        $this->W = $w;
    
        return $this;
    }

    /**
     * Get W
     *
     * @return boolean 
     */
    public function getW()
    {
        return $this->W;
    }

    /**
     * Set X
     *
     * @param boolean $x
     * @return Permission
     */
    public function setX($x)
    {
        $this->X = $x;
    
        return $this;
    }

    /**
     * Get X
     *
     * @return boolean 
     */
    public function getX()
    {
        return $this->X;
    }

    /**
     * Set id_object
     *
     * @param integer $idObject
     * @return Permission
     */
    public function setIdObject($idObject)
    {
        $this->id_object = $idObject;
    
        return $this;
    }

    /**
     * Get id_object
     *
     * @return integer 
     */
    public function getIdObject()
    {
        return $this->id_object;
    }

    /**
     * Set team
     *
     * @param \Arii\CoreBundle\Entity\Team $team
     * @return Permission
     */
    public function setTeam(\Arii\CoreBundle\Entity\Team $team = null)
    {
        $this->team = $team;
    
        return $this;
    }

    /**
     * Get team
     *
     * @return \Arii\CoreBundle\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }
}