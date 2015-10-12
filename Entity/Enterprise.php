<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enterprise
 *
 * @ORM\Table(name="ARII_ENTERPRISE")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\EnterpriseRepository")
 */
class Enterprise
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="enterprise", type="string", length=100)
     */
    private $enterprise;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="maildomain", type="string", length=100)
     */
    private $maildomain;
    
    /**
     * @var string
     *
     * @ORM\Column(name="modules", type="string", length=255,nullable=true)
     */
    private $modules;


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
     * Set enterprise
     *
     * @param string $enterprise
     * @return Enterprise
     */
    public function setEnterprise($enterprise)
    {
        $this->enterprise = $enterprise;
    
        return $this;
    }

    /**
     * Get enterprise
     *
     * @return string 
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * Set modules
     *
     * @param string $modules
     * @return Enterprise
     */
    public function setModules($modules)
    {
        $this->modules = $modules;
    
        return $this;
    }

    /**
     * Get modules
     *
     * @return string 
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Enterprise
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
     * Set maildomain
     *
     * @param string $maildomain
     * @return Enterprise
     */
    public function setMaildomain($maildomain)
    {
        $this->maildomain = $maildomain;
    
        return $this;
    }

    /**
     * Get maildomain
     *
     * @return string 
     */
    public function getMaildomain()
    {
        return $this->maildomain;
    }
}