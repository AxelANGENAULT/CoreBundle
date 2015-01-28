<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notification
 *
 * @ORM\Table(name="ARII_NOTIFICATION")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\NotificationRepository")
 */
class Notification
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
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Team")
     * @ORM\JoinColumn(nullable=true)
     * 
     */
    private $team;
    
    /**
     * @ORM\ManyToMany(targetEntity="Arii\CoreBundle\Entity\Notifier",inversedBy="notifications")
     * @ORM\JoinTable(name="ARII_NOTIFICATION_NOTIFIER")
     */
    private $notifiers;
    
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
     * @return Notification
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
        $this->notifiers = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set team
     *
     * @param \Arii\CoreBundle\Entity\Team $team
     * @return Notification
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

    /**
     * Add notifiers
     *
     * @param \Arii\CoreBundle\Entity\Notifier $notifiers
     * @return Notification
     */
    public function addNotifier(\Arii\CoreBundle\Entity\Notifier $notifiers)
    {
        $this->notifiers[] = $notifiers;
    
        return $this;
    }

    /**
     * Remove notifiers
     *
     * @param \Arii\CoreBundle\Entity\Notifier $notifiers
     */
    public function removeNotifier(\Arii\CoreBundle\Entity\Notifier $notifiers)
    {
        $this->notifiers->removeElement($notifiers);
    }

    /**
     * Get notifiers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotifiers()
    {
        return $this->notifiers;
    }
}