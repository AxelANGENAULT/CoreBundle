<?php
namespace Arii\CoreBundle\Service;
use Symfony\Component\Translation\Translator;

class AriiFiles {
    
    protected $session; 
    protected $workspace;
    
    public function __construct(AriiSession $session, $workspace) {
        $this->session = $session;
        $this->workspace = $workspace;
    }
    
    public function DirList($path)
    {
        $dir = $this->workspace.'/'.$path.'/'; 
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= "<rows>\n";
        $xml .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>
        </head>';
        if (is_dir($dir)) {
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    // if ((substr($file, -8)!='.job.xml') and ($file != 'error.log')) continue;
                    if (is_dir($dir . $file)) continue; 
                    $Infos = stat($dir . $file);
                    $Time = localtime($Infos[9],true);
                    $size = $Infos[7];
                    if ($file == 'error.log') {
                        if ($size>0) { 
                            $xml .= '<row id="'.$file.'" style="background-color: #fbb4ae;"><cell>'.$file.'</cell><cell>'.sprintf("%04d-%02d-%02d %02d:%02d:%02d",$Time['tm_year']+1900,$Time['tm_mon']+1,$Time['tm_mday'],$Time['tm_hour'],$Time['tm_min'],$Time['tm_sec']).'</cell><cell>'.$size.'</cell></row>';
                        }
                        else {
                            // rien ou du vert ?
                        }
                    }
                    else {
                        $xml .= '<row id="'.$file.'"><cell>'.$file.'</cell><cell>'.sprintf("%04d-%02d-%02d %02d:%02d:%02d",$Time['tm_year']+1900,$Time['tm_mon']+1,$Time['tm_mday'],$Time['tm_hour'],$Time['tm_min'],$Time['tm_sec']).'</cell><cell>'.$size.'</cell></row>';
                    }                   
                }
                closedir($dh);            
            }
        }
        $xml .= '</rows>';
        return $xml;
    }
    
    public function XMLView($file) {
        $workspace = $this->workspace;
        return '<pre>'.str_replace(array('<','>'),array('&lt;','&gt;'),file_get_contents("$workspace/$file")).'</pre>'; 
    }
    
    public function Upload($path, $name='') {
        $dir =  $this->workspace.'/'.$path;
        if (!is_dir($dir)) @mkdir($dir);
        if (isset($_FILES['error'])) {
            file_put_contents("$dir/error.log",$_FILES['error']);
            exit();
        }
        if (@$_REQUEST["mode"] == "html5" || @$_REQUEST["mode"] == "flash") {
                $filename = $_FILES["file"]["name"];
                if ($name == '')
                    $name = $filename;
                $file = "$dir/$name";
                @unlink($file);
                move_uploaded_file($_FILES["file"]["tmp_name"], $file );
                return "{state: true, name:'".str_replace("'","\\'",$name)."'}";
        }
        if (@$_REQUEST["mode"] == "html4") {
                if (@$_REQUEST["action"] == "cancel") {
                        return "{state:'cancelled'}";
                } else {
                        $filename = $_FILES["file"]["name"];
                        if ($name == '')
                            $name = $filename;
                        $file = "$file/$name";
                        @unlink($file);
                        move_uploaded_file($_FILES["file"]["tmp_name"], $file );
                                               
                        return "{state: true, name:'".str_replace("'","\\'",$name)."', size:".$_FILES["file"]["size"]/*filesize("uploaded/".$filename)*/."}";
                }
        }
        return "{state:'cancelled'}";
    }
}
