<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\Translator;

class DefaultController extends Controller
{
    public function homepageAction() {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('arii_Home_index'));
        }
        else  {  
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }
    }
    
    public function indexAction()
    {        
        return $this->render('AriiCoreBundle:Default:index.html.twig');            
    }
    
    public function ribbonAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        return $this->render('AriiCoreBundle:Default:ribbon.json.twig',array(), $response );
    }

   public function toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiCoreBundle:Default:toolbar.xml.twig",array(), $response );
    }

    public function modulesAction()
    {   
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $list = '<?xml version="1.0" encoding="UTF-8"?>';
        $list .= "<data>\n";
        foreach ($this->getModules() as $k=>$v) {
            $list .= "  <item id=\"$k\">\n";
            foreach(array('BUNDLE','role','summary','name','desc','img', 'url') as $t) {
                $list .= "      <$t>".$v[$t]."</$t>\n";
            }
            $list .= "  </item>\n";
        }
        $list .= "</data>\n";

        $response->setContent($list);
        return $response;
    }
        
    private function getModules() {
        $sc = $this->get('security.context');
        $Params = array();
        $Result = array();
        # Les modules pour tout le monde
        $session = $this->container->get('arii_core.session');
        $param = $session->getModules(); 
        if ($param != '')
            foreach (explode(',',$param) as $p)
                array_push($Params, $p);
                
        # On retrouve l'url active 
        foreach ($Params as $p) {
            // Modules limites à un droit ?
            if (($d = strpos($p,'('))>0) {
                $module = substr($p,0,$d);
                $f = strpos($p,')',$d+1);
                $role = substr($p,$d+1,$f-$d-1);
                $p = '';
                if (($sc->isGranted('IS_AUTHENTICATED_FULLY')) 
              or ($sc->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
                    if ($sc->isGranted($role))
                        $p = $module;
                }
                else {
                    if ($role == 'ANONYMOUS')
                        $p = $module;
                }
            }
            else {
                $role = '';
            }
            if ($p!='') 
                $Result[$p] = array(
                    'BUNDLE'=>$p,
                    'role' => $this->get('translator')->trans($role), 
                    'mod' => strtolower($p), 
                    'name' => $this->get('translator')->trans('module.'.$p), 
                    'desc' => $this->get('translator')->trans('text.'.$p), 
                    'summary' => $this->get('translator')->trans('summary.'.$p), 
                    'img' => "$p.png",
                    'url' => $this->generateUrl('arii_'.$p.'_index') );
        } 
        return $Result;
    }
    
    public function cover_toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiCoreBundle:Default:cover_toolbar.xml.twig",array(), $response );
    }

    public function menuAction($route='arii_homepage')
    {
        $here = $url = $this->generateUrl($route);
        
        $session = $this->container->get('arii_core.session');
        $liste = array();
        
        # Les utilisateur non authentifiés sont dans public
        # Les autres dans home
        $sc = $this->get('security.context');
/*
        if (($sc->isGranted('IS_AUTHENTICATED_FULLY')) 
              or ($sc->isGranted('IS_AUTHENTICATED_REMEMBERED')))
            $Params = array('Home');        
        else
            $Params = array('Public');        
*/        
        $Params = array();
        # Les modules pour tout le monde

        $param = $session->getModules(); 
        if ($param != '')
            foreach (explode(',',$param) as $p)
                array_push($Params, $p);
                
        # On moins un home
        array_push($liste, array(
                'mod' => 'navigation', 
                'module' => 'Core', 
                'url' => $this->generateUrl('arii_Home_index'), 
                'class' => '', 
                'title' => 'Navigation' ) );
 
        # On retrouve l'url active 
        foreach ($Params as $p) {
            // Modules limites à un droit ?
            if (($d = strpos($p,'('))>0) {
                $module = substr($p,0,$d);
                $f = strpos($p,')',$d+1);
                $role = substr($p,$d+1,$f-$d-1);
                $p = '';
                if (($sc->isGranted('IS_AUTHENTICATED_FULLY')) 
              or ($sc->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
                    if ($sc->isGranted($role))
                        $p = $module;
                }
                else {
                    if ($role == 'ANONYMOUS')
                        $p = $module;
                }
            }
            if ($p == '') continue;
            $class='';
            $url = $this->generateUrl('arii_'.$p.'_index');
            $len = strlen($url);
            if (substr($here,0,$len)==$url) $class='selected';
            
            array_push($liste, array( 'mod' => strtolower($p), 'module' => $p, 'url' => $url, 'class' => $class, 'title' => 'module.'.$p ) );
        }   

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');   
        return $this->render('AriiCoreBundle:Default:menu.xml.twig',array('MENU' => $liste), $response );
    }

    public function aboutAction()
    {
        return $this->render('AriiCoreBundle:Default:about.html.twig');
    }

    private function Modules($route='arii_homepage') {
        $here = $url = $this->generateUrl($route);
        $session = $this->container->get('arii_core.session');
        $liste = array();

        # Les utilisateur non authentifiés sont dans public
        # Les autres dans home
        $sc = $this->get('security.context');
        if (($sc->isGranted('IS_AUTHENTICATED_FULLY')) 
              or ($sc->isGranted('IS_AUTHENTICATED_REMEMBERED')))
            $Params = array('Home');        
        else
            $Params = array('Public');        
        
        # Les modules pour tout le monde
        $param = $session->getModules(); 
        if ($param != '')
            foreach (explode(',',$param) as $p)
                array_push($Params, $p);
        
        # On retrouve l'url active 
        foreach ($Params as $p) {
            // Modules limites à un droit ?
            if (($d = strpos($p,'('))>0) {
                $module = substr($p,0,$d);
                $f = strpos($p,')',$d+1);
                $role = substr($p,$d+1,$f-$d-1);
                $p = '';
                if (($sc->isGranted('IS_AUTHENTICATED_FULLY')) 
              or ($sc->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
                    if ($sc->isGranted($role))
                        $p = $module;
                }
                else {
                    if ($role == 'ANONYMOUS')
                        $p = $module;
                }
            }
            if ($p == '') continue;
            $class='';
            $url = $this->generateUrl('arii_'.$p.'_index');
            $len = strlen($url);
            // print "((".substr($here,0,$len)."-".$url."))";
            if (substr($here,0,$len)==$url) $class='selected';
            
            array_push($liste, array( 'module' => $p, 'class' => $class, 'title' => 'module.'.$p ) );
        }   
        return $liste;
    }
    
    public function dashboardAction($route)
    {
        return $this->render('AriiCoreBundle:Default:dashboard.html.twig', array(
          'menu' => $this->Modules($route)
        ));
    }

    public function langAction($lang = null)
    {
        $request = $this->container->get('request');
        $routeName = $request->attributes->get('_route');
        print  $routeName;
        exit();
        $Lang['en'] = $this->generateUrl($routeName, 'en');
        $Lang['fr'] = $this->generateUrl($routeName, 'fr');
        
        return $this->render('AriiCoreBundle:Default:lang.html.twig', array(
          'lang' => $Lang
        ));
    }

    public function calendarAction() 
    {
        $session = $this->container->get('arii_core.session');
        $request = Request::createFromGlobals();
        
        // Date courante
        $info = localtime(time(), true);
        $dc = $info['tm_mday'];        
        $datec = sprintf("%04d-%02d",$info['tm_year']+1900,$info['tm_mon']+1);
        $heurec = sprintf("%02d:%02d:%02d",$info['tm_hour'],$info['tm_min'],$info['tm_sec']);
        
        $time = $request->query->get( 'ref_date' );
        if ($time == "") {
            $time = $session->get('ref_date' );
        }

        // Date reference Get ou Session ou Date actuelle
        if ($time=="") {    
            $time = time();
            $info = localtime($time, true);
            $heure = sprintf("%02d:%02d:%02d",$info['tm_hour'],$info['tm_min'],$info['tm_sec']);
            $y = $info['tm_year']+1900;
            $m = $info['tm_mon']+1;
            $d = $info['tm_mday'];
        }
        else {    
            $y = substr($time,0,4);
            $m = substr($time,5,2);
            $d = substr($time,8,2);
            $h = substr($time,11,2);
            $mi = substr($time,14,2);
            $s = substr($time,17,2);
            $heure = substr($time,11,8);
        }
        
        $Cal['heure'] = $heurec;
        
        // Precedent
        $mp = $m - 1;
        if ($mp<1) {
            $yp = $y - 1;
        }
        else {
            $yp = $y;
        }
        $Cal['precedent'] = $_SERVER['PHP_SELF'].'?ref_date='.sprintf("%04d-%02d-%02d ",$yp,$mp,$d).$heurec;

        // 1er jour du mois
        $Cal['mois'] = 'str_month.'.($m*1);
        $Cal['annee'] = $y;
        $date = sprintf("%04d-%02d",$y,$m );

        $first = mktime(0,0,0,$m,1,$y);
        // dernier jour du mois
        if ($m==12) {
            $m=1;
            $y++;
        }
        else {
            $m++;
        }
        $last = mktime(0,0,0,$m,1,$y);
        // Jour de la semaine de ce mois
        $info_first = localtime($first, true);
        $jf = $info_first['tm_wday'];
        // on doit avoir 35 jours !
        // on commence la semaine au lundi
        // $jf = ($jf+1) % 7;
        for($i=0;$i<35;$i++) {
            $D[$i] = '<span></span>';
        }
        // Nombre de jours
        $nb = ($last - $first)/86400;
        // Si le jour est superieur, on se recale au mois
        if ($d>$nb) $d=$nb;
        
        for($i=1;$i<=$nb;$i++) {
            $j = $jf+$i-2;
            $D[$j] = '<a href="'.$_SERVER['PHP_SELF'].'?ref_date='.$date.'-'.substr("0".$i,-2).' '.$heurec.'"';
            if (($date==$datec) and ($i==$dc)) 
                $D[$j] .= ' class="today"';
            elseif ($i==$d)
                $D[$j] .= ' class="event"';
            $D[$j] .= '>'.$i.'</a>';
        }
        $Cal['jours'] = $D;
        $Cal['suivant'] = $_SERVER['PHP_SELF'].'?ref_date='.sprintf("%04d-%02d-%02d ",$y,$m,$d).$heurec;
        
        $ref_date = $date.'-'.substr("0".$d,-2).' '.$heurec;
        $session->set( 'ref_date', $ref_date );
        
        // Passe et futur
        $Cal['ref_past'] = $session->get('ref_past' );
        if ($Cal['ref_past']=="") 
            $Cal['ref_past'] = 4;
        $Cal['ref_future'] = $session->get('ref_future' );
        if ($Cal['ref_future']=="") 
            $Cal['ref_future'] = 2;
      return $this->render('AriiCoreBundle:Sidebar:calendar.html.twig', $Cal );
    }
    
    public function quickinfoAction()
    {
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');
        
        $qry = 'SELECT "Jobs" as what,count(*) as nb   FROM SCHEDULER_JOBS
 union SELECT "Orders",count(*) as nb   FROM SCHEDULER_ORDERS
 union SELECT "Events",count(*) as nb   FROM SCHEDULER_EVENTS
 union SELECT "History",count(*) as nb   FROM SCHEDULER_HISTORY
 union SELECT "Messages",count(*) as nb   FROM SCHEDULER_MESSAGES
';
        $Infos = array();
        $res = $data->sql->query( $qry );
        while ($line = $data->sql->get_next($res)) {
            $what = $line['what'];
            $nb = $line['nb'];
            $Infos[$what] = $nb;
        }       
        return $this->render('AriiCoreBundle:Default:quickinfo.html.twig', array('Infos' => $Infos) );
    }

    public function todoAction()
    {
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');
        
        $qry = 'SELECT MOD_TIME, SPOOLER_ID, JOB_CHAIN, ID, STATE, STATE_TEXT, TITLE  from scheduler_orders where STATE_TEXT like "PROMPT: %"';
        //$Infos = array();
        $res = $data->sql->query( $qry );
        $Todo = array();
        $nb=0;
        while ($line = $data->sql->get_next($res)) {
            $New['type'] ='prompt';
            $New['title'] =  utf8_encode ( substr($line['STATE_TEXT'],8) );
            $Msg = array();
            if ($line['TITLE'] != '') 
                array_push($Msg,$line['TITLE']);
            array_push($Msg,'['.$line['ID'].' -> '.$line['JOB_CHAIN'].'('.$line['STATE'].')]');
            $New['message'] = implode( '<br/>', $Msg );
            $Actions['ok'] = 'OK!!';
            $Actions['ko'] = 'Cancel';
            $New['actions'] = $Actions;
            array_push($Todo, $New);
            $nb++;
        }       
        if ($nb==0)
            exit();
        return $this->render('AriiCoreBundle:Sidebar:todo.html.twig', array('Todo' => $Todo ) );
    }

    public function favoritesPPAction()
    {
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('dataview');
        $sql = $this->container->get('arii_core.sql');
        
        $qry = $sql->Select(array('ID,BUNDLE')) 
        .$sql->From(array('ARII_FAVORITE'))
        .$sql->Where(array("USER_ID"=>$user_id))
        .$sql->OrderBy(array('LEVEL'));
        
        $data->render_sql($qry,"ID","BUNDLE","USER_ID");
    }

    public function favoritesAction()
    {
        $sc = $this->get('security.context');
        $user_id = $sc->getToken()->getUser()->getId();
        $db = $this->container->get('arii_core.db');
        $data = $db->Connector('dataview');
        $sql = $this->container->get('arii_core.sql');
        
        $qry = $sql->Select(array('ID,BUNDLE,LEVEL')) 
        .$sql->From(array('ARII_FAVORITE'))
        .$sql->Where(array("USER_ID"=>$user_id))
        .$sql->OrderBy(array('LEVEL'));
        
        $data->event->attach("beforeRender",array($this,"addColumn"));
        $data->render_sql($qry,"ID","BUNDLE,LEVEL,name");
    }
    
    public function addColumn($row)
    {
        $p = $row->get_value("BUNDLE");
        $row->set_value("ID",$p);
        $row->set_value("name",$this->get('translator')->trans('module.'.$p));
        $row->set_value("desc",$this->get('translator')->trans('text.'.$p));
        $row->set_value("summary",$this->get('translator')->trans('summary.'.$p));
    }

}
