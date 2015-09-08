<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Yaml\Parser;

class DocController extends Controller
{
    public function indexAction($doc)
    {
        $file = basename($doc);
        $folder = dirname($doc);
        
        // Cas particulier du readme
        if ($file == 'README.md') {
            $service = 'md';
            $render = 'bootstrap';
            $page = "../src/Arii/$folder"."Bundle/$file";            
        }
        else {
            $FileInfos = split('\.',$file);
            $service = array_pop($FileInfos);
            $render = array_pop($FileInfos);
            $page = "docs/$folder/$file";
        }
        if (!($content = @file_get_contents($page))) {
            $error = array( 'text' =>  'File not found: '.$page );
            return $this->render('AriiDOCBundle:Templates:ERROR.html.twig', array('error' => $error));
        }
        
        switch ($service) {
            case 'yml':
                $yaml = new Parser();
                try {
                    $value = $yaml->parse($content);
                } catch (ParseException $e) {
                    $error = array( 'text' =>  "Unable to parse the YAML string: %s<br/>".$e->getMessage() );
                    return $this->render('AriiDOCBundle:Templates:ERROR.html.twig', array('error' => $error));
                }
                break;
            case 'md':
                $doc = $this->container->get('arii_core.doc');
                $value =  array('content' => $doc->Parsedown($content));
                break;
        }      
        return $this->render('AriiDOCBundle:Templates:'.$render.'.html.twig', array('doc' => $value));
    }
}
