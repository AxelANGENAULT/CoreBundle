<?php
// src/Arii/JOEBundle/Controller/XMLController.php

namespace Arii\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SOSController extends Controller
{
   protected $id_why=0;
    
   public function XMLCommandAction( )
   {
        $audit = $this->container->get('arii_core.audit');
        $errorlog = $this->container->get('arii_core.log');
        
        $request = Request::createFromGlobals();
        $xml_command = $request->get( 'command' );
        $spooler_id = $request->get( 'spooler_id' );
        switch ($xml_command) {            
            case 'add_order': 
                // En entrée:
                //   order_id: identifiant du traitement
                $id = $request->get('id');                
                $order_id = $request->get('order_id');    
                $title = $request->get('title'); 
                $at = $request->request->get('at');
                $start_state = $request->request->get('start_state');
                $end_state = $request->request->get('end_state');
                // informations du job
                list($spooler_id,$oid,$job_chain) = $this->getOrderInfos($id);
                
                // Cas particulier de la nested job chain qui n'est pas dans la DB
                if ($request->get( 'chain' ) != 'undefined') {
                    $chain = trim($request->get( 'chain' ));
                    $job_chain = dirname($job_chain).'/'.$chain;
                    # on force l'order id si il y a un oubli
                    if (strpos($order_id,'.')==0) {
                        $order_id = $chain.'.'.$order_id;
                    }
                }
                
                $cmd = '<add_order job_chain="'.$job_chain.'" id="'.$order_id.'"';
                if ($title!='') {
                    $cmd .= ' title="'.$title.'"';
                }
                if ($start_state!=''){
                    $cmd .= ' state="'.$start_state.'"';
                }
                if ($end_state!='' && $end_state !="none")
                {
                    $cmd .= ' end_state="'.$end_state.'"';
                }
                if ($at == '') $at = 'now';
                $cmd .= ' at="'.$at.'">';
                $params_string = $request->request->get( 'paramsStr' );
                if ($params_string!='') {
                    $cmd  .= '<params>';
                    foreach (explode(',',urldecode($params_string)) as $params) { 
                       // $val = $request->request->get( $var );
                        $param = explode('=', $params);
                        $cmd  .= '<param name="'.$param[0].'" value="'.$param[1].'"/>';
                    }
                    $cmd  .= '</params>';
                 }
                 $cmd .= '</add_order>';
                break;
            case 'start_order':
                // En entrée:
                //   order_id: identifiant du traitement
                //   at: heure de depart
                 $id = $request->get('order_id');
                 $at = $request->get('time');
/*
                 if ($request->get('plan') === 'yes') { 
                    $Infos = explode('/',$id);
                    $spooler_id = array_shift($Infos);
                    $order_id = array_pop($Infos);
                    $job_chain = implode('/',$Infos);
                }
                else {
                    // informations du job
                    list($spooler_id,$order_id,$job_chain) = $this->getOrderInfos($id);                 
                }
 */
                 list($spooler_id,$order_id,$job_chain) = $this->getOrderInfos($id);                 
                 // Attention! si c'est un ordre statique, il faut un add order
                 $cmd = '<modify_order order="'.$order_id.'" job_chain="'.$job_chain.'"';
                 if ($at == '') $at = 'now';
                 $cmd .= ' at="'.$at.'">';
                 
                 $params_string = $request->get('params');
                 if ($params_string!='') {
                    $cmd .= '<params>';
                    foreach (explode(',',urldecode($params_string)) as $params) {
                        $param = explode('=', $params);
                        $cmd  .= '<param name="'.$param[0].'" value="'.$param[1].'"/>';
                    }
                    $cmd .= '</params>';
                 }
                 $cmd .= '</modify_order>';
                 break;                    
            case 'start_job':
                // En entrée:
                //   job_id: identifiant du traitement
                //   at:     heure de démarrage
                //   params: paramètres
                $params_string = $request->get('params');
                $at = $request->get('time');
                $job_id = $request->get('job_id');   
                // informations du job
                list($spooler_id,$job) = $this->getJobInfos($job_id);
                // construction de la commande
                $cmd  = '<start_job job="'.$job.'"';
                if ($at == '') $at = 'now';
                $cmd .= ' at="'.$at.'">';
                //$parameters = $request->request->get( 'parameters' );
                 if ($params_string!='') {
                    $cmd  .= '<params>';
                    foreach (explode(',',urldecode($params_string)) as $params) { 
                       // $val = $request->request->get( $var );
                        $param = explode('=', $params);
                        $cmd  .= '<param name="'.$param[0].'" value="'.$param[1].'"/>';
                    }
                    $cmd  .= '</params>';
                 }
                 $cmd .= '</start_job>';
                break;
            case 'why_job':
                // En entrée:
                //   job_id: identifiant du traitement
                $job_id = $request->get('job_id');
                // informations du job
                list($spooler_id,$job) = $this->getJobInfos($job_id);
                // construction de la commande
                $cmd  = '<job.why job="'.$job.'"/>';
                break;
            case 'kill_task': 
                 $job_id = $request->get( 'job_id' );
                 if (($p=strpos($job_id,'#'))>0) {
                     $job_id=substr($job_id,0,$p);
                 }
                 list($spooler_id,$job) = $this->getJobInfos($job_id);
                 $cmd  = '<kill_task job="'.$job.'"';
                 $cmd .= ' id="'.$job_id.'"';
//                 if ($request->request->get( 'immediately' )=='yes')
// http://www.sos-berlin.com/mediawiki/index.php/What_is_the_difference_between_%22end%22_and_%22kill_immediately%22%3F
                 $cmd .= ' immediately="yes"';
                 $cmd .= '/>';                 
                 break;
            case 'delete_task': 
                 $job_id = $request->get( 'job_id' );
                 if (($p=strpos($job_id,'#'))>0) {
                     $job_id=substr($job_id,0,$p);
                 }
                 list($spooler_id,$job) = $this->getTaskInfos($job_id);
                 $cmd  = '<kill_task job="'.$job.'"';
                 $cmd .= ' id="'.$job_id.'"';
//                 if ($request->request->get( 'immediately' )=='yes')
// http://www.sos-berlin.com/mediawiki/index.php/What_is_the_difference_between_%22end%22_and_%22kill_immediately%22%3F
                 $cmd .= ' immediately="yes"';
                 $cmd .= '/>';                 
                 break;
            case 'stop_job':
            case 'unstop_job':
                // En entrée:
                //   job_id: identifiant du traitement
                $job_id = $request->request->get( 'job_id' );
                // informations du job
                list($spooler_id,$job) = $this->getJobInfos($job_id);
                // construction de la commande
                if ($xml_command=="stop_job")
                {
                    $cmd  = '<modify_job job="'.$job.'" cmd="stop"/>';
                } elseif ($xml_command=="unstop_job")
                {
                    $cmd  = '<modify_job job="'.$job.'" cmd="unstop"/>';
                }
                break;
            case 'resume_order':
            case 'reset_order':
            case 'remove_setback':
            case 'suspend_order':
                $id = $request->get('id');
                
                 list($spooler_id,$order,$job_chain) = $this->getOrderInfos($id);                 
                 if($xml_command=="suspend_order")
                {
                    $cmd = '<modify_order job_chain="'.$job_chain.'" order="'.$order.'" suspended="yes" />';
                } elseif ($xml_command=="resume_order")
                {
                    $cmd = '<modify_order job_chain="'.$job_chain.'" order="'.$order.'" suspended="no" />';
                } elseif ($xml_command=="reset_order")
                {
                    $cmd = '<modify_order job_chain="'.$job_chain.'" order="'.$order.'" action="reset" />';
                } elseif ($xml_command=="remove_setback")
                {
                    $cmd = '<modify_order job_chain="'.$job_chain.'" order="'.$order.'" setback="no" />';
                }
                
                break;	
            /*    
            case 'modify_order_prompt':
                 $order = $request->request->get( 'order' );
                 $job_chain = $request->request->get( 'job_chain' );
                 $state = $request->request->get( 'state' );
                 $cmd  = '<modify_order job_chain="'.$job_chain.'" state="'.$state.'" order="'.$order.'" suspended="no"><params><param name="scheduler_prompt" value="true"/></params></modify_order>';
                break;
            case 'remove_order':
                 $order = $request->request->get( 'order' );
                 $job_chain = $request->request->get( 'job_chain' );
                 $cmd  = '<remove_order job_chain="'.$job_chain.'" order="'.$order.'"></remove_order>';
                break;
             * 
             */
            case 'abort_spooler':
                $cmd = "<modify_spooler cmd='terminate'/>";
                break;
            case 'terminate_spooler':
                $cmd = "<modify_spooler cmd='abort_immediately'/>";
                break;
            case 'restart_spooler':
                $cmd = "<modify_spooler cmd='terminate_and_restart'/>";
                break;
            case 'pause_spooler':
                $cmd = "<modify_spooler cmd='pause'/>";
                break;
            case 'continue_spooler':
                $cmd = "<modify_spooler cmd='continue'/>";
                break;
            case 'stop_node':
            case 'unstop_node':
            case 'skip_node':
            case 'unskip_node':
                $id = $request->get('id');
                list($spooler_id,$order,$job_chain,$state) = $this->getStateInfos($id); 
                switch($xml_command){
                    case "stop_node":
                        $cmd = '<job_chain_node.modify job_chain="'.$job_chain.'" state="'.$state.'" action="stop" />';
                        break;
                    case "skip_node":
                        $cmd = '<job_chain_node.modify job_chain="'.$job_chain.'" state="'.$state.'" action="next_state" />';
                        break;
                    case "unstop_node":
                    case "unskip_node":
                        $cmd = '<job_chain_node.modify job_chain="'.$job_chain.'" state="'.$state.'" action="process" />';
                        break;
                    default:
                        break;
                }
                break;
/* A corriger pour postgres ! */
                $state_id = $request->get('id');
                $qry = 'SELECT sjcn.spooler_id,sjcn.job_chain,sjcn.order_state,ac.interface as hostname,ac.port as tcp_port,ac.path,an.protocol
                        FROM SCHEDULER_JOB_CHAIN_NODES sjcn
                        LEFT JOIN ARII_SPOOLER asp
                        ON sjcn.spooler_id=asp.scheduler
                        LEFT JOIN ARII_CONNECTION ac
                        ON asp.connection_id = ac.id
                        LEFT JOIN ARII_NETWORK an
                        ON ac.network_id = an.id
                        WHERE CONCAT(sjcn.spooler_id,"/",sjcn.job_chain,"/",sjcn.order_state) = "'.$state_id.'"';
                $res = $data->sql->query($qry);
                $line = $data->sql->get_next($res);

                $job_chain = $line['job_chain'];
                $state = $line['order_state'];
                switch($xml_command){
                    case "stop_node_job":
                        $cmd = '<job_chain_node.modify job_chain="'.$job_chain.'" state="'.$state.'" action="stop" />';
                        break;
                    case "skip_node_job":
                        $cmd = '<job_chain_node.modify job_chain="'.$job_chain.'" state="'.$state.'" action="next_state" />';
                        break;
                    case "unstop_node_job":
                    case "unskip_node_job":
                        $cmd = '<job_chain_node.modify job_chain="'.$job_chain.'" state="'.$state.'" action="process" />';
                        break;
                    default:
                        break;
                }
                break;
            case 'stop_chain':
            case 'unstop_chain':
                $id = $request->get('id');
                list($spooler_id,$order_id,$job_chain) = $this->getOrderInfos($id);
                
                // Cas particulier de la nested job chain qui n'est pas dans la DB
                if ($request->get( 'chain' ) != 'undefined') {
                    $chain = trim($request->get( 'chain' ));
                    $job_chain = dirname($job_chain).'/'.$chain;
                }

                if($xml_command == "stop_chain")
                {
                    $cmd = '<job_chain.modify job_chain="'.$job_chain.'" state="stopped" />';
                } 
                if($xml_command == "unstop_chain")
                {
                    $cmd = '<job_chain.modify job_chain="'.$job_chain.'" state="running" />';
                }
                break;
            default:
                $cmd = "Unknown command !!";
                print "XML Command '$xml_command' ?!";
                exit();
        }
        // Recherche les informations de connexion
        list($protocol,$scheduler,$port,$path) = $this->getConnectInfos($spooler_id);                
        
        if (!isset($cmd)) {
            $errorlog->Error("XML Command undefined",0,__FILE__,__LINE__,__FUNCTION__);
            print "Undefined XML Command";
            exit();
        }
print "<pre>$cmd</pre>";
        
        $SOS = $this->container->get('arii_core.sos');
        $result = $SOS->XMLCommand($spooler_id,$scheduler,$port,$path,$protocol,$cmd);
print "<pre>$cmd</pre>";
        if (isset($result['ERROR'])) {
            if (substr($result['ERROR'],0,7) === 'CONNECT') {
                $t = $this->get('translator')->trans('Connection failed %protocol%://%host%:%port%! Please make sure the JobScheduler have started!', array('%protocol%' => $protocol,'%host%' => $scheduler,'%port%' => $port ));
                // on modifie le statut dans la base de données
                $dhtmlx = $this->container->get('arii_core.dhtmlx');
                $data = $dhtmlx->Connector('data');
                
                $sql = $this->container->get('arii_core.sql');
                $qry = $sql->Update(array('SCHEDULER_INSTANCES'))
                        .$sql->Set(array('IS_RUNNING' => 0))
                        .$sql->Where(array('SCHEDULER_ID' => $spooler_id));
                $res = $data->sql->query( $qry );
            }
            else {
                $t = $this->get('translator')->trans('Unknown error !');    
            }
            print "<font color='red'>".$this->get('translator')->trans('ERROR !')."</font>";
            print "<p>$t</p>";
            // print_r($result);
            exit();
            return new Response($t);
        }

        if (!isset($result['spooler'])) {
            print "<font color='red'>No answer!</font>";
            exit();
        }
        if (!isset($result['spooler']['answer'])) {
            print "<font color='red'>".$result."</font>";
            exit();
        }
        
        // Si c'est pas bon on sort en erreur
        if (!isset($result['spooler']['answer']['ok'])) {
            if (isset($result['spooler']['answer']['ERROR'])) {
                if (isset($result['spooler']['answer']['ERROR_attr'])) {
                    print $result['spooler']['answer']['ERROR_attr']['time'].": ";
                    print "<font color='red'>";
                    print $result['spooler']['answer']['ERROR_attr']['text'];
                    print "</font>";
                    exit();
                }
            }
        }
        
        switch ($xml_command) {
           case 'why_job':
                $xml = $this->XmlWhy($result['spooler']['answer']);
                header('Content-type: text/xml');
                print "<?xml version='1.0' encoding='utf-8' ?>";
                print "<rows>$xml</rows>";
                break;
           default:
                print "<table>";
                if (isset($result['spooler']['answer_attr']['time']))
                    print "<tr><th align='right'>Executed</th><td>".$result['spooler']['answer_attr']['time']."</td></tr>";
               if (isset($result['spooler']['answer']['ok']['order_attr'])){
                    foreach (array('job_chain','order','created','state') as $k) {
                        if (isset($result['spooler']['answer']['ok']['order_attr'][$k]))
                            print "<tr><th align='right'>$k</th><td>".$result['spooler']['answer']['ok']['order_attr'][$k]."</td></tr>";
                    }
                }
                if (isset($result['spooler']['answer']['ok']['task_attr'])){
                    foreach (array('job','id','start_at','state') as $k) {
                        if (isset($result['spooler']['answer']['ok']['task_attr'][$k]))
                            print "<tr><th align='right'>$k</th><td>".$result['spooler']['answer']['ok']['task_attr'][$k]."</td></tr>";
                    }
                }
                print "</table>";
        }
        exit();
   }

