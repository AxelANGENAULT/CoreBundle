<?php
namespace Arii\CoreBundle\Service;
use Symfony\Component\Translation\Translator;

class AriiSSH {
    
    protected $session; 
    
    public function __construct(AriiSession $session) {
        $this->session = $session;
        set_include_path(get_include_path() . PATH_SEPARATOR . '../vendor/phpseclib');
        include('Net/SSH2.php');   
    }
    
    public function Exec($host,$user,$password,$command) {
        $ssh = new Net_SSH2($host);
        if (!$ssh->login($user, $password)) {
            exit('Login Failed');
        }

        echo $ssh->exec("system '$command'");        
    }

}
?>
