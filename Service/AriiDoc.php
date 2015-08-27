<?php
namespace Arii\CoreBundle\Service;
use Symfony\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;

class AriiDoc {
   
    public function __construct(AriiSession $session)            
    {
        require_once '../vendor/parsedown/Parsedown.php';
    }

    public function Parsedown($doc) {
        $Parsedown = new \Parsedown();
        $parsedown = $Parsedown->text($doc);
        $parsedown = str_replace('<table>','<table class="table table-striped table-bordered table-hover">',$parsedown);
        return $parsedown;
    }
}
?>