<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WikiController extends Controller
{
    public function readAction($doc,$template)
    {
        $root = $this->get('kernel')->getRootDir();
        $request = Request::createFromGlobals();
        $locale = $this->get('request')->getLocale();
        $default = $this->container->getParameter('locale');
        if ($wiki = @file_get_contents ( $root.'/../wiki/'.$doc.'.'.$locale.'.wiki' )) {
        }
        elseif ($wiki = @file_get_contents ( $root.'/../wiki/'.$doc.'.'.$default.'.wiki' )) {
        }
        else {
            file_put_contents ( $root.'/../wiki/'.$doc.'.todo', "TODO" );
            $wiki =  '= TO DO! =';
        }
        return $this->render($template, array('doc' => 'doc.'.str_replace('/','.',$doc), 'wiki' => $wiki ));
    }
             
}