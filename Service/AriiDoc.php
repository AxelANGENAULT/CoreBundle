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

    public function Parsedown($doc) {
        $Parsedown = new \Parsedown();
        $parsedown = $Parsedown->text($doc);
        $parsedown = str_replace('<table>','<table class="table table-striped table-bordered table-hover">',$parsedown);
        return $parsedown;
    }
 
}
