<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class AuditController extends Controller
{

    public function listAction()   
    {
        return $this->render('AriiCoreBundle:Audit:list.html.twig');
    }

    public function toolbarAction()   
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render('AriiCoreBundle:Audit:toolbar.xml.twig', array(), $response );
    }

    public function xmlAction()   
    {
        $db = $this->container->get('arii_core.db');
        $grid = $db->Connector('grid');
        
        $qry = "select a.ID,a.LOGTIME,a.IP,a.ACTION,a.STATUS,a.MODULE,u.USERNAME"
                . " from ARII_AUDIT a"
                . " left join ARII_USER u"
                . " on a.user_id=u.id"
                . " order by a.LOGTIME desc";
        $grid->event->attach("beforeRender",array($this,"changeColor"));
        $grid->render_sql($qry,"ID","LOGTIME,MODULE,ACTION,STATUS,USERNAME,IP");
    }
    
    function changeColor($row)
    {
        if($row->get_value('STATUS')=="ERROR")
        {   
            $row->set_row_style("background-color: #fbb4ae;");
        } 
        if($row->get_value('STATUS')=="SUCCESS") {
            $row->set_row_style("background-color: #ccebc5;");    
        }
        $row->set_value('ACTION',str_replace(array('<','>'), array('&lt;','&gt;'), $row->get_value('ACTION')));
    }
    
    public function detailAction()
    {
        $request = Request::createFromGlobals();
        $id = $request->query->get( 'id' );
        
        $dhtmlx = $this->container->get('arii_core.db');
        $data = $dhtmlx->Connector('grid');
        
        $sql = $this->container->get('arii_core.sql');

        $qry = "select a.ID,a.LOGTIME,a.IP,a.ACTION,a.MESSAGE,a.STATUS,a.MODULE,u.USERNAME"
                . " from ARII_AUDIT a"
                . " left join ARII_USER u"
                . " on a.user_id=u.id";
        if ($id>0) {
            $qry .= $sql->Where(array('a.ID'=>$id));
        }
        else {
            $qry .= " where a.ID in (select max(ID) from ARII_AUDIT)";
        }
            
        $res = $data->sql->query( $qry );
        $Infos = $data->sql->get_next($res);
        // if (isset($Infos['LOG'])) $Infos['LOG'] = substr($Infos['LOG'],50); 
        return $this->render('AriiCoreBundle:Audit:detail.html.twig', $Infos);
     }  
     
    public function chartAction()
    {
        $db = $this->container->get('arii_core.db');        
        $chart = $db->Connector('chart');
        $sql = $this->container->get('arii_core.sql');
        $sql->setDriver($this->container->getParameter('database_driver'));
        
        $qry = $sql->Select(array('ID','LOGTIME','STATUS','count(ID) as NB'))
                .$sql->From(array('ARII_AUDIT'))
                .$sql->GroupBy(array('STATUS'));
        
        $chart->event->attach("beforeRender",array($this,"rowColor"));
        $chart->render_sql($qry,'ID','LOGTIME,STATUS,NB,COLOR');     
    }

    function rowColor($row)
    {
        if ($row->get_value('STATUS')=="ERROR")
        {   
            $row->set_value('COLOR', '#fbb4ae');
        }
        elseif ($row->get_value('STATUS')=="SUCCESS") {
            $row->set_value('COLOR', '#ccebc5');
        }
        else {
            $row->set_value('COLOR', '#00cc33');
        }
        $row->set_value( 'LOGTIME', strtotime($row->get_value( 'LOGTIME' ))/3600);
    }

}
