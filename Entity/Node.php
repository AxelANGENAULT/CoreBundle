<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node
 *
 * @ORM\Table(name="ARII_NODE")
 * @ORM\Entity
 */
class Node
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
     * @ORM\Column(name="name", type="string", length=64)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Category")
     * @ORM\JoinColumn(nullable=true)
     **/
    private $category;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     **/
    private $shell;

    /**
     * @var heartbeat
     *
     * @ORM\Column(name="heartbeat", type="integer")
     */
    private $heartbeat;

    /**
     * @var status
     *
     * @ORM\Column(name="status", type="string", length=16)
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

    /**
     * Set node
     *
     * @param string $node
     * @return Node
     */
    public function setNode($node)
    {
        $this->node = $node;
    
        return $this;
    }

    /**
     * Get node
     *
     * @return string 
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Node
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
     * Set type
     *
     * @param string $type
     * @return Node
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }
}