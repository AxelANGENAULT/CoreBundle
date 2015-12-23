<?php
// src/Sdz/BlogBundle/Service/AriiSQL.php
 
namespace Arii\CoreBundle\Service;

class AriiCron
{
    
    public function __construct(AriiSession $session)
    {
    }

    // Recupere les informations du fichier cron et transforme en tableau    
    public function Tab2Array($cron) {
        /* Mode debug */
/*        
         $cron = "
12 23 * * * test1 out > out
12 23 * * * test2 out appended >> out appended
12 23 * * * test3 out and err > out 2> err
12 23 * * * test3 err on out > out err 2>&1
12 23 * * * test3 err on out appended >> out err appended 2>&1
";
 */
        $Infos = array();
        $Comments = array();
        foreach (explode("\n",$cron) as $command) {
            $command = trim($command);
            if ($command =='') {
                $Comments = array();
                continue;
            }
            
            if (substr($command,0,1)=='#') {
                array_push($Comments, trim(substr($command,1)));
                continue;
            }
            $Cron = explode(" ",$command);
            $mm = array_shift($Cron);
            $hh = array_shift($Cron);
            $jj = array_shift($Cron);
            $MMM = array_shift($Cron);
            $JJJ = array_shift($Cron);
            $task = implode(" ",$Cron);
            
            // Découpage par '>'
            $Files = explode('>',$task);
            
            // la premiere tranche est la tache
            $task = trim(array_shift($Files));
            
            // append ?
            $append = false;
            if (isset($Files[0]) and ($Files[0]=='')) {
                $append = true;
                array_shift($Files);
            }
            
            // err sur out
            $last = array_pop($Files);
            if ($last == '&1') {
                $outerr = true;
            }
            else {
                $outerr = false;
                array_push($Files,$last);
            }
            
            $stdout = '';
            if (isset($Files[0])) {
                $next = array_shift($Files);
                if (substr($next,-2)==' 2') {
                    $stdout = substr($next,0,strlen($next)-2);
                    $stderr = array_shift($Files);
                }
                else {
                    $stdout = $next;
                    $stderr = '';
                }
                if (isset($Files[0])) {
                    $stderr = $Files[0];
                }
            }
            else {
                $stderr = '';
            }

            // mode err on out
            if ($outerr) {
                $stderr = $stdout;
            }
            
            $info = array( 
                "min"  => $mm,
                "hour" => $hh,
                "wday" => $jj,
                "mon"  => $MMM,
                "mday" => $JJJ,
                "task" => $task,
                "comment" => implode("\n",$Comments),
                "stdout" => $stdout,
                "stderr" => $stderr,
                "append" => $append
            );
            array_push($Infos,$info);
            $Comments = array();            
        }                
        return $Infos;
    }
     
}