   private function getJobInfos($job_id) {   
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');
       
        // le job_id peut avoir une tâche
        if (($p = strpos($job_id,'#'))>0) {
            $job_id = substr($job_id,0,$p);
        }
        $sql = $this->container->get('arii_core.sql');
        $qry = $sql->Select(array('sh.JOB_NAME','sh.SPOOLER_ID','sh.PARAMETERS'))
                .$sql->From(array('SCHEDULER_HISTORY sh'))
                .$sql->Where(array('sh.id' => $job_id));
        $res = $data->sql->query( $qry );
        $line = $data->sql->get_next($res);

        return array($line['SPOOLER_ID'],$line['JOB_NAME'],$line['PARAMETERS']);        
   }

   private function getTaskInfos($job_id) {   
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');
       
        // le job_id peut avoir une tâche
        if (($p = strpos($job_id,'#'))>0) {
            $job_id = substr($job_id,0,$p);
        }
        $sql = $this->container->get('arii_core.sql');
        $qry = $sql->Select(array('st.JOB_NAME','st.SPOOLER_ID','st.PARAMETERS'))
                .$sql->From(array('SCHEDULER_TASKS st'))
                .$sql->Where(array('st.TASK_ID' => $job_id));
        $res = $data->sql->query( $qry );
        $line = $data->sql->get_next($res);

        return array($line['SPOOLER_ID'],$line['JOB_NAME'],$line['PARAMETERS']);        
   }

