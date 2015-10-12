<?php

namespace Arii\CoreBundle\Service;
use Arii\CoreBundle\Entity\Message;
use Doctrine\ORM\EntityManager;

class AriiMessage {
    
    protected $em;


    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    
    public function createMessage($content,$unique_id,$notification_id)
    {
        $message = new Message();
        $time = new \DateTime();
        
        $message->setContent($content);
        $message->setMessageID($unique_id);
        $message->setNotification($notification_id);
        $message->setTime($time);
        $message->setStatus("");
        
        $this->em->persist($message);
        $this->em->flush();
             
    }
    
}

?>
