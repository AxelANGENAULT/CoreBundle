<?php

namespace Arii\CoreBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Arii\CoreBundle\Entity\Spooler;
use Arii\CoreBundle\Form\SpoolerType;

/**
 * Spooler controller.
 *
 */
class SpoolerController extends Controller
{
    /**
     * Lists all Spooler entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AriiCoreBundle:Spooler')->findAll();

        return $this->render('AriiCoreBundle:Spooler:index.html.twig', array(
            'entities' => $entities,
        ));
    }

    public function toolbarAction()
    {
        return $this->render('AriiCoreBundle:Spooler:toolbar.xml.twig');
    }

    /**
     * Finds and displays a Spooler entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AriiCoreBundle:Spooler')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Spooler entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AriiCoreBundle:Spooler:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),        ));
    }

    /**
     * Displays a form to create a new Spooler entity.
     *
     */
    public function newAction()
    {
        $entity = new Spooler();
        $form   = $this->createForm(new SpoolerType(), $entity);

        return $this->render('AriiCoreBundle:Spooler:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new Spooler entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity  = new Spooler();
        $form = $this->createForm(new SpoolerType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('spooler_show', array('id' => $entity->getId())));
        }

        return $this->render('AriiCoreBundle:Spooler:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing Spooler entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AriiCoreBundle:Spooler')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Spooler entity.');
        }

