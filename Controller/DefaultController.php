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

    public function defaultAction()
    {   
        // est-ce que la langue est en session
        $locale = $this->get('request')->getLocale();       
        return $this->redirect($this->generateUrl('arii_home'));
    }

    // On passe les modules dans l'index pour construire la page
    public function indexAction()
    {   
        $portal = $this->container->get('arii_core.portal');
        return $this->render('AriiCoreBundle:Default:index.html.twig',array('Modules'=> $portal->getModules()));
    }
    
    public function readmeAction()
    {
        return $this->render('AriiCoreBundle:Default:readme.html.twig');
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
        $portal = $this->container->get('arii_core.portal');
        foreach ($portal->getModules() as $k=>$v) {
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
    
    public function cover_toolbarAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        return $this->render("AriiCoreBundle:Default:cover_toolbar.xml.twig",array(), $response );
    }

    public function menuAction($route='arii_Home_readme')
    {
        $here = $url = $this->generateUrl($route);

        $request = $this->container->get('request');
        if ($request->get('route')!='') 
            $route = $request->get('route');
                
        $locale =  $this->get('request')->getLocale();
        foreach (array('en','fr') as $l ) {
            if ($l == $locale) continue;            
            $lang[$l]['string'] = $this->get('translator')->trans("lang.$l");     
            $lang[$l]['url'] = $this->generateUrl($route,array('_locale' => $l)); 
        }

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
 
        # Par defaut
        $current = array( 
            'mod' => 'Core',
            'url'    => $this->generateUrl('arii_Core_readme'),
            'img'  => 'navigation'
            );
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
            
            $test = 'arii_'.$p;
            if (substr($route,0,strlen($test))==$test) {
                $class='selected';
                $current = array( 
                    'mod' => $p,
                    'url'    => $this->generateUrl('arii_'.$p.'_readme'),
                    'img'  => strtolower($p)
                    );
            }
            
            array_push($liste, array( 'mod' => strtolower($p), 'module' => $p, 'url' => $url, 'class' => $class, 'title' => 'module.'.$p ) );
        }   

        // Timezone
        $tz = array();
        foreach (timezone_identifiers_list() as $c) {
            $p = strpos($c,'/');
            $continent = substr($c,0,$p);
            $country = substr($c,$p+1);
            if ($continent != '') 
                $tz[$continent][$country]=str_replace('_',' ',$country);            
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');   
        return $this->render('AriiCoreBundle:Default:menu.xml.twig',array('MENU' => $liste, 'LANG' => $lang, 'BUNDLE' => $current, 'TZ' => $tz ), $response );
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

    public function docAction() {
        $request = Request::createFromGlobals();
        $lang = $this->getRequest()->getLocale();

        $doc = $request->get('doc');
        if ($doc == 'README')
            $file = "../src/Arii/ATSBundle/README.md";
        else 
            $file = "../src/Arii/ATSBundle/Docs/$lang/$doc.md";

        $content = @file_get_contents($file);
        if ($content == '') {
            print "$doc ?!";
            exit();
        }
        $doc = $this->container->get('arii_core.doc');
        $parsedown =  $doc->Parsedown($content,$file);

        return $this->render('AriiCoreBundle:Default:bootstrap.html.twig',array( 'content' => $parsedown ) );
    }

    public function readme_htmlAction($route="arii_Core_index") 
    {
        $request = $this->container->get('request');
        if ($request->get('route')!='') 
            $route = $request->get('route');
        // Historique...
        if (substr($route,0,10)=='arii_Home_') {
            $bundle = 'Core';
        }
        else {
            $p = strpos($route,'_',5);
            $bundle = substr($route,5,$p-5);
        }
        
        $content = @file_get_contents('../src/Arii/'.$bundle.'Bundle/README.md');
        $doc = $this->container->get('arii_core.doc');
        $value =  array('content' => $doc->Parsedown($content));
        
        return $this->render('AriiCoreBundle:Templates:bootstrap.html.twig', array('doc' => $value));
    }


}
