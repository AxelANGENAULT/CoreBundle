<?php
namespace Arii\CoreBundle\Service;

use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class AriiPortal
{
    protected $session;
    protected $context;
    protected $translator;
    protected $router;
    
    public function __construct(    AriiSession $session, 
                                    SecurityContext $context, 
                                    TranslatorInterface $translator,
                                    $router )
    {
        $this->session = $session;
        $this->context = $context;
        $this->translator = $translator;
        $this->router = $router;
    }

    public function getModules() {
        $sc = $this->context;
        $session = $this->session;
        
        $Params = array();
        $Result = array();
        # Les modules pour tout le monde
        $param = $session->getModules(); 
        if ($param != '')
            foreach (explode(',',$param) as $p)
                array_push($Params, $p);
                
        # On retrouve l'url active 
        foreach ($Params as $p) {
            // Modules limites à un droit ?
            if (($d = strpos($p,'('))>0) {
                $module = substr($p,0,$d);
                $f = strpos($p,')',$d+1);
                $role = substr($p,$d+1,$f-$d-1);
                $p = '';
                if (($sc->isGranted('IS_AUTHENTICATED_FULLY')) 
              or ($sc->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
                    if ($sc->isGranted($role))
                        $p = $module;
                }
                else {
                    if ($role == 'ANONYMOUS')
                        $p = $module;
                }
            }
            else {
                $role = '';
            }
            if ($p!='') 
                $Result[$p] = array(
                    'BUNDLE'=>$p,
                    'role' => $this->translator->trans($role), 
                    'mod' => strtolower($p), 
                    'name' => $this->translator->trans('module.'.$p), 
                    'desc' => $this->translator->trans('text.'.$p), 
                    'summary' => $this->translator->trans('summary.'.$p), 
                    'img' => "$p.png",
                    'url' => $this->router->generate('arii_'.$p.'_index') );
        } 
        return $Result;
    }

}
