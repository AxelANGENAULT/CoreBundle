<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiCoreBundle:Admin:index.html.twig');
    }
    
    public function flashbagAction()
    {
       $Color = array (
            'error' => 'red', 
            'success' => 'green',
            'warning' => 'yellow',
            'info' => 'blue' );
       $Flashbag = array();
       foreach ( $this->get('session')->getFlashBag()-> All() as $f=>$Msg ) {
            $color = 'white';
            if (isset($Color[$f]))
                $color = $Color[$f];
            foreach( $Msg as $msg ) {
               $Flashbag[$msg] = $color;
            }
       }
       return $this->render('AriiCoreBundle:Admin:flashbag.html.twig', array('Flashbag'=>$Flashbag) );
    }

}
