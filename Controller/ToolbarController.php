<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ToolbarController extends Controller
{
    public function globalAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        
        return $this->render('AriiCoreBundle:Toolbar:global.xml.twig',array(), $response );
    }

    public function global2Action()
    {
        // date de reference
        $session = $this->container->get('arii_core.session');
        $ref_date = $session->getRefDate();

        $refresh = $session->getRefresh();
        
        // databases 
        $db = $session->getDatabase();
        $database = $db['name'];
        $Databases = $session->getDatabases();
        foreach($Databases as $f) {
            $databases[$f['id']] = $f['name'];
        }        
        
        // filtres
        $Filters = $session->getFilters();
        $filter = $session->getFilter();
        foreach($Filters as $f) {
            $filters[$f['id']] = $f['name'];
        }
        $filter_str = $session->get('filter_str');
        
        // spoolers
        $CurrentSpooler = $session->getSpooler();
        $spooler = $CurrentSpooler['name'];
        if ($spooler=='%') {
            $spooler = 'spooler.all';
        }        
        $Spoolers = $session->getSpoolers();
        
        // sites
        $site = $session->getSite();
        $Sites = $session->getSites();
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        
        return $this->render('AriiCoreBundle:Toolbar:global.xml.twig', array( 'Sites' => $Sites, 'site' => $site, 'spoolers' => $Spoolers, 'spooler' => $spooler, 'filter_str' => $filter_str, 'refresh' => $refresh, 'ref_date' => $ref_date, 'database' => $database, 'databases' => $databases, 'filter' => $filter, 'filters' => $filters ), $response );
    }

    public function FiltersAction() {
        return $this->render('AriiCoreBundle:Filter:toolbar.html.twig');
    }
    
    public function Filter_addAction() {
        return $this->render('AriiCoreBundle:Filter:toolbar_add.html.twig');
    }

    public function getrefreshAction() {
        $session = $this->getRequest()->getSession();
        if ($session->get('ref_past' ))
            $Default['ref_past'] = $session->get('ref_past' );
        else
            $Default['ref_past'] = 2;
    }

    public function SendAction()
    {
        # La barre se remet à la derniere heure connue
        $session = $this->getRequest()->getSession();
        if ($session->get('ref_date' )) {
            $Default['ref_date'] = $session->get('ref_date' );
        }
        else {
            $Time = localtime(time(),true);
            $Default['ref_date'] = sprintf("%04d-%02d-%02d %02d:%02d", $Time['tm_year']+1900, $Time['tm_mon']+1, $Time['tm_mday'], $Time['tm_hour'], $Time['tm_min'] );
            $session->set( 'ref_date', $Default['ref_date'] );
        }
        if ($session->get('ref_past' ))
            $Default['ref_past'] = $session->get('ref_past' );
        else {
            $Default['ref_past'] = 2;
            $session->set( 'ref_past', $Default['ref_past'] );
        }
        if ($session->get('ref_future' ))
            $Default['ref_future'] = $session->get('ref_future' );
        else {
            $Default['ref_future'] = 4;
            $session->set( 'ref_future', $Default['ref_future'] );
        }
        if ($session->get('ref_refresh' ))
            $Default['ref_refresh'] = $session->get('ref_refresh' );
        else {
            $Default['ref_refresh'] = 30;
            $session->set( 'ref_refresh', $Default['ref_refresh'] );
        }
        if ($session->get('Status' )) {
            $Status = $session->get('Status');
        }
        else {
            $Status = array();
            foreach (array( 'STOPPED','FAILURE','SUCCESS','RUNNING','LATE' ) as $s ) {
                $Status[$s] = 0;
            }
            $session->set( 'Status', $Status );
        }

        // Bug DHTMLX ?
        $NewStatus=array();
        foreach (array( 'STOPPED','FAILURE','SUCCESS','RUNNING','LATE' ) as $s) {
            if (isset($Status[$s]) && ($Status[$s]>0)) {
                $NewStatus[$s] = 'selected="true"';
            }
            else {
                $NewStatus[$s] = '';                  
            }
        }
        $Default['status'] = $NewStatus;
        
        # On recupere les spoolers 
        if ($session->get('spooler' )) {
            $spooler = $session->get('spooler');
        }
        else {
            $spooler = '%';
            $session->set( 'spooler', $spooler );
        }
        
        $spoolers = array( 'Christine', 'Pavilion', 'Scheduler', 'SPP' ); 
        foreach ($spoolers as $s) {
            if ($s==$spooler) {
                $Spooler[$s] = "selected='true'";
            }
            else {
                $Spooler[$s] = '';                
            }
        }
        $Default['spoolers'] = $Spooler;

        if ($session->get('custom_spooler' )) {
            $spooler = $session->get('custom_spooler');
        }
        else {
            $session->set( 'custom_spooler', $spooler );
        }
        $Default['custom_spooler'] = $spooler;
        
        
        # On recupere les filtres
/*
        if ($session->get('filter' )) {
            $filter = $session->get('filter');
        }
        else {
            $filter = -1;
            $session->set( 'filter', $filter );
            $Filters = array( 'spooler' => '%', 'job_name' => '%', 'job_chain' => '%', 'status' => '%' );

        }
*/           
        foreach (array('5', '30', '60', '300', '900', '3600', '36000') as $r ) {
            if ($r == $Default['ref_refresh'] ) 
                 $Default['refresh_'.$r] = "selected='true'";
            else 
                 $Default['refresh_'.$r] = '';
        }
        
        # On recupere les filtres
        $repository = $this->getDoctrine()
        ->getManager()
        ->getRepository('AriiCoreBundle:Filter');
        
        foreach ($repository->findAll() as $filter) {
            $k = $filter->getId();
            $v = utf8_decode( $filter->getFilter() );
            $Filters[$k] = $v;
        } 
        $Default['filters'] = $Filters;
        
        return $this->render('AriiCoreBundle:Default:toolbar.html.twig', $Default );
    }

    public function updateAction()
    {
        $request = Request::createFromGlobals();
        $session = $this->container->get('arii_core.session');
        $GlobalParams = array( 'ref_date', 'ref_past', 'ref_future', 'ref_refresh', 'spooler', 'custom_spooler' );
        foreach ($GlobalParams as $p) {
            if ($request->query->get( $p )) 
                $session->set( $p, $request->query->get( $p ) );
        }
        print $session->get('ref_past');
        foreach ( array('STOPPED','FAILURE','SUCCESS','RUNNING','LATE') as $s){
            $status = $session->get('status');
            if($request->get($s) == 'true')
            {
                $status[$s] = 1;
            } 
            if($request->get($s) == 'false')
            {
                $status[$s] = 0;
            }
            $session->set('status',$status);
        }

        print_r($session->get('status'));
        //file_put_contents("c:/temp.txt", $session->get('status'));
        # Cas particulier
       /*
        foreach ( array('STOPPED','FAILURE','SUCCESS','RUNNING','LATE') as $s ){
        if ($request->query->get($s)) {
            $Status = $session->get( 'Status' );
           // $r = 0;
            if ($request->query->get($s) == 'true')
            {   
                $r = 1;
            }
            elseif ($request->query->get($s) == 'false')
            {
                $r = 0;
            }
            $Status[$s] = $r;
            $session->set( 'Status', $Status );
        } 
        }
        * 
        */
        // print_r($Status);
        
        
        exit();
    }

}
