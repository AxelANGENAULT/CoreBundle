<?php
// src/Sdz/BlogBundle/Service/AriiSQL.php
 
namespace Arii\CoreBundle\Service;
use Arii\CoreBundle\Service\AriiSession;

class AriiJOE
{
    private $session;
    
    public function __construct(AriiSession $session)
    {
        $this->session = $session;
        
    }
   
    public function Job( $JobInfos ) {
        print_r($JobInfos);
        return '';
    }
}
