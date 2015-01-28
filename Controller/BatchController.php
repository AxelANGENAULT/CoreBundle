<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class BatchController extends Controller
{
    # -------------------------------------------------------
    # PAGES
    # -------------------------------------------------------
    public function listAction()
    {
        return $this->render('AriiCoreBundle:Batch:list.html.twig');
    }

    public function installerAction($mode='new',$id=-1)
    {
        // en cas d'edit on retrouve l'id par le request (demandé par dhtmlx)
        if ($mode == 'edit') {
            $request = Request::createFromGlobals();
             if ($request->get('id')) {
                 $id = $request->get('id' );
             }
        }
        return $this->render('AriiCoreBundle:Batch:installer.html.twig', array( 'mode'=> $mode, 'id' => $id ));
    }
    
    # -------------------------------------------------------
    # DHTMLX
    # -------------------------------------------------------
    public function contextmenuAction()
    {
         return $this->render('AriiCoreBundle:Batch:contextmenu.html.twig');
    }

    public function toolbarEditAction()
    {
        return $this->render('AriiCoreBundle:Batch:toolbar_edit.html.twig' );
    }

    public function toolbarListAction()
    {
        return $this->render('AriiCoreBundle:Batch:toolbar_list.html.twig');
    }

    public function gridAction()
    {
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('grid');
        $data->render_table('arii_installer');
    }

    public function formAction()
    {
         $dhtmlx = $this->container->get('arii_core.dhtmlx');
         $data = $dhtmlx->Connector('form');
         $data->event->attach('beforeRender', array( $this, "reorganize") );
         $data->render_table('arii_installer');
    }

    function reorganize($data){
        $request = Request::createFromGlobals();
        // on modifie les resultats de select
        $database = $data->get_value('databaseDbms');
        // on refait les champs en fonction de la base de données
        foreach (array('databaseHost','databasePort','databaseSchema','databaseUser','databasePassword','connectorJTDS','connector') as $k ) {
            if ($request->get($database.'_'.$k)) {
                $data->set_value($k, $request->get($database.'_'.$k) );
            }
        }
    }

    # -------------------------------------------------------
    # PAGES
    # -------------------------------------------------------
    public function xmlAction()
    {
       exit();
    }

}
