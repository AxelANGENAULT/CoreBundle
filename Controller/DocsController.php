<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DocsController extends Controller
{    
    public function indexAction()
    {        
        return $this->render('AriiCoreBundle:Docs:index.html.twig');            
    }
    
    public function ribbonAction()
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        
        return $this->render('AriiCoreBundle:Docs:ribbon.json.twig',array(), $response );
    }

    public function treeAction($bundle='Core')
    {        
        $request = Request::createFromGlobals();
        if ($request->query->get( 'route' )) {
            $route = $request->query->get( 'route' );
            $p = strpos($route,'_',5);
            $bundle = substr($route,5,$p-5);
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $xml = "<?xml version='1.0' encoding='utf-8'?>";                
        $xml .= '<tree id="0" text="root">';
        $xml .= $this->TreeXML($bundle,$this->getRequest()->getLocale(),'');
        $xml .= '</tree>';        
        $response->setContent($xml);
        return $response;
    }

    public function TreeXML($bundle,$lang,$dir ) {
        $basedir = '../src/Arii/'.$bundle.'Bundle/Resources/doc/'.$lang;
        $xml ='';
        if ($dh = @opendir($basedir.'/'.$dir)) {
            $Dir = array();
            $Files = array();
            while (($file = readdir($dh)) !== false) {
                $sub = $basedir.'/'.$dir.'/'.$file;
                if (($file != '.') and ($file != '..')) {
                    if (is_dir($sub)) {
                        array_push($Dir, $file );
                    }
                    else {
                        array_push($Files, $file );                
                    }
                }
            }
            closedir($dh);
            
            sort($Files);
            foreach ($Files as $file) {
                // on ne s'intéresse qu'aux md
                if ((substr($file,-3)=='.md') or (substr($file,-4)=='.rst')) {
                    $f = substr($file,0,strlen($file)-4);
                    $xml .= '<item id="'.$this->DocTitle($bundle.'/'.$lang.$dir.'/'.$file).'" text="'.$this->DocTitle($f).'" im0="page.png"/>';
                }
            }

            sort($Dir);
            foreach ($Dir as $file) {
                $xml .= '<item id="'.$this->DocTitle("$bundle/$lang/$dir$file").'" text="'.$this->DocTitle($file).'" im0="folder.gif">';
                $xml .= $this->TreeXML($bundle,$lang,$dir.'/'.$file);
                $xml .= '</item>';
            }
            
        }
        else {
            exit();
        }
        return $xml;
    }

    private function DocTitle($doc) {        
        if (preg_match('/^\d\d - /',$doc,$matches))
                $doc = substr($doc,5);
        $this->charset = $this->container->getParameter('charset');
        if ($this->charset != 'UTF-8')
            return utf8_encode($doc);
        return $doc;
        // Devient inutile car tout est en utf-8, même sur windows
    }
    
    public function doc2Action()
    {
        $request = Request::createFromGlobals();
        $this->charset = $this->container->getParameter('charset');
        $doc = $this->Decodage($request->query->get( 'doc' ));
        $p = strpos($doc,'.');
        $type = substr($doc,$p+1);
        $response = new Response();
        $content = file_get_contents($doc);
        switch($type) {
            case 'pdf':
                $response->headers->set('Content-Type', 'application/pdf');
                break;
            case 'rtf':
                $response->headers->set('Content-Type', 'application/msword');
                break;
            case 'xls':
                $response->headers->set('Content-Type', 'application/xls');
                break;
            case 'html':
                $response->headers->set('Content-Type', 'text/html');
                break;
            case 'xml':
                $content = "<pre>".str_replace('<','&lt;',$content)."</pre>";
                break;
            default:
               $content = $doc;
        }    
        $length = strlen($content);        
        $response->headers->set('Content-Length',$length);
        $response->headers->set('Content-Disposition', 'inline; filename="'.$doc.'"');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Expires', 0);
        $response->headers->set('Cache-Control', 'must-revalidate');
        $response->headers->set('Pragma', 'public');
        $response->setContent($content);
        return $response;
    }
    
    protected function Decodage($text) {
        $this->charset = $this->container->getParameter('charset');
        if ($this->charset != 'UTF-8')
            return utf8_decode($text);
        return $text;
    }

    public function viewAction()
    {
        $request = Request::createFromGlobals();
        $doc = $request->query->get( 'doc' );

        $p = strpos($doc,'/');
        
        $page = '../src/Arii/'.substr($doc,0,$p).'Bundle/Resources/doc/'.substr($doc,$p);
        $file = $this->Decodage($page);
        
        // Est ce un répertoire ?
        if (is_dir($file)) {
            $doc = "$doc.rst";
            $content = '';
//            $content= "$doc\n";
//            $content.= "===========\n";
            $dir = $file;
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    if (substr($file,0,1)!='.' )
                        $content .= "- $file\n";
                }
                closedir($dh);
            }
        }
        elseif (!($content = @file_get_contents($file))) {
            $error = array( 'text' =>  'File not found: '.$doc );
            return $this->render('AriiCoreBundle:Templates:ERROR.html.twig', array('error' => $error));
        }
        
        if ((substr($doc,-3)=='.md') or (substr($doc,-4)=='.rst')) {
            $doc = $this->container->get('arii_core.doc');
            $value =  array('content' => $doc->Parsedown($content,$file));
            return $this->render('AriiCoreBundle:Templates:bootstrap.html.twig', array('doc' => $value));
        }
        else {
            $yaml = new Parser();
            try {
                $value = $yaml->parse($content);
            } catch (ParseException $e) {
                $error = array( 'text' =>  "Unable to parse the YAML string: %s<br/>".$e->getMessage() );
                return $this->render('AriiCoreBundle:Templates:ERROR.html.twig', array('error' => $error));
            }                        
        }
        exit();
    }
    
}
