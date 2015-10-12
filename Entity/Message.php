<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Message
 *
 * @ORM\Table(name="ARII_MESSAGE",uniqueConstraints={@ORM\UniqueConstraint(name="message_uniqueID", columns={"messageID"})})
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\MessageRepository")
 */
class Message
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
     *
     * @var integer
     * 
     * @ORM\Column(name="messageID",type="integer")
     * 
     * 
     */
    private $messageID;
    
    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=128)
     */
    private $subject;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="string", length=255)
     */
    private $content;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=20)
     */
    private $status;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="time", type="datetime")
     * 
     */
    private $time;
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Notification") 
     * @ORM\JoinColumn(nullable=true)
     * 
     */
    private $notification;
    
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
     * Set content
     *
     * @param string $content
     * @return Message
     */
    public function setContent($content)
    {
        $this->content = $content;
    
        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Message
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
     * Set messageID
     *
     * @param integer $messageID
     * @return Message
     */
    public function setMessageID($messageID)
    {
        $this->messageID = $messageID;
    
        return $this;
    }

    /**
     * Get messageID
     *
     * @return integer 
     */
    public function getMessageID()
    {
        return $this->messageID;
    }

    /**
     * Set notification
     *
     * @param \Arii\CoreBundle\Entity\Notification $notification
     * @return Message
     */
    public function setNotification(\Arii\CoreBundle\Entity\Notification $notification = null)
    {
        $this->notification = $notification;
    
        return $this;
    }

    /**
     * Get notification
     *
     * @return \Arii\CoreBundle\Entity\Notification 
     */
    public function getNotification()
    {
        return $this->notification;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     * @return Message
     */
    public function setTime($time)
    {
        $this->time = $time;
    
        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    
        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }
}