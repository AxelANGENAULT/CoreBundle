<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ErrorsController extends Controller
{

    public function listAction()   
    {
        return $this->render('AriiCoreBundle:Errors:list.html.twig');
    }

    public function toolbarAction()   
    {
        return $this->render('AriiCoreBundle:Errors:toolbar.xlml.twig');
    }
    
public function xmlAction()   
    {
        $db = $this->container->get('arii_core.db');
        $grid = $db->Connector('grid');
        
        $qry = "select e.ID,e.LOGTIME,e.IP,e.MESSAGE,e.CODE,e.FILE,e.LINE,e.TRACE,e.USERNAME,e.IP"
                . " from ARII_ERROR_LOG e"
                . " order by e.LOGTIME desc";
        $grid->event->attach("beforeRender",array($this,"changeColor"));
        $grid->render_sql($qry,"ID","LOGTIME,MESSAGE,CODE,FILE,LINE,TRACE,USERNAME,IP");
    }
    
    function changeColor($row)
    {
        if($row->get_value('CODE')=="ERROR")
        {   
            $row->set_row_style("background-color: #fbb4ae;");
        } 
        if($row->get_value('CODE')=="SUCCESS") {
            $row->set_row_style("background-color: #ccebc5;");    
        }

    }

}
