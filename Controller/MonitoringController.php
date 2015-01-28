<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class MonitoringController extends Controller
{
    protected $storage;
        
    public function indexAction()   
    {
        return $this->render('AriiCoreBundle:Monitoring:index.html.twig' );
    }
   
}
