<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Arii\CoreBundle\Entity\TeamFilter;
/**
 * Filter
 *
 * @ORM\Table(name="ARII_FILTER")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\FilterRepository")
 */
class Filter
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
    *  @ORM\OneToMany(targetEntity="Arii\CoreBundle\Entity\TeamFilter",mappedBy="filter",cascade={"all"})
    * 
    */
    private $tf;

    /**
    * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Enterprise")
    * @ORM\JoinColumn(nullable=true)
    */
    private $enterprise;

    /**
     * @var string
     *
     * @ORM\Column(name="filter", type="string", length=64)
     */
    private $filter;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=128)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="repository", type="string", length=64)
     */
    private $repository;

    /**
     * @var string
     *
     * @ORM\Column(name="spooler", type="string", length=64)
     */
    private $spooler;

    /**
     * @var string
     *
     * @ORM\Column(name="job", type="string", length=255)
     */
    private $job;

    /**
     * @var string
     *
     * @ORM\Column(name="job_chain", type="string", length=255)
     */
    private $job_chain;

    /**
     * @var string
     *
     * @ORM\Column(name="order_id", type="string", length=255)
     */
    private $order_id;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=128)
     */
    private $status;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    public function setId($id)
    {
        return $this->id = $id;
    }
    
    public function getTf()
    {
        return $this->tf;
    }
    
    public function setTf($tf)
    {
        return $this->tf = $tf;
    }
    /**
     * Set filter
     *
     * @param string $filter
     * @return Filter
     */
    public function setFilter($filter)
    {
        $this->filter = $filter;
    
        return $this;
    }

    /**
     * Get filter
     *
     * @return string 
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * Set spooler
     *
     * @param string $spooler
     * @return Filter
     */
    public function setSpooler($spooler)
    {
        $this->spooler = $spooler;
    
        return $this;
    }

    /**
     * Get spooler
     *
     * @return string 
     */
    public function getSpooler()
    {
        return $this->spooler;
    }

    /**
     * Set job
     *
     * @param string $job
     * @return Filter
     */
    public function setJob($job)
    {
        $this->job = $job;
    
        return $this;
    }

    /**
     * Get job
     *
     * @return string 
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * Set job_chain
     *
     * @param string $jobChain
     * @return Filter
     */
    public function setJobChain($jobChain)
    {
        $this->job_chain = $jobChain;
    
        return $this;
    }

    /**
     * Get job_chain
     *
     * @return string 
     */
    public function getJobChain()
    {
        return $this->job_chain;
    }

    /**
     * Set order_id
     *
     * @param string $orderId
     * @return Filter
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;
    
        return $this;
    }

    /**
     * Get order_id
     *
     * @return string 
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Filter
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Filter
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
     * Constructor
     */
    public function __construct()
    {
        $this->tf = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add tf
     *
     * @param \Arii\CoreBundle\Entity\TeamFilter $tf
     * @return Filter
     */
    public function addTf(\Arii\CoreBundle\Entity\TeamFilter $tf)
    {
        $this->tf[] = $tf;
    
        return $this;
    }

    /**
     * Remove tf
     *
     * @param \Arii\CoreBundle\Entity\TeamFilter $tf
     */
    public function removeTf(\Arii\CoreBundle\Entity\TeamFilter $tf)
    {
        $this->tf->removeElement($tf);
    }

    /**
     * Set enterprise
     *
     * @param \Arii\CoreBundle\Entity\Enterprise $enterprise
     * @return Filter
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
     * Set repository
     *
     * @param string $repository
     * @return Filter
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    
        return $this;
    }

    /**
     * Get repository
     *
     * @return string 
     */
    public function getRepository()
    {
        return $this->repository;
    }
}