<?php
namespace Arii\CoreBundle\Service;
use Symfony\Component\HttpFoundation\RequestStack;

class AriiDoc
{
    protected $requestStack;
    protected $java;
    protected $ditaa;
    protected $graphviz;
    
    public function __construct (RequestStack $requestStack, $java, $ditaa, $graphviz ) {    
        $this->requestStack = $requestStack;
        $this->java = $java;
        $this->ditaa = $ditaa;
        $this->graphviz = $graphviz;
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

    public function Parsedown($doc,$filename='') {
        $Parsedown = new \Parsedown();
        $parsedown = $Parsedown->text($doc);
        // Traitement des tables
        $parsedown = str_replace('<table>','<table class="table table-striped table-bordered table-hover">',$parsedown);
        $path = dirname($filename);
        
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
        $balise = 'ditaa';
        $len = strlen($balise)+1;
        while (($p = strpos($parsedown,"<$balise"))>0) {            
            // fin du tag
            $t = strpos($parsedown,'>',$p+$len);
            $tag = substr($parsedown,$p+1,$t-$p-1);            
            
            # On retrouve les attributs
            $d = strpos($tag,'"');
            $f = strpos($tag,'"',$d+1);
            $name = substr($tag,$d+1,$d-$d-1);
            
            $e = strpos($parsedown,"</$balise>",$t);
            if ($e===false) {
                print "</$balise> !!";
                exit();
            }
            $dita = substr($parsedown,$t+1,$e-$t-1); 
            $parsedown = substr($parsedown,0,$p).$this->Ditaa($dita,$name,$filename).substr($parsedown,$e+$len+2);
        }

        // Traitement du dot:
        $balise = 'dot';
        $len = strlen($balise)+1;
        while (($p = strpos($parsedown,"<$balise"))>0) {            
            // fin du tag
            $t = strpos($parsedown,'>',$p+$len);
            $tag = substr($parsedown,$p+1,$t-$p-1);            
            
            # On retrouve les attributs
            $d = strpos($tag,'"');
            $f = strpos($tag,'"',$d+1);
            $name = substr($tag,$d+1,$d-$d-1);
            
            $e = strpos($parsedown,"</$balise>",$t);
            if ($e===false) {
                print "</$balise> !!";
                exit();
            }
            $dot = substr($parsedown,$t+1,$e-$t-1); 
            $parsedown = substr($parsedown,0,$p).$this->Dot($dot,$name,$filename).substr($parsedown,$e+$len+2);
        }
        
        return $parsedown;
    }
 
    // appel le script java et renvoie le contenu du png
    public function Ditaa($text,$name,$filename='') {

        // Document en cours
        $Info = stat($filename);
        $mtime_doc = $Info[9];
        
        // Nom de l'image
        $output = dirname($filename)."/".$name.'.png';    
        // L'image existe ? Si oui, est elle plus récente que le document ?
        $update = true;
        if (is_file($output)) {
            $Info = stat($output);
            $mtime_img = $Info[9];            
            if ($mtime_img>=$mtime_doc) {
                $update = false;
            }
        }
        
        // On refait le cache
        $out = '';
        if ($update) {            
            // nettoyage
            $text = str_replace(array("<p>","</p>"),array("","\n"),$text);

            $input = sys_get_temp_dir().'/'.str_replace(array(' ','.'),'',microtime());
            file_put_contents("$input.ditaa", $text );
            $cmd = '"'.$this->java.'/bin/java" -Dfile.encoding=UTF-8 -jar ../vendor/'.$this->ditaa." \"$input.ditaa\" \"$output\"";
            exec("$cmd 2>&1", $screen, $result);
            $out .= '<p class="bg-info"><ol>';
            foreach ($screen as $s) {
               $out .= "<li>$s</li>";
            }
            $out .= "</ol></p>";
            if ($result>0) {
                return "<pre>$text</pre>";
            }
        }
        $img = file_get_contents($output);
        return '<img class="img-thumbnail" src="data:image/png;base64,'.base64_encode($img).'"/>'.$out;
    }
    
    // appel de graphviz
    public function Dot($text,$name,$filename='') {

        // Document en cours
        $Info = stat($filename);
        $mtime_doc = $Info[9];
        
        // Nom de l'image
        $output = dirname($filename)."/".$name.'.png';    
        // L'image existe ? Si oui, est elle plus récente que le document ?
        $update = true;
        if (is_file($output)) {
            $Info = stat($output);
            $mtime_img = $Info[9];            
            if ($mtime_img>=$mtime_doc) {
                $update = false;
            }
        }
        
        // On refait le cache
        if ($update) {            
        
        // Format 
        $format = "
    fontname=arial
    fontsize=10
    splines=polyline
    randkir=TB
    node [shape=plaintext,fontname=arial,fontsize=10]
    edge [shape=plaintext,fontname=arial,fontsize=8,decorate=true,compound=true]
    bgcolor=transparent";

            // Nettoyage
            $text = str_replace(array("<p>","</p>",'&gt;'),array('','','>'),$text);
            $text = "digraph arii {\n$format\n$text\n}\n";

            // Appel
            $input = sys_get_temp_dir().'/'.str_replace(array(' ','.'),'',microtime());
            file_put_contents("$input.dot", $text );
            $cmd = '"'.$this->graphviz.'" "'.$input.'.dot" -Tpng > "'.$output.'"';
            exec("$cmd 2>&1", $screen, $result);
            if ($result>0)
                return "<pre>$text<font color='red'>".var_dump($screen)."</font></pre>";
        }
        $img = file_get_contents($output);
        return '<img class="img-responsive" src="data:image/png;base64,'.base64_encode($img).'"/>';
    }
    
}
