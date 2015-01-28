<?php

// src/Arii/CoreBundle/Twig/CoreExtension.php
namespace Arii\CoreBundle\Twig;

class WikiExtension extends \Twig_Extension
{
    

    public function getFilters()
    {
        return array(
            'Wiki2HTML' => new \Twig_Filter_Method($this, 'Wiki2HTMLFilter'),
        );
    }

    public function getName()
    {
        return 'wiki_extension';
    }

    /**
     * Wiki text to HTML script.
     * (c) 2007, Frank Schoep
     *
     * This script will convert the input given on the standard input
     * from Wiki style text syntax to minimally formatted HTML output.
     */

    /**
     * Converts a Wiki text input string to HTML.
     * 
     * @param	array	$input	The array of strings containing Wiki
     * 				text markup.
     * @return	array	An array of strings containing the output
     * 			in HTML.	
     */
    public function Wiki2HTMLFilter($input) {
    // these constants define the list states
        $LS_NONE	=	0;
        $LS_ORDERED	=	1;
        $LS_UNORDERED	=	2;
    // constants for defining preformatted code state
        $CS_NONE	=	0;
        $CS_CODE	=	1;
        
        $LT_OPEN =
            array(
                    $LS_ORDERED	=>	"<ol>",
                    $LS_UNORDERED=>	"<ul>"
            );
        $LT_CLOSE =
            array(
                    $LS_ORDERED =>	"</ol>",
                    $LS_UNORDERED =>	"</ul>"
            );

$RULES =
            array(
                    '/^= (.*) =$/'
                            =>	'<h1>\1</h1>',
                    '/^== (.*) ==$/'
                            =>	'<h2>\1</h2>',
                    '/^=== (.*) ===$/'
                            =>	'<h3>\1</h3>',
                    '/^==== (.*) ====$/'
                            =>	'<h4>\1</h4>',
                    '/^===== (.*) =====$/'
                            =>	'<h5>\1</h5>',
                    '/^====== (.*) ======$/'
                            => '<h6>\1</h6>',
                    '/\[\[(.*?)\]\]/'
                            =>	'<span class="keys">\1</span>',
                    '/^\#* (.+)$/'
                            =>	'<li>\1</li>',
                    '/^\** (.+)$/'
                            =>	'<li>\1</li>',
                    '/\*(.+?)\*/'
                            =>	'<em>\1</em>',
                    "/'''(.+?)'''/"
                            =>	'<b>\1</b>',
                    "/''(.+?)''/"
                            =>	'<i>\1</i>',
                    '/`(.+?)`/'
                            =>	'<tt>\1</tt>',
                    '/^----$/'
                            =>	'<hr />'
            );

            // output array
            $output = array();

            // reset initial list states
            $liststate = $LS_NONE;
            $listdepth = 1;
            $prevliststate = $liststate;
            $prevlistdepth = $listdepth;

            // preformatted code state
            $codestate = $CS_NONE;

            // loop through the input
            foreach(explode("\n",$input) as $in) {
                    // read, htmlify and right-trim each input line
                    $in = htmlspecialchars(rtrim($in));
                    $out = $in;		

                    // match against Wiki text to HTML rules
                    foreach($RULES as $pattern => $replacement) {
                            $out = preg_replace($pattern, $replacement,
                                    $out);
                    }

                    // determine list state based on leftmost character
                    $prevliststate = $liststate;
                    $prevlistdepth = $listdepth;
                    $l = substr(ltrim($in), 0, 1);
                    switch($l) {
                            case '#':
                                    $liststate = $LS_ORDERED;
                                    // $listdepth = strpos($in, '1');
                                    $listdepth=1;
                                    while ($in[$listdepth]=='#') { $listdepth++; };
                                    //$out = substr($in,$listdepth);
                                    break;
                            case '*':
                                    $liststate = $LS_UNORDERED;
                                    $listdepth=1;
                                    while ($in[$listdepth]=='*') { $listdepth++; };
                                    //$out = substr($in,$listdepth);
                                    break;
                            default:
                                    $liststate = $LS_NONE;
                                    $listdepth = 1;
                                    break;
                    }

                    // check if list state has changed
                    if($liststate != $prevliststate) {
                            // close old list
                            if($LS_NONE != $prevliststate) {
                                    $output[] =
                                            $LT_CLOSE[$prevliststate];
                            }

                            // start new list
                            if($LS_NONE != $liststate) {
                                    $output[] = $LT_OPEN[$liststate];
                            }
                    }

                    // check if list depth has changed
                    if ($listdepth != $prevlistdepth) {
                            // calculate the depth difference
                            $depthdiff = abs($listdepth - $prevlistdepth);

                            // open or close tags based on difference
                            if($listdepth > $prevlistdepth) {
                                    for($i = 0;
                                            $i < $depthdiff;
                                            $i++)
                                    {
                                            $output[] =
                                                    $LT_OPEN[$liststate];
                                    }
                            } else {
                                    for($i = 0;
                                            $i < $depthdiff;
                                            $i++)
                                    {
                                            $output[] =
                                                    $LT_CLOSE[$prevliststate];
                                    }
                            }
                    }

                    // determine output format
                    if('' == $in) {
                    } else if ('{{{' == trim($in)) {
                            $output[] = '<pre>';
                            $codestate = $CS_CODE;
                    } else if ('}}}' == trim($in)) {
                            $output[] = '</pre>';
                            $codestate = $CS_NONE;
                    } else if (
                            $in[0] != '=' &&
                            $in[0] != ' ' &&
                            $in[0] != '-')
                    {
                            // only output paragraphs when not in code
                            if($CS_NONE == $codestate) {
                                    $output[] = '';
                            }

                            $output[] = "$out";

                            // only output paragraphs when not in code
                            if($CS_NONE == $codestate) {
                                    $output[] = '';
                            }
                    } else {
                            $output[] = $out;
                    }
            }

            // return the output
            return implode("\n",$output);
    }
}

?>