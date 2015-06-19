<?php

// src/Arii/CoreBundle/Twig/CoreExtension.php
namespace Arii\CoreBundle\Twig;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;

class CoreExtension extends \Twig_Extension
{
    private $locale;

    public function _construct($locale)
    {
        $this->locale = $locale;
    }

    public function langSwitchToFilter($url,$lang)
    {
        if ($lang == 'fr') return str_replace('/en/',"/$lang/",$url);
        if ($lang == 'en') return str_replace('/fr/',"/$lang/",$url);
    }

    public function getFilters()
    {
        return array(
            'refresh' => new \Twig_Filter_Method($this, 'refreshFilter'),
            'langSwitchTo' => new \Twig_Filter_Method($this, 'langSwitchToFilter')
        );
    }

    public function refreshFilter($default)
    {
        return $default;
    }

    public function getName()
    {
        return 'core_extension';
    }
}

?>