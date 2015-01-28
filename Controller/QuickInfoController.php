<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class QuickInfoController extends Controller
{
    public function sessionAction()
    {
        $session = "<?xml version='1.0' encoding='utf-8' ?>\n";
        $session .= '<rows><head><afterInit><call command="clearAll"/></afterInit></head>';
        $Var['eric'] = 'test';
        foreach ($Var as $k=>$v) {
            $session .= '<row id="'.$k.'"><cell><![CDATA[ '.$k.']]></cell><cell><![CDATA[ '.$v.']]></cell></row>';
        }
        $session .= '</rows>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $session );
        return $response;
    }
    
    public function errorsAction()
    {
        return $this->render('AriiCoreBundle:Default:settings.html.twig');
    }

    public function auditAction()
    {
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('grid');
        $qry = "select logtime,action from ARII_AUDIT order by logtime desc";
        $data->render_sql($qry,"ID","logtime,action");
    }
}