   private function getOrderInfos($id) {   
       // Si on commence par !, c'est activé mais non historisé
       if (substr($id,0,1)=='!') {
           return $this->getJobChainInfos(substr($id,1));
       }
       
       $dhtmlx = $this->container->get('arii_core.dhtmlx');
       $data = $dhtmlx->Connector('data');

        // si l'id est une chaine, c'est dans le SCHEDULER_ORDERS
        if (strpos($id,'/')>0) {
            $Infos = explode('/',$id);
            $spooler = array_shift($Infos);
            $order = array_pop($Infos);
            $job_chain = implode('/',$Infos);
            return array($spooler,$order,$job_chain,'');
        }
        
        $sql = $this->container->get('arii_core.sql');
        $qry = $sql->Select(array('soh.JOB_CHAIN','soh.SPOOLER_ID','soh.ORDER_ID'))
                .$sql->From(array('SCHEDULER_ORDER_HISTORY soh'))
                .$sql->Where(array('soh.history_id' => $id));
        
        $res = $data->sql->query( $qry );
        $line = $data->sql->get_next($res);
        return array($line['SPOOLER_ID'],$line['ORDER_ID'],$line['JOB_CHAIN']);
   }

   private function getJobChainInfos($id) {   
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');

        $sql = $this->container->get('arii_core.sql');
        $qry = $sql->Select(array('ID','JOB_CHAIN','SPOOLER_ID'))
                .$sql->From(array('SCHEDULER_ORDERS'))
                .$sql->Where(array('ORDERING' => $id));

        $res = $data->sql->query( $qry );
        $line = $data->sql->get_next($res);
        return array($line['SPOOLER_ID'],$line['ID'],$line['JOB_CHAIN']);
   }

