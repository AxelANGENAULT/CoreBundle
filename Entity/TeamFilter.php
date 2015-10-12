<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team_Filter
 *
 * @ORM\Table(name="ARII_TEAM_FILTER")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\TeamFilterRepository")
 */

class TeamFilter
{
  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Team",inversedBy="tf")
   * @ORM\JoinColumn(name="team_id",referencedColumnName="id")
   */
  private $team;
 
  /**
   * @ORM\Id
   * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Filter",inversedBy="tf")
   * @ORM\JoinColumn(name="filter_id",referencedColumnName="id")
   */
  private $filter;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

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
     * Set name
     *
     * @param string $name
     * @return Team_Filter
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
     * @return Team_Filter
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
     * Set R
     *
     * @param boolean $r
     * @return Team_Filter
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
     * @return Team_Filter
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
     * @return Team_Filter
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
     * Set team
     *
     * @param \Arii\CoreBundle\Entity\Team $team
     * @return Team_Filter
     */
    public function setTeam(\Arii\CoreBundle\Entity\Team $team)
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

    /**
     * Set filter
     *
     * @param \Arii\CoreBundle\Entity\Filter $filter
     * @return Team_Filter
     */
    public function setFilter(\Arii\CoreBundle\Entity\Filter $filter)
    {
        $this->filter = $filter;
    
        return $this;
    }

    /**
     * Get filter
     *
     * @return \Arii\CoreBundle\Entity\Filter 
     */
    public function getFilter()
    {
        return $this->filter;
    }

}