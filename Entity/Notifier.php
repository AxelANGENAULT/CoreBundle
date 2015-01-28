<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notifier
 *
 * @ORM\Table(name="ARII_NOTIFIER")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\NotifierRepository")
 */
class Notifier
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
     * @ORM\Column(name="title", type="string", length=50)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     *
     * @ORM\ManyToMany(targetEntity="Arii\CoreBundle\Entity\Notification",mappedBy="notifiers") 
     * 
     */
    private $notifications;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     */
    private $connection;
    
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
     * @return Notifier
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
     * Set description
     *
     * @param string $description
     * @return Notifier
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
     * Constructor
     */
    public function __construct()
    {
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add notifications
     *
     * @param \Arii\CoreBundle\Entity\Notification $notifications
     * @return Notifier
     */
    public function addNotification(\Arii\CoreBundle\Entity\Notification $notifications)
    {
        $this->notifications[] = $notifications;
    
        return $this;
    }

    /**
     * Remove notifications
     *
     * @param \Arii\CoreBundle\Entity\Notification $notifications
     */
    public function removeNotification(\Arii\CoreBundle\Entity\Notification $notifications)
    {
        $this->notifications->removeElement($notifications);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set connection
     *
     * @param \Arii\CoreBundle\Entity\Connection $connection
     * @return Notifier
     */
    public function setConnection(\Arii\CoreBundle\Entity\Connection $connection = null)
    {
        $this->connection = $connection;
    
        return $this;
    }

    /**
     * Get connection
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getConnection()
    {
        return $this->connection;
    }
}