        $editForm = $this->createForm(new SpoolerType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('AriiCoreBundle:Spooler:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing Spooler entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AriiCoreBundle:Spooler')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Spooler entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new SpoolerType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('spooler_edit', array('id' => $id)));
        }

        return $this->render('AriiCoreBundle:Spooler:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a Spooler entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AriiCoreBundle:Spooler')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Spooler entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('spooler'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }

   public function logAction()   
    {
        // on renvoie les parametres de telechargement
        return $this->render('AriiCoreBundle:Spooler:log.html.twig',
                array(  'file_uploads' => ini_get('file_uploads'), 
                        'post_max_size' => ini_get('post_max_size'),
                        'upload_max_filesize' => ini_get('upload_max_filesize'))
                );
    }    

    public function log_chartAction()   
    {
                // passé en session
        $session = $this->container->get('arii_core.session');
        $request = Request::createFromGlobals();

        if ($request->get( 'date' )=='') 
            $date = $session->getRefDate( 'ref_date' );
        else 
            $date = $request->get( 'date' );
        
        // $file = 'scheduler-2014-07-17-130156.lsjea.log';
        if ($request->get( 'file' ) == '') exit();
        $file = $request->get( 'file' );

        $workspace = $this->container->getParameter('workspace');        
        $rh = fopen($workspace.'/logs/'.$file, 'r') or die ("error!");
        
        $max=10000;
        $ref = 0;
        $last = '';
        $maxtime=0;
        $maxnb = 0;
        $predate = substr($date,0,8);
        while ((!feof($rh)) and ($max>0)) { 
            $l = fgets($rh, 10240);
            if (strlen($l)<40) continue;
            if (substr($l,0,1)=='.') {
                $d  = substr($l,1,2);
                $h = substr($l,4,2);
                $m = substr($l,7,2);                
                $duration = (int) substr($l,11,7);
            }
            else {
                $d  = substr($l,0,2);
                $h = substr($l,3,2);
                $m = substr($l,6,2);                
                $duration = (int) substr($l,17,6);
            }
            $start = $d*(24*60)+$h*60+$m;            
            if ($ref==0) $ref = $start;
            if (isset($Nb[$start])) { 
                $Nb[$start]++;
                if ($Nb[$start]>$maxnb) $maxnb=$Nb[$start];
            }
            else {
                $Nb[$start]=1;
                $Tooltip[$start]="$d $h:$m";
            }
            if ($duration<1000) continue;
            $point = $start.'-'.$duration;
            if ($point == $last ) continue;
            $last=$point;
            if (isset($Max[$start])) {
                if ($duration>$Max[$start]) $Max[$start]=$duration;
            }
            else { 
                $Max[$start] = $duration;
            }
            if ($duration>$maxtime) $maxtime=$duration;
            $max--;
        }
        $xml = '<data>';
        $high = $maxnb*0.8;
        $id=0;
        foreach (array_keys($Max) as $k) {
            if (($Max[$k]>1000) and ($Nb[$k]>60)) {
                $xml .= '<item id="'.$predate.$Tooltip[$k].':00">';
                $xml .= '<START>'.($k-$ref).'</START>';
                $xml .= '<DURATION>'.round($Max[$k]/1000).'</DURATION>';
                $val = $Nb[$k]*(65536/$maxnb);
                $red = dechex($val / 256);
                $green = dechex(($val % 256));
                $xml .= '<COLOR>#'.substr('0'.$red,-2).substr('0'.$green,-2).'00</COLOR>';
                // $xml .= '<COLOR>#'.$color.'00</COLOR>';
                $xml .= '<NB>'.$Nb[$k].'</NB>';
/*                if ($Max[$k]>$high) 
                    $xml .= '<COLOR>red</COLOR>';
                else
                    $xml .= '<COLOR>orange</COLOR>';
*/                $xml .= '<TOOLTIP>'.$Tooltip[$k].' #'.$Nb[$k].' ('.$Max[$k].'ms)</TOOLTIP>';
                $xml .= '</item>';
            }
        }
        $xml .= '</data>';
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent($xml);
        return $response;        
    }
    
   public function log_schedulerAction()   
    {
        // on renvoie les parametres de telechargement
        return $this->render('AriiCoreBundle:Spooler:scheduler_log.html.twig',
                array(  'file_uploads' => ini_get('file_uploads'), 
                        'post_max_size' => ini_get('post_max_size'),
                        'upload_max_filesize' => ini_get('upload_max_filesize'))
                );
    }    

    public function log_analyzerAction()   
    {
        // passé en session
        $session = $this->container->get('arii_core.session');
        $request = Request::createFromGlobals();
        // $file = 'scheduler-2014-07-17-130156.lsjea.log';
        if ($request->get( 'file' ) == '') exit();
        $file = $request->get( 'file' );
        $text = $request->get( 'text' );

        if ($request->get( 'date' )=='') 
            $date = $session->getRefDate( 'ref_date' );
        else 
            $date = $request->get( 'date' );

        $max = 1000;
        if ($request->get( 'max' ) != '') $max = $request->get( 'max' );
        $format = 1;
        if ($request->get( 'format' ) != '') $format = $request->get( 'format' );
        
        $workspace = $this->container->getParameter('workspace');        
        $rh = fopen($workspace.'/logs/'.$file, 'r') or die ("error!");
        
        // on retrouve le type de log
        $xml = "<?xml version='1.0' encoding='utf-8' ?><rows>";
        $xml .= '<head>
            <afterInit>
                <call command="clearAll"/>
            </afterInit>';
        $xml .= '</head>';
     
        switch ($format) {
            case 1: //scheduler.log unix
                $xml .= $this->log_server($rh,$max,$date,$text);
                break;
            case 2: // scheduler.log windows
                $xml .= $this->log_server2($rh,$max,$date,$text);
                break;
        }
        $xml .= '</rows>';
        fclose($rh);

        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent($xml);
        return $response;
    }
    
    // scheduler.log Windows
    private function log_server($rh,$max=1000,$ref_date='',$text='')   
    {
        $first_date = false;
        $xml = '';
        $last = ''; // eviter les redondances
        $predate = substr($ref_date,0,8);
        while ((!feof($rh)) and ($max>0)) { 
            $l = fgets($rh, 102400);
            
            if ($l == $last) continue;
            // jour du mois 
            // format .dd ou dd
            if (substr($l,0,1)=='.') {
                $d = substr($l,1,2);
                $h = substr($l,4,12);
                $l = trim(substr($l,16));
            }
            else {
                $d = substr($l,0,2);                
                $h = substr($l,3,8);
                $l = trim(substr($l,12));
            }
            $date = $predate.$d.' '.substr($h,0,8);
            // Premiere

            if (($date < $ref_date) and ($first_date)) continue;
            $first_date = true;

            if ($l=='') continue;
            
            // On peut traiter le message
            $last = $l;
            $max--;
                        
            // on cherche l'espace pour la prochaine colonne
            $p = strpos($l,' ');
            $n = substr($l,0,$p);

            $e = strpos($l,' ',$p+1);
            if ($e===false) continue;
            
            $process = substr($l,$p+1,$e-$p-1);

            // on cherche le point
            $p = strpos($process,'.');
            $pid = substr($process,0,$p);
            $thread = substr($process,$p+1);

            $l = substr($l,$e);

            $type = $task = $status = $style = '';
            $section = substr($l,0,5);
            if ($section == "{sche") {
                $type= "scheduler";
                $l= substr($l,12);
                $l = trim($l);
                // on repere les [
                if ($l=='') continue;
                if (substr($l,0,1)=='[') {
                    $sub = substr($l,1,4);
                    if ($sub == 'info') {
                        $status = 'INFO';
                        $style = 'style="background-color: lightblue;"';
                        $l = substr($l,9);
                    }
                    elseif ($sub == 'WARN') {
                        $status = 'WARN.';
                        $style = 'style="background-color: #FFE763;"';
                        $l = substr($l,9);
                    }
                    elseif ($sub == 'ERRO') {
                        $status = 'ERROR';
                        $style = 'style="background-color: #fbb4ae;"';
                        $l = substr($l,9);
                    }
                }
                elseif (substr($l,0,4)=='sos:') {
                    if (substr($l,0,46)=='sos::scheduler::database::Transaction::execute') {
                        $type = "database";
                        $p = strpos($l,' ',48);
                        $status = substr($l,48,$p-48);
                        $l = substr($l,$p);
                    }
                }
                else {
                    $status = "";
                }
                if ($l=='') continue;
                if (substr($l,0,1)=='(') {
                    // Est ce que c'est le log du job
                    $sub = substr($l,1,3);
                    // Est ce que c'est le log du job 
                    if ($sub=='Job') {
                        $d = 4;
                        $e = strpos($l,')',$d);
                        $task = substr($l,$d,$e-$d);
                        $type = "job";
                        $l = substr($l,$e+2);
                    }
                    elseif ($sub=='Tas') {
                        $d = 6;
                        $e = strpos($l,')',$d);
                        $task = substr($l,$d,$e-$d);
                        $type = "task";
                        $l = substr($l,$e+2);
                    }
                    // Est ce que c'est le log du job 
                    elseif ($sub=='Ord') {
                        $d = 7;
                        $e = strpos($l,')',$d);
                        $task = substr($l,$d,$e-$d);
                        $type = "order";
                        $l = substr($l,$e+2);
                    }
                    // Est ce que c'est le log du job 
                    elseif ($sub=='Dat') {
                        $type = "database";
                        $l = substr($l,11);
                    }
                }
            }
            elseif ($section == '-----') {
                $type = 'rotate';
            }
            else {
                $type = 'message';
                $nc = 0;
                while ($l[$nc]==' ') {
                    $nc++;
                }
                $l = str_repeat('&nbsp;',$nc).substr($l,$nc);
            }
            $l = trim($l);
            if ($l=='') continue;
            $xml .= "<row $style>";
            $xml .= "<cell>$d</cell>";
            $xml .= "<cell>$h</cell>";
            $xml .= "<cell>".$n."</cell>";
            $xml .= "<cell>".$pid."</cell>";
            $xml .= "<cell>$thread</cell>";
            $xml .= "<cell>$type</cell>";
            $xml .= "<cell>$status</cell>";
            // $xml .= "<cell>$task</cell>";
            if (substr($l,0,10)=='SCHEDULER-') {
                $l = '['.substr($l,10,3).']'.substr($l,14);
            }
            $l = str_replace(array('<','>'),array('&lt;','&gt;'),$l);
            $xml .= "<cell><![CDATA[".utf8_encode($l)."]]></cell>";
            $xml .= "</row>";
        }
        return $xml;
    }
    // format scheduler-<DATE>.<id>.log
    private function log_server2($rh,$max=1000,$ref_date='',$text='')   
    {
        $first_date = false;
        $xml = '';
        $last = ''; // eviter les redondances
        while ((!feof($rh)) and ($max>0)) {       
            $l = fgets($rh, 10240);
            if ($l == $last) continue;
            $last = $l;
            $Infos = explode(" ",$l,4 );
            if (!isset($Infos[1])) continue;
            $date  = $Infos[0];
            $d = (int) substr($date,8,2);
            $heure = $Infos[1];
            list($date,$heure) = $this->NewTime($date,$heure);

            $test = $date.' '.substr($heure,0,8);
            if (($test<$ref_date) and ($first_date)) continue;
            $first_date = true;
            if ($text!='') {
                if (strpos($l,$text)===false) continue;
            }
            $max--;
            $statut = $Infos[2];
            $msg = trim($Infos[3]);
            if ($msg=='') continue;
            // On sort le type
            if (substr($msg,0,1)=='(') {
                $p = strpos($msg,')');
                $t = substr($msg,1,$p-1);
                if (($e = strpos($t,' '))>0) {
                    $type = substr($t,0,$e);
                    $name = substr($t,$e+1);
                }
                else {
                    $type = $t;                 
                    $name= '';
                }
                $msg = trim(substr($msg,$p+1));
            }
            else {
                $type = '';
                $name = '';
            }
            
            // code
            if (substr($msg,0,10)=='SCHEDULER-') {
                $code = substr($msg,10,3);
                $msg = substr($msg,15);
            }
            else {
                $code = '';
            }
            $s = strtoupper(substr($statut,1,1));
            if ($s=='E') {
                $style = ' style="background-color: #fbb4ae;"';
            }
            elseif ($s=='W') {
                $style = ' style="background-color: #ffffcc;"';
            }
            else {
                $style = '';
            }
            $xml .= "<row$style>";
            $xml .= "<cell>$date</cell>";
            $xml .= "<cell>".$heure."</cell>";
            $xml .= "<cell>".$s."</cell>";
            $xml .= "<cell>".$type."</cell>";
            $xml .= "<cell><![CDATA[".utf8_encode($name)."]]></cell>";
            // suppression des nul ?
            $utf8 = utf8_encode(str_replace('<','&lt;',$msg));
            $utf8 = str_replace("\x00","?",$utf8);
            $xml .= "<cell><![CDATA[".$utf8."]]></cell>";
            $xml .= "<cell>".$code."</cell>";
            $xml .= "</row>";
        }
        return $xml;
    }

    protected function NewTime($date,$time) {
        list($y,$m,$d) = explode('-',$date);
        $m = substr($date,5,2);
        $d = substr($date,8,2);
        $hh = substr($time,0,2);
        $mi = substr($time,3,2);
        $ss = substr($time,6,2);
        $s = substr($time,9,3);
        $g = substr($time,12,3)*3600+substr($time,15);
        $NT = localtime(mktime($hh,$mi,$ss,$m,$d,$y)-$g,true);
        return array(sprintf("%04d-%02d-%02d", 
                $NT['tm_year']+1900,$NT['tm_mon']+1,$NT['tm_mday']),
                sprintf("%02d:%02d:%02d.%03d",
                $NT['tm_hour'],$NT['tm_min'],$NT['tm_sec'],$s));
    }
    
    public function dirlistAction() 
     {
        $files = $this->container->get('arii_core.files');
        $xml = $files->DirList( "logs" );   
        
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');
        $response->setContent( $xml );
        return $response;
    }

    public function uploadAction()   
    {
        $files = $this->container->get('arii_core.files');
        print $files->Upload( "logs" );   

        exit();
    }

}
