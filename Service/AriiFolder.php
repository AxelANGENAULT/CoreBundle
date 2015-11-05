<?php
namespace Arii\CoreBundle\Service;
use Symfony\Component\Translation\Translator;

class AriiFolder {
   
    protected $session; 
    
    public function __construct(AriiSession $session) {
        $this->session = $session;
    }
    
    public function Tree($basedir,$dir,$Files=array(),$Ext=array() ) {
        if ($dh = @opendir($basedir.'/'.$dir)) {
            while (($file = readdir($dh)) !== false) {
                if (($file != '.') and ($file != '..')) {
                    $sub = $basedir.'/'.$dir.'/'.$file;  
                    if (is_dir($sub)) {
                        $Files = array_merge($Files,$this->Tree($basedir,"$dir/$file",$Files,$Ext));
                    }
                    else {
                        if (count($Ext)>0) {
                            $ok = 0;
                            $ext = substr($file,-3);
                            foreach ($Ext as $e) {
                                if ($ext == $e) {
                                    $ok = 1;
                                } 
                            }
                            if ($ok) {
                                array_push($Files, $sub );
                            }
                        }
                        else {
                            array_push($Files, $sub ); 
                        }
                    }
                }
            }
            closedir($dh);
        }
        else {
            array_push($Files, "!!! $basedir/$dir!");           
        }
        return $Files;
    }

    public function TreeXML($basedir,$dir ) {
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
            
            sort($Dir);
            foreach ($Dir as $file) {
                $xml .= '<item id="'."$dir/$file/".'" text="'.$file.'" im0="folder.gif">';
                $xml .= $this->TreeXML($basedir,"$dir/$file");
                $xml .= '</item>';
            }
            
            sort($Files);
            foreach ($Files as $file) {
                // on ne s'intéresse qu'aux objets principaux
                $P = explode('.',$file);
                if (array_pop($P)=='xml') {
                    $obj = array_pop($P);
                    if (in_array($obj,array('job','job_chain','order','lock','process_class'))) {
                        $f = implode('.',$P);
                        $xml .= '<item id="'.utf8_encode("$dir/$file").'" text="'.utf8_encode($f).'" im0="'.$obj.'.xml.png"/>';
                    }
                }
            }
        }
        else {
            exit();
        }
        return $xml;
    }
    
}
?>