<?php
namespace Arii\CoreBundle\Service;

class AriiExec {
    
    protected $session;
    protected $db;
    protected $sql;
    protected $audit;
    protected $log;
    protected $workspace;
    protected $status; # Status du noeud
    
    public function __construct(
            \Arii\CoreBundle\Service\AriiSession $session,
            $workspace,
            \Arii\CoreBundle\Service\AriiDB $db,
            \Arii\CoreBundle\Service\AriiSQL $sql,
            \Arii\CoreBundle\Service\AriiAudit $audit,  
            \Arii\CoreBundle\Service\AriiLog $log
    ) {
        $this->workspace = $workspace;
        $this->session = $session;
        $this->db = $db;
        $this->sql = $sql;
        $this->audit = $audit;
        $this->log = $log;

        set_include_path('../vendor/phpseclib' . PATH_SEPARATOR . get_include_path());
        include('Net/SSH2.php');
        define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
        include('Crypt/RSA.php');
        
    }
    
    public function ExecNodeId($id,$command,$stdin='') {
                
        // Tableau des connections
        $sql = $this->sql;        
        $qry = $sql->Select(array('N.ID','C.INTERFACE as host','C.LOGIN as user','C.PASSWORD as password','C.KEY as key'))
                .$sql->From(array('ARII_NODE N'))
                .$sql->LeftJoin('ARII_CONNECTION C',array('SHELL_ID','C.ID'))
                .$sql->Where(array('N.ID' => $id));
        
        $data = $this->db->Connector("data");                        
        $res = $data->sql->query( $qry );
        $shell = $data->sql->get_next($res);
        if (!$shell) {
            print "$id ?!";
            exit();
        }               
        
        if (($shell['password']=='') and ($shell['key']!='')) {
            $keyfile = $this->workspace."/Site/Keys/".$shell['key'];
            $shell['key'] = file_get_contents($keyfile);
        }
        
        $this->status = '!UNKNOWN';
        $result = $this->Exec($shell,$command,$stdin=''); 
        
        // On en profite pour mettre à jour le heartbeat
        $data->sql->query( "update ARII_NODE set HEARTBEAT=".time().",STATUS=\"".$this->status."\" where ID=$id" );
             
        return $result;
    }

    public function getNodes() {
        $db = $this->db;
        $data = $db->Connector("data");

        # Tableau des connections
        $sql = $this->sql;
        $qry =  $sql->Select(array("N.ID","C.NAME as CATEGORY","N.NAME","N.TITLE","N.DESCRIPTION","N.HEARTBEAT","N.STATUS")) 
                .$sql->From(array("ARII_NODE N"))
                .$sql->LeftJoin("ARII_CATEGORY C",array("N.CATEGORY_ID","C.ID"))
                .$sql->OrderBy(array("NAME","CATEGORY"));
        
        $res = $data->sql->query( $qry );
        $Infos = array();
        while ($line = $data->sql->get_next($res)) {
            $heartbeat = time() - $line['HEARTBEAT'];
            if ($line['STATUS'] == 'RUNNING') {
                $color = '#ccebc5';
            }
            elseif (substr($line['STATUS'],0,1)=='!') {
                $color = 'red';
            }
            else {
                $color = 'orange';
            }
            $line['COLOR'] = $color;                       
            array_push($Infos,$line);
        }        
        return $Infos;
    }
    
    public function Exec($shell,$command,$stdin='') {
        
        $host = $shell['host'];
        $user = $shell['user'];

        $ssh = new \Net_SSH2($host);
        if (isset($shell['key'])) {
            $key = new \Crypt_RSA();
            $ret = $key->loadKey($shell['key']);
            if (!$ret) {
                $this->status = '!KEY';
                echo "loadKey failed\n";
                print "<pre>".$ssh->getLog().'</pre>';
                return;
            }
        }
        elseif (isset($shell['password'])) {
            $key = $shell['password'];
        }
        else {
            $key = ''; // ?! possible ?
        }

        if (!@$ssh->login($shell['user'], $key)) {
            $this->status = '!LOGIN';
            print 'Login Failed: '.$shell['user'];
            print "<pre>".$ssh->getLog().'</pre>';
            return;
        }

        $this->status = 'RUNNING';
        if ($stdin=='')
            return $ssh->exec("$command");
        
        return;
    }
    
    
}
?>
