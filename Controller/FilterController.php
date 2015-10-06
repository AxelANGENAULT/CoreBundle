<?php
// src/Arii/AdminBundle/Controller/DBController.php
namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class FilterController extends Controller
{
    public function indexAction()
    {
        return $this->render('AriiCoreBundle:Filter:index.html.twig');
    }

    public function ribbonAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        return $this->render('AriiCoreBundle:Filter:ribbon.json.twig',array(), $response );
    }

    public function listAction()
    {
        return $this->render('AriiCoreBundle:Filter:list.html.twig');
    }
    
    public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiCoreBundle:Filter:toolbar.xml.twig",array(),$response);
    }
    
    public function addAction()
    {
        return $this->render('AriiCoreBundle:Filter:add.html.twig');
    }

    // on sauve le filtre avec l'id du user
    public function saveAction()
    {
        $request = Request::createFromGlobals();

        $session = $this->container->get('arii_core.session');
        $user_id =  $session->getUserId();
        $user = $this->getDoctrine()->getRepository("AriiUserBundle:User")->find($user_id);            
        
        $em = $this->getDoctrine()->getManager();
        $id = $request->get('id');
        if ($id != "") // nouveau filtre
        {
            $user_filter = $this->getDoctrine()->getRepository("AriiCoreBundle:UserFilter")->find($id);            
        }
        else {
            $user_filter = new \Arii\CoreBundle\Entity\UserFilter();
        }
        
        $user_filter->setUser($user);
        $user_filter->setName($request->get('name'));
        $user_filter->setDescription($request->get('description'));
        $user_filter->setSpooler($request->get('spooler'));
        $user_filter->setJobChain($request->get('job_chain'));
        $user_filter->setJob($request->get('job'));
        $user_filter->setOrderId($request->get('order_id'));
        $user_filter->setStatus($request->get('status'));
        $em->persist($user_filter);
        $em->flush();
        
        // on remet à jour la session
        $session->setUserFilters();
        return new Response("success");
    }

    public function deleteAction()
    {
        $request = Request::createFromGlobals();

        $session = $this->container->get('arii_core.session');
        $user_id =  $session->getUserId();
        $id = $request->get('id');
                
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('data');
        $qry1 = "DELETE FROM ARII_USER_FILTER WHERE id='$id' and user_id='$user_id'";
        $res = $data->sql->query($qry1);
        
        // on remet à jour la session
        $session->setUserFilters();
        return new Response("success");
    }

    public function formAction()
    {
        $dhtmlx = $this->container->get('arii_core.db');
        $form = $dhtmlx->Connector('form');
        
        $form->render_table("ARII_USER_FILTER","ID","id,name,description,job,job_chain,order_id,status,spooler");
    }
    
    public function xmlAction()
    {
        $db = $this->container->get('arii_core.db');
        $grid = $db->Connector('grid');
  
        $session = $this->container->get('arii_core.session');
        $user_id = $session->getUserId();
        
        $sql = $this->container->get('arii_core.sql');
        $sql->setDriver($this->container->getParameter('database_driver'));
        
        $qry = $sql->Select(array('ID','NAME','DESCRIPTION','SPOOLER','JOB','JOB_CHAIN','ORDER_ID','STATUS'))
            .$sql->From(array('ARII_USER_FILTER'))
            .$sql->Where(array('USER_ID'=>$user_id))
            .$sql->OrderBy(array('name'));
        $grid->render_sql($qry,"ID","NAME,DESCRIPTION,SPOOLER,JOB,JOB_CHAIN,ORDER_ID,STATUS");
    }

    public function filter_formAction()
    {
        require_once '../vendor/dhtmlx/dhtmlxConnector/codebase/form_connector.php';

        $database_host = $this->container->getParameter('database_host'); 
        $database_port = $this->container->getParameter('database_port'); 
        $database_name = $this->container->getParameter('database_name'); 
        $database_user = $this->container->getParameter('database_user'); 
        $database_password = $this->container->getParameter('database_password'); 

        $conn=mysql_connect( $database_host, $database_user,  $database_password );
        mysql_select_db( $database_name );

        $data = new \FormConnector($conn);
        $data->render_table('arii_filter',"ID","filter,title,spooler,job,job_chain,order_id,status");
    }

    public function filter_gridAction()
    {
        require_once '../vendor/dhtmlx/dhtmlxConnector/codebase/grid_connector.php';

        $database_host = $this->container->getParameter('database_host'); 
        $database_port = $this->container->getParameter('database_port'); 
        $database_name = $this->container->getParameter('database_name'); 
        $database_user = $this->container->getParameter('database_user'); 
        $database_password = $this->container->getParameter('database_password'); 

        $conn=mysql_connect( $database_host, $database_user,  $database_password );
        mysql_select_db( $database_name );

        $data = new \GridConnector($conn);
        $data->render_table('arii_filter',"ID","filter,title,spooler,job,job_chain,order_id,status");
    }

}