   private function getStateInfos($id) {   
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');

        // si l'id est une chaine, c'est dans le SCHEDULER_ORDERS_STEP_HISTORY
        if (strpos($id,'/')>0) {
            $Infos = explode('/',$id);
            $spooler = array_shift($Infos);
            $state = array_pop($Infos);
            $job_chain = implode('/',$Infos);
            return array($spooler,'',$job_chain,$state);
        }
        
        $sql = $this->container->get('arii_core.sql');
        $qry = $sql->Select(array('sosh.STATE','soh.JOB_CHAIN','soh.SPOOLER_ID','soh.ORDER_ID'))
                .$sql->From(array('SCHEDULER_ORDER_STEP_HISTORY sosh'))
                .$sql->LeftJoin('SCHEDULER_ORDER_HISTORY soh',array('sosh.HISTORY_ID','soh.HISTORY_ID'))
                .$sql->Where(array('sosh.TASK_ID'=>$id)); 
        $res = $data->sql->query( $qry );
        $line = $data->sql->get_next($res);
        return array($line['SPOOLER_ID'],$line['ORDER_ID'],$line['JOB_CHAIN'],$line['STATE']);
   }

   private function getConnectInfos($spooler) {
        $session = $this->container->get('arii_core.session');
	$enterprise_id = $session->getEnterpriseId(); // get the enterprise id from the session
		
       // si il n'existe pas d'entreprise
       if ($enterprise_id<0) {
           $dhtmlx = $this->container->get('arii_core.dhtmlx');
           $data = $dhtmlx->Connector('data');
           
           // on cherche le scheduler dans la base de données
           $sql = $this->container->get('arii_core.sql');
           $qry = $sql->Select(array('SCHEDULER_ID as SPOOLER_ID','HOSTNAME','TCP_PORT','IS_RUNNING','IS_PAUSED','START_TIME'))
                   .$sql->From(array('SCHEDULER_INSTANCES'))
                   .$sql->Where(array('SCHEDULER_ID' => $spooler ))
                   .$sql->OrderBy(array('ID desc'));

           $res = $data->sql->query( $qry );
           // pourrais etre en parametre si besoin
           $protocol = "http"; $path = "";
           while ($line = $data->sql->get_next($res)) {
               $scheduler = $line['SPOOLER_ID'];
               $hostname = $line['HOSTNAME'];
               $port = $line['TCP_PORT'];
               $start_time = $line['TCP_PORT'];
               if ($line['IS_RUNNING']!=1) {
                   // on tente un update ?
               }
               return array($protocol,$hostname,$port,$path);  
           }
           // sinon on regarde dans les parametres
           
           
           // return array('http','localhost','4444','/');
       }
       
       // sinon on retrouve le spooler dans la base de données
       $qry = "SELECT ac.interface as HOSTNAME,ac.port as TCP_PORT,ac.path,an.protocol 
        from ARII_SPOOLER asp
        LEFT JOIN ARII_CONNECTION ac
        ON asp.connection_id=ac.id
        LEFT JOIN ARII_NETWORK an
        ON ac.network_id=an.id
        where asp.name='".$spooler."' 
        and asp.site_id in (select site.id from ARII_SITE site where site.enterprise_id='$enterprise_id')"; // we should use ac.interface as HOSTNAME

        if ($line['protocol'] == "osjs")
        {
            $protocol = "http";
        } elseif($line['protocol'] == "sosjs")
        {
            $protocol = "https";
        }
        if ((!isset($scheduler)) or ($scheduler == "") or ($port=="")) {
            $errorlog = $this->container->get('arii_core.log');
            $errorlog->createLog("No scheduler or port found!",0,__FILE__,__LINE__,"Error at: ".__FILE__." function: ".__FUNCTION__." line: ".__LINE__." $spooler ?!",$_SERVER['REMOTE_ADDR']);
            print "$spooler ?!"; // we use the audit service here to record the errors during using the XML command
            exit();
        }
   }

