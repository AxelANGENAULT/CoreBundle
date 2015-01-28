<?php

// src/Arii/CoreBundle/Twig/CoreExtension.php
namespace Arii\CoreBundle\Twig;

class CoreExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'refresh' => new \Twig_Filter_Method($this, 'refreshFilter'),
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