<?php
// src/Sdz/BlogBundle/Service/AriiSQL.php

namespace Arii\CoreBundle\Service;

use Arii\CoreBundle\Entity\Audit;

class AriiAudit
{
    protected $session;
    protected $userManager;
    protected $entityManager;

    protected $when;

    public function __construct(\Arii\CoreBundle\Service\AriiSession $session,  \FOS\UserBundle\Doctrine\UserManager $userManager, \Doctrine\ORM\EntityManager $entityManager)
    {      
        $this->session = $session;
        $this->userManager = $userManager;
        $this->entityManager = $entityManager;
        $localtime = localtime(time(), true);
      //  $this->when    = sprintf();
    }

    public function AuditLog( $host,$action,$status,$module,$message )
    {   
        $username = $this->session->getUsername();
        if ($username!='') {
            $user = $this->userManager->findUserByUsername($username);
        }
        else {
            $user ='PB __FILE__ __LINE__';
            return 1;
        }
        $logTime = new \DateTime();
        
        $audit = new Audit();
        $audit->setLogtime($logTime);
        $audit->setUser($user);
        
        $audit->setIp($_SERVER['REMOTE_ADDR']);
        
        $audit->setAction($action);
        $audit->setStatus($status);
        $audit->setModule($module);
        $audit->setMessage($message);

        
        $this->entityManager->persist($audit);
        $this->entityManager->flush();

        return 0;        
    }
}
