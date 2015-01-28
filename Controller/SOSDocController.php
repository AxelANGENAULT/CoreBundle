<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SOSDocController extends Controller
{
	private $doc;
	private $topic;
	private $root;
	private $docs;
	private $images;
	private $Done = array();
	private $level=0;
	private $part=0;
	private $lang='en';
	private $title;
        private $UniqId;
        private $width=0;
        private $height=0;

    public function viewAction($doc)  {
        $html = $this->publishAction($doc);  
        return $this->render('AriiPublicBundle:Doc:index.html.twig',array('title'=>$this->title, 'html' => $html) );
    }
    
    public function publishAction($doc)  {
		$this->doc = $doc;
                $this->title = $doc;
                $request = $this->getRequest();
                $locale = $request->getLocale();
		$this->lang = substr($locale,0,2);
		$this->root = $this->get('kernel')->getRootDir().'/../web/docs/en/'.$doc;
		$this->docs = $this->getRequest()->getUriForPath('/../docs');
		$this->images = $this->getRequest()->getUriForPath('/../arii/images/doc');
		$Html = $this->Xml2Html($doc,'scheduler_quickstart.sosdoc',$this->lang);
                return utf8_encode(str_replace('[{[','<',str_replace(']}]','>',$Html)));
/*
                $response = new Response();
		$response->setContent(utf8_encode(str_replace('[{[','<',str_replace(']}]','>',$Html))));
		return $response;
 */
    }

	private function GetDoc($doc,$section) {
		$file = $this->root."/$section";
		if (isset($Done[$file])) return '';
		$Done[$file]=1;
		$sXml  = @file_get_contents($file);
		if ($sXml == '') {
			return '[{[font color="red"]}]'.$section.'[{[/font]}]';
		}
		# Suppression du <?xml
		if (($p = strpos(' '.$sXml,'<?xml'))>0) {
			$e = strpos($sXml,'?>');
			$sXml = substr($sXml,0,$p-1).substr($sXml,$e-$p+3);
		}
		return $sXml;
	}
	
	private function Xml2Html($doc,$section,$lang) {
		$sXml = $this->GetDoc($doc,$section);
		$this->topic = $section;
		# <test>data</test>
		# <test id="autre">data</test>
		# <test/>		
		# <test id="autre"/>
		# <test><test>data</test></test>
		# On cherche les fins simples
		# On supprime les commentaires
		while (($p = strpos(' '.$sXml,'<!--'))>0) {
			# on cherche la fin d�but
			$e = strpos($sXml,'-->',$p);
			if ($e === FALSE)
				return "[{[span style='background-color: red; color: yellow; padding: 10px;']}]ERROR [$doc] $section COMMENT (pos:$p)[{[/span]}]";
			$sXml = substr($sXml,0,$p-1).substr($sXml,$e+3);
		}
		# tags simples
		while (($p = strpos($sXml,'/>'))>0) {
			# on cherche le d�but
			$d = $p-1;
			while (($d>=0) and (substr($sXml,$d,1)!= '<')) { $d--; };
			$b = substr($sXml,$d+1,$p-$d-1);
			$new = $this->ReplaceTag($b);
			$sXml = substr($sXml,0,$d).$new.substr($sXml,$p+2);
		}
		# On cherche les tags de fins
		while (($p = strpos($sXml,'</'))>0) {
			# on cherche la fin
			$f = $p+2;
			$len = strlen($sXml); 
			while (($f<$len) and (substr($sXml,$f,1)!= '>')) { $f++; };
			$b = substr($sXml,$p+2,$f-$p-2);
			$new = $this->ReplaceTag($b);
			
			# on cherche la balise de debut
			$len = strlen($b);
			$d = $p-1; $last=-1;
			while (($d>=0) and (substr($sXml,$d,1)!= "<")) {
				# on en profite pour trouver le dernier >
				if (substr($sXml,$d,1)=='>') $last = $d; 
				$d--;
			};
			if (substr($sXml,$d,$len+1)!="<$b") {
				return "[{[span style='background-color: red; color: yellow; padding: 10px;']}]ERROR [$doc] $section $b (pos:$p) ".substr($sXml,$d,$len+1)." [{[/span]}]";
			}				
			if ($last < 0 ) {
				$sXml = substr($sXml,0,$d)."[{[font color='red']}]".substr($sXml,$d+1,$p-$d-1)."[{[/font]}]".substr($sXml,$f+1);				
			}
			else {
				$attr = substr($sXml,$d+$len+1,$last-$d-$len-1);
				$data = substr($sXml,$last+1,$p-$last-1);
				$new = $this->Transform($b,$attr,$data,$lang);
				$sXml = substr($sXml,0,$d).$new.substr($sXml,$f+1);
			}
		}
		# On traite les inclusions
		//$this->GetDoc($this->doc,$section);
                $this->level++;
		while (($p = strpos($sXml,'[[['))>0) {
			$e = strpos($sXml,']]]',$p);
			list($from,$file) = explode('|',substr($sXml,$p+3,$e-$p-3));
			# print "(($from => ".dirname($from).")/$file)))<br/>";
			$this->part++;
			$sXml =  substr($sXml,0,$p).$this->Xml2Html($doc,dirname($from).'/'.$file,$lang).substr($sXml,$e+3);
		}
		$this->part=0;
		return $sXml;
	}

    private function ReplaceTag($tag)  {
		if (($p=strpos($tag,' '))>0) {
			$attr = substr($tag,$p+1);
			$tag = substr($tag,0,$p);
			$Infos = $this->Attr2Array($attr);
			if ($tag == 'xi:include') {
				if (isset($Infos['href'])) {
					$section = $Infos['href'];
					if (isset($Infos['xml:base']))
						$section = $Infos['xml:base'].'/'.$section;
					return '[[['.$this->topic.'|'.$section.']]]';
				}
				else {
					return '[{[span class="label label-important"]}]'.$tag.'[{[/span]}]';
				}
			}
			elseif ($tag == 'image') {
                                // On conserve les dernieres largeur/hauteur
                                $width = $height = '';
                                if (isset($Infos['width'])) {
                                    $this->width=$Infos['width'];
                                    $width = ' width="'.round($this->width/2).'"';
                                }
                                if (isset($Infos['height'])) {
                                    $this->height=$Infos['height'];
                                    $height = ' height="'.round($this->height/2).'"';
                                }
 				return '[{[img src="'.$this->docs.'/en/scheduler_quickstart/'.$Infos['src'].'"'.$width.$height.' class="img-polaroid"/]}]';
                         }
		}
		if (($tag == 'br') or ($tag == 'hr')) {
			return '[{['.$tag.'/]}]';
		}
		elseif ($tag == 'sosdocpdflink') {
			return '[{[a href="'.$this->docs.'/en/'.$Infos['linkto'].'/pdf/'.$this->lang.'/'.$Infos['linkto'].'.pdf"]}][{[img src="'.$this->images.'/book_go.png"/]}][{[/a]}]';
		}
		elseif ($tag == 'JobScheduler') {
			return '[{[strong]}]'.$tag.'[{[/strong]}]';
		}
                elseif ($tag == 'description') {
                    return '';
                }

        elseif ($tag == 'p') {
            return '';
        }
		else {
			return '[{[span class="'.$tag.'"]}]'.$this->get('translator')->trans($tag).'[{[/span]}]';
		}
	}
	
    private function Transform($tag,$attr,$data,$lang)  {
		$Infos = $this->Attr2Array($attr);
		if (isset($Infos['language']) and ($Infos['language']!=$lang)) return '';
		if (isset($Infos['display']) and ($Infos['display']=='false')) return '';
        if ($tag == 'topic') { 
            return '[{[div id="topic"]}][{[a name="'.$Infos['id'].'"]}][{[/a]}]'.$data.'[{[/div]}]';
        }
		elseif ($tag == 'button') {
			//return '[{[span class="button send_form_btn"]}][{[span]}][{[span]}]'.$data.'[{[/span]}][{[/span]}][{[/span]}]';
			return '[{[span class="badge badge-inverse"]}]'.$data.'[{[/span]}]';
		}
        elseif ($tag == 'documentation')
            return $data;
        elseif ($tag == 'terme')
            return '[{[i]}][{[strong]}]'.$data.'[{[/strong]}][{[/i]}]';      
        elseif ($tag == 'glossary')
            return $data;
        elseif ($tag == 'glossarlink')
            return '[{[i]}]'.$data.'[{[/i]}]';
	elseif ($tag == 'glossaryTerm')
            return '[{[div id="G_'.$Infos['id'].'" style="display: none;"]}][{[blockquote]}]'.$data.'[{[/blockquote]}][{[/div]}]';
        elseif ($tag == 'p') {
            // Si le paragraphe contient une image
/*            if (($p=strpos(' '.$data,'[{[img'))>0) {
                // le paragraphe ne contient que l'image
                $test = str_replace(' ','',substr($data,0,$p));
                $test = str_replace("\t","",$test);
                $test = str_replace("\r","",$test);
                $test = str_replace("\n","",$test);
                if ($test == '') {
                    return $data;
                }
                // on ne prend pas en compte ce paragraphe
                // return $data;
            }
*/
            // Si on a une image en cours
/*            if ($this->height>0) {
                $div = '[{[div style="border: 1px solid red; height: '.(round($this->height/2)+40).'px;"]}][{[p]}]'.$data.'[{[/p]}][{[/div]}]';
                $this->height=0;
                return $div; 
            }
*/            return '[{[blockquote]}]'.$data.'[{[/blockquote]}]';
        }
		elseif ($tag == 'title') {
                        if ($this->level>0) {
                            $this->UniqId++;
                            return '[{[h3]}]'.$data.'[{[/h3]}]';
                        }    
                        else
                            $this->title = $data;
		}
                elseif ($tag == 'description')
                    return '[{[div id="T_'.$this->UniqId.'"]}]'.$data.'[{[/div]}]';
		elseif ($tag == 'list') {
			if (isset($Infos['type']) and ($Infos['type']=='ordered')) 
				return '[{[ol]}]'.$data.'[{[/ol]}]';
			else
				return '[{[ul]}]'.$data.'[{[/ul]}]';
		}
		elseif ($tag == 'item') 
			return '[{[li]}]'.$data.'[{[/li]}]';
		elseif ($tag == 'subtitle') {
                        $this->title = $data;
                        return '';
 			return '[{[h4]}]'.$data.'[{[/h4]}]';
		}
		elseif ($tag == 'item') 
			return '[{[li]}]'.$data.'[{[/li]}]';
		elseif ($tag == 'embeddedExample') {
                    if (($Infos['type'] == 'code') 
                            || ($Infos['type'] == 'envvar')) 
			return '[{[pre]}]'.trim($data).'[{[/pre]}]';
		}
		// <indexterm glossarid="job_chain" index="Job chain">job chains</indexterm>
		elseif ($tag == 'indexterm') {
			if (isset($Infos['glossarid'])) {
                                return '[{[a href="#" data-toggle="tooltip'.$Infos['glossarid'].'" title="test'.$Infos['glossarid'].'"]}]'.$data.'[{[/a]}]';
				# return '[{[a href="#G_'.$Infos['glossarid'].'" class="glossary"]}]'.$data.'[{[/a]}]';
                                
			}
			else return '[{[span class="label label-info"]}]'.$data.'[{[/span]}]';
		}
		elseif (($tag == 'path') || ($tag == 'file')  || ($tag == 'folder'))
			return '[{[code]}]'.$data.'[{[/code]}]';
		elseif ($tag == 'command')
			return '[{[pre]}]'.$data.'[{[/pre]}]';
		elseif ($tag == 'link')
			return '[{[a href="#'.$Infos['id'].'"]}]'.$data.'[{[/a]}]';
		elseif (($tag == 'company')
				|| ($tag == 'author'))
			return '';
                elseif ($tag == 'formlabel')
			return '[{[span class="badge badge-warning"]}]'.$data.'[{[/span]}]';
                elseif ($tag == 'formvalue')
			return '[{[span class="label label-warning"]}]'.$data.'[{[/span]}]';
                elseif ($tag == 'key')
			return '[{[span class="btn btn-info"]}]'.$data.'[{[/span]}]';
                elseif ($tag == 'menu')
			return '[{[span class="btn"]}]'.$data.'[{[/span]}]';
                elseif ($tag == 'envvar')
			return '[{[code]}]'.$data.'[{[/code]}]';
                elseif (in_array($tag,array('table','tr','td','thead','tbody','strong','topics')))
                        return '[{['.$tag.']}]'.$data.'[{[/'.$tag.']}]';
                 elseif (in_array($tag,array('codeexample')))
                        return $data;
                else return '[{[span class="'.$tag.'"]}](('.$tag."))".$data.'[{[/span]}]';
    }

    private function Attr2Array($attr) {
		// on supprime les sauts de ligne
		$attr = str_replace("\n",' ',$attr);
		$attr = str_replace("\r",' ',$attr);
		$Result = array();
		$Attrs = explode(' ',$attr);
		foreach ($Attrs as $a) {
			if (strpos($a,'=')>0) {
				list($var,$val) = explode('=',$a);
				$val=str_replace('"','',$val);
				$Result[$var] = $val;
			}
		}
		return $Result;
    }
}