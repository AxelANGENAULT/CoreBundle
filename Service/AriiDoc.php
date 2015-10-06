<?php
namespace Arii\CoreBundle\Service;
use Symfony\Component\HttpFoundation\RequestStack;

class AriiDoc
{
    protected $requestStack;
    protected $java_home;
    protected $ditaa;
    
    public function __construct (RequestStack $requestStack, $java_home, $ditaa ) {    
        $this->requestStack = $requestStack;
        $this->java_home = $java_home;
        $this->ditaa = $ditaa;
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
        
        // Traitement du dita:
        while (($p = strpos($parsedown,'(ditaa:'))>0) {
            $e = strpos($parsedown,':ditaa)',$p+7);
            if ($e===false) {
                $e = strpos($parsedown,')',$p+7);
                $dita = substr($parsedown,$p+7,$e-$p); 
                $parsedown = substr($parsedown,0,$p).$this->Ditaa($dita).substr($parsedown,$e+7);
            }
            else {
                $dita = substr($parsedown,$p+7,$e-$p-7); 
                $parsedown = substr($parsedown,0,$p).$this->Ditaa($dita).substr($parsedown,$e+1);
            }
        }
        
        return $parsedown;
    }
 
    // appel le script java et renvoie le contenu du png
    public function Ditaa($text) {
        // nettoyage
        $text = str_replace(array("<p>","</p>"),'',$text);

        $file = sys_get_temp_dir().'/'.str_replace(array(' ','.'),'',microtime());
        file_put_contents("$file.ditaa", $text );
        $cmd = '"'.$this->java_home.'/bin/java" -jar ../vendor/'.$this->ditaa." \"$file.ditaa\" \"$file.png\"";
        exec("$cmd 2>&1", $output, $result);
        if ($result==0) {
            $img = file_get_contents("$file.png");
            return '<img class="img-responsive" src="data:image/png;base64,'.base64_encode($img).'"';
        }
        return "<pre>$text</pre>";
    }
}
