<?php
namespace Arii\CoreBundle\Service;
use Symfony\Component\HttpFoundation\RequestStack;

class AriiDoc
{
    protected $requestStack;
    
    public function __construct (RequestStack $requestStack) {
        $this->requestStack = $requestStack;
        require_once '../vendor/parsedown/Parsedown.php';
    }
    
    /* Transforme un module en url avec des arguments */
    public function Url($doc) {
        $request = $this->requestStack->getCurrentRequest();
        $lang = $request->getLocale();

        while (($p = strpos($doc,'{'))>0) {
            $e = strpos($doc,'}',$p);
            $sub = substr($doc,$p+1,$e-$p-1);
            if ($request->query->get($sub)) {
                $replace=$request->query->get($sub);
            }
            elseif ($sub == 'locale' ) {
                $replace = $lang;
            }
            else {
                $replace = "[$sub]";
            }
            $doc = substr($doc,0,$p).$replace.substr($doc,$e+1);
        }
        return $doc;
    }

    public function Parsedown($doc,$path='') {
        $Parsedown = new \Parsedown();
        $parsedown = $Parsedown->text($doc);
        // Traitement des tables
        $parsedown = str_replace('<table>','<table class="table table-striped table-bordered table-hover">',$parsedown);
        // Traitement des images
        while (($p = strpos($parsedown,'<img src="'))>0) {         
            $e = strpos($parsedown,'"',$p+10);
            $file = substr($parsedown,$p+10,$e-$p-10);            
            $img = @file_get_contents("$path/$file");
            $ext = substr($file,-3);
            $replace = '<img class="img-thumbnail" src="data:image/'.$ext.';base64,'.base64_encode($img).'"';
            $parsedown = substr($parsedown,0,$p).$replace.substr($parsedown,$e+1);
        }
        
        return $parsedown;
    }
 
}
