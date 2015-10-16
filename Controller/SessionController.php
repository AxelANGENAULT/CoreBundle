<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionController extends Controller
{

    public function updateAction()
    {
        $request = Request::createFromGlobals();
        $session = $this->container->get('arii_core.session');
 
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        if ($request->query->get( 'action' )== 'init') {
            $session->ForceInit();
           return $this->render('AriiCoreBundle:Session:result.html.twig',array(), $response );
        }
        
        # Date de reference
        if ($request->query->get( 'ref_date' )) {
            if ($request->query->get( 'ref_date' ) == 'now()') {
                  $Time = localtime(time(),true);
                  $session->setRefDate( sprintf("%04d-%02d-%02d %02d:%02d:%02d", $Time['tm_year']+1900, $Time['tm_mon']+1, $Time['tm_mday'], $Time['tm_hour'], $Time['tm_min'], $Time['tm_sec']) );       
            }
            else {
                $session->setRefDate( $request->query->get( 'ref_date' ) );                
            }
        }
        if ($request->query->get( 'ref_past' )) 
            $session->setRefPast( $request->query->get( 'ref_past' ) );

        if ($request->query->get( 'ref_future' )) 
            $session->setRefFuture( $request->query->get( 'ref_future' ) );

        if ($request->query->get( 'refresh' )) {
            $session->setRefresh( $request->query->get( 'refresh' ) );
        }
        
        if ($request->query->get( 'filter' )) {
            $session->setUserFilterById( $request->query->get( 'filter' ) );
        }
        
        if ($request->query->get( 'database' )) {
            $db = substr($request->query->get( 'database' ),2);
            $Databases = $session->getDatabases();
            print "<pre>";
            print_r($Databases);
            if (isset($Databases[$db])) {
                $session->setDatabase($Databases[$db]);
                print "NOUVEAU !!!!". $Databases[$db]['name'];
                print_r($session->getDatabase());
                
                exit();
            }
            else {
                print "<font color='red'>$db ?!</font>";
                exit();
            }
            /* ancien systeme
            if ($db != 'recalc') {
                $session->setDatabaseById($db);
            }
            else {
                $session->setDatabases();
            }
             */
        }
        
        if ($request->query->get( 'spooler' )) {
             $session->setSpoolerById($request->query->get( 'spooler' ));
        }
        
        if ($request->query->get( 'site' )) {
            $session->setSiteById($request->query->get( 'site' ));
        }

        if ($request->query->get( 'current_dir' )) {
            $session->set('current_dir',$request->query->get( 'current_dir' ));
        }

        if ($request->query->get( 'layout_sidebar' )) {
            $session->set( 'layout_sidebar', $request->query->get( 'layout_sidebar' ) );
        }
        if ($request->query->get( 'layout_header' )) {
            $session->set( 'layout_header', $request->query->get( 'layout_header' ) );
        }

        if ($request->query->get( 'state' )) {
            list($page, $id, $state) = explode('::',$request->query->get( 'state' )); 
            $State = $session->get( 'state' );
            $State[$page][$id] = $state;
            $session->set( 'state', $State );
        }
        exit();
        return $this->render('AriiCoreBundle:Session:result.html.twig',array(), $response );
    }
    
     public function viewAction()
    {
         $session = $this->container->get('arii_core.session');
         print_r($session->get('database'));
        return $this->render('AriiCoreBundle:Session:view.html.twig' );
    }
   
}