   private function XmlWhy($result,$xml='') {
       foreach ($result as $k=>$v) {
           $img = ' image="bullet_green.png"'; $style = '';
           if (substr($k,-5)!='_attr') {
               if (substr($k,-4)=='.why') {
                    $img = ' image="eye.png"';
                    $k = substr($k,0,strpos($k,'.why'));
                    $style=' style="background-color: lightblue;"'; 
               }
               elseif ($k == 'obstacle') {
                    $k = '';
                    $img = ' image="exclamation.png"';
                    $style=' style="background-color: red;"'; 
               }
            $xml .= '<row id="'.$this->id_why++.'" open="1"'.$style.'>';
            $xml .= '<cell'.$img.'>'.$k.'</cell>';           
           }
            if (is_array($v)) {
                $xml .= $this->XmlWhy($v);
            }
            else {
                // si on a un obstacle, c'est sur les prochains
                $xml .= '<cell>'.$v.'</cell>';
            }
           if (substr($k,-5)!='_attr') {
            $xml .= '</row>';
           }
       }
       return $xml;
   }
   
   private function PurgeOrder($spooler,$order,$job_chain) {
        $dhtmlx = $this->container->get('arii_core.dhtmlx');
        $data = $dhtmlx->Connector('data');
        $qry = 'delete from SCHEDULER_ORDERS
            where JOB_CHAIN="'.$job_chain.'" and SPOOLER_ID="'.$spooler.'" and ID="'.$order.'"';
        try {
            $res = $data->sql->query( $qry );
            return "<font color='green'>Order purged</font>";
        } catch (Exception $exc) {
            return "<font color='red'>Database error</font>";
        }
   }
}
