<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function indexAction()
    {   
        $me = $this->container->get('security.context')->getToken()->getUsername();    
        $dhtmlx = $this->container->get('arii_core.db');
        $data = $dhtmlx->Connector('data');
        $qry = 'select * from ARII_USER where username = "'.$me.'"';
        $res = $data->sql->query( $qry );
        $line = $data->sql->get_next( $res );
        return $this->render('AriiCoreBundle:User:index.html.twig', $line );
    }

    public function ribbonAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        return $this->render('AriiCoreBundle:User:ribbon.json.twig',array(), $response );
    }

    public function saveAction()
    {   
        $me = $this->container->get('security.context')->getToken()->getUsername();    

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($me);
        if (isset($_POST['inputEmail']))
            $user->setEmail($_POST['inputEmail']);
        if (isset($_POST['inputFirstName']))
            $user->setFirstName($_POST['inputFirstName']);
        if (isset($_POST['inputLastName'] )) {
            $user->setLastName($_POST['inputLastName']);
        }
        $userManager->updateUser($user);
        $t = $this->get('translator')->trans('Profile updated');
        return new Response($t);
    }

    public function passwordAction()
    {   
        $me = $this->container->get('security.context')->getToken()->getUsername();    

        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($me);
        if (isset($_POST['inputPassword']))
            $user->setPlainPassword($_POST['inputPassword']);
        $userManager->updateUser($user);
        $t = $this->get('translator')->trans('Password updated');
        return new Response($t);
    }

    public function toolbarAction()
    {        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiCoreBundle:User:toolbar.xml.twig', array(), $response );
    }

    public function sessionAction()
    {        
        return $this->render('AriiCoreBundle:User:session.html.twig');
    }

    public function session_xmlAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiCoreBundle:User:session.xml.twig',array(),$response);
    }
}
