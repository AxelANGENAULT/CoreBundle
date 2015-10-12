<?php

namespace Arii\CoreBundle\Service;
use Arii\CoreBundle\Entity\ErrorLog;
use Doctrine\ORM\EntityManager;

class AriiLog {
    
    protected $em;
    protected $ariiSession;

    public function __construct(EntityManager $em, AriiSession $ariiSession) {
        $this->em = $em;
        $this->ariiSession = $ariiSession;
    }
    
    public function createLog($Message,$Code,$File,$Line,$Trace,$ip)
    {
        $errorlog = new ErrorLog();
        $username = $this->ariiSession->getUsername(); 
        $date = new \DateTime();
        
        $errorlog->setLogtime($date);
        $errorlog->setUsername($username);
        $errorlog->setMessage($Message);
        $errorlog->setCode($Code);
        $errorlog->setFile($File);
        $errorlog->setLine($Line);
        $errorlog->setTrace($Trace);
        $errorlog->setIp($ip);
        
        $this->em->persist($errorlog);
        $this->em->flush();
    }
    
    public function Error($Message,$Code,$File,$Line,$Trace)
    {
        $errorlog = new ErrorLog();
        $username = $this->ariiSession->getUsername(); 
        $date = new \DateTime();
        
        $errorlog->setLogtime($date);
        $errorlog->setUsername($username);
        $errorlog->setMessage($Message);
        $errorlog->setCode($Code);
        $errorlog->setFile($File);
        $errorlog->setLine($Line);
        $errorlog->setTrace($Trace);
        $errorlog->setIp($_SERVER['REMOTE_ADDR']);
        
        $this->em->persist($errorlog);
        $this->em->flush();
    }

    public function generateAriiLog(array $parameters)
    {
        $errorlog = new ErrorLog();
        
        $username = $this->ariiSession->getUsername();
        $date = new \DateTime();
        $ip = "127.0.0.1";
        
        $errorlog->setUsername($username);
        $errorlog->setLogtime($date);
        $errorlog->setIp($ip);
        
        foreach ($parameters as $key => $value)
        {
            $function = "set".ucwords(strtolower($key));
            if(method_exists($errorlog, $function)){
                $errorlog->$function($value);
            }
        }
        
        $this->em->persist($errorlog);
        $this->em->flush();
        
    }
}

?>
