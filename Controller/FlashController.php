<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FlashController extends Controller
{

     public function viewAction()
    {

       $this->get('session')->getFlashBag()->add(
            'white',
            'Vos changements ont été sauvegardés!'
        );
       $this->get('session')->getFlashBag()->add(
            'red',
            'Vos changements ont été sauvegardés!'
        );
       $this->get('session')->getFlashBag()->add(
            'blue',
            'Vos changements ont été sauvegardés!'
        );
       $this->get('session')->getFlashBag()->add(
            'green',
            'Vos changements ont été sauvegardés!'
        );
       $this->get('session')->getFlashBag()->add(
            'yellow',
            'Vos changements ont été sauvegardés!'

         );
       
        return $this->render('AriiCoreBundle:Flash:view.html.twig' );
    }
   
}
