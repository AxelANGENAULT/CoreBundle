<?php
// src/Arii/CoreBundle/Service/AriiMenu.php
// 
// Ce service gère la session utilisateur
namespace Arii\CoreBundle\Service;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Locale\Locale;
// use Symfony\Component\HttpFoundation\RedirectResponse;

class AriiSession
{
    protected $session;
    protected $db;
    protected $container;
    protected $security;

    protected $username;
    protected $enterprise_id;
    protected $user_id;
    protected $team_id;
    protected $arii_modules;
    protected $arii_databases;
    protected $arii_spoolers;

    public function __construct(Session $session, ContainerInterface $service_container, \Arii\CoreBundle\Service\AriiDB $db )
    {
        $this->db = $db;
        $this->session = $session;
        $this->container = $service_container;
        $this->arii_modules = $this->container->getParameter('arii_modules');
        $this->arii_databases = $this->container->getParameter('databases');
        $this->arii_spoolers = $this->container->getParameter('spoolers');

        // on teste si il est anonyme
        $securityContext = $this->container->get('security.context');
        // est ce qu'il y a un tocken ?
        if( !$securityContext->getToken() ) {
            return;
        }
        $this->security = $securityContext;

        if( !$securityContext->isGranted('IS_AUTHENTICATED_FULLY') 
                and !$securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED') ){
            // authenticated REMEMBERED, FULLY will imply REMEMBERED (NON anonymous)
//            return new RedirectResponse($this->generateUrl('arii_Home_index'));
            return;
        }

        $this->username = $securityContext->getToken()->getUsername();
        $this->roles = $securityContext->getToken()->getRoles();

        if ($this->get('enterprise')=='')
        {
            $user = $this->container->get('fos_user.user_manager')->findUserByUsername($this->username);
            if($user != null){
                $enterprise_obj  = $user->getEnterprise();
                
                if($enterprise_obj  != null){
                    $enterprise = $enterprise_obj->getEnterprise(); 
                    $this->set('enterprise',  $enterprise); 
                }
            }
        }
        
        // On recupere les informations de la base de données si l'utilisateur est incomplet
        if ($this->get('user_id')=='') {
            $this->setUserInfo();
        }

        // On doit avoir toutes les informations brutes pour les requetes suivantes
        if ($this->get('enterprise_id')=='') {
            $this->set('enterprise_id',-1);
        }
        if ($this->get('team_id')=='') {
            $this->set('team_id',-1);
        }
        
        $this->user_id = $this->get('user_id');
        $this->team_id = $this->get('team_id');
        $this->enterprise_id = $this->get('enterprise_id');

        // La securité liée à l'équipe
        // Les filtres de l'utilisateur 
        $TeamRights = $this->get( 'TeamRights');
      //  if (empty($TeamRights)) {
            $this->setTeamRights();
      //  }
        
        // Les filtres de l'utilisateur 
        $UserFilters = $this->get( 'UserFilters');
        if (empty($UserFilters)) {
            $this->setUserFilters();
        }

        // les layout
        if ($this->get('layout_sidebar')=='')
            $this->set('layout_sidebar','true');
        if ($this->get('layout_header')=='')
            $this->set('layout_header','true');
        
        // Les modules
        if ($this->get('arii_modules')=='') {
            $this->setModules();
        }

        // Les bases de données
        if (empty($this->getDatabase()) or empty($this->get( 'Databases'))) {            
            $this->setDatabases();            
        }
        // Les spoolers
        /* Inutile on reprends le parameters.yml
        $Spoolers = $this->get( 'Spoolers');
        if (empty($Spoolers)) {
            $this->setSpoolers();
        }
        */
        
        // Les sites 
        $Sites = $this->get( 'Sites');
        if (empty($Sites)) {
            $this->setSites();
        }

        // environnement technique
        if ($this->getRefDate() == '') {
            $Time = localtime(time(),true);
            $this->setRefTimestamp(time());
            $this->setRefDate( sprintf("%04d-%02d-%02d %02d:%02d:%02d", $Time['tm_year']+1900, $Time['tm_mon']+1, $Time['tm_mday'], $Time['tm_hour'], $Time['tm_min'], $Time['tm_sec']) );
        }

        if ($this->getRefPast() == '')
            $this->setRefPast( -1 );

        if ($this->getRefFuture() == '')
            $this->setRefFuture( 1 );   
        
        if ($this->getPast() == '')
            $this->setPast( $this->CalcDate( $this->get( 'ref_date'), $this->get( 'ref_past') ));   

        if ($this->getFuture() == '')
            $this->setFuture( $this->CalcDate( $this->get( 'ref_date'), $this->get( 'ref_future') )); 

        if ($this->get('refresh') == '') {
            $refresh=30;
            $this->set('refresh',$refresh);
        }
        
    }
    
    public function setUserInfo() {
        $qry = 'SELECT u.id,u.first_name,u.last_name,u.team_id,t.name as team,u.enterprise_id '
                . 'from ARII_USER u '
                . 'left join ARII_TEAM t '
                . 'on u.team_id=t.id '
                . 'where u.username="'.$this->username.'"';

        $data = $this->db->Connector('data');
        $res = $data->sql->query($qry);
        // une seule 
        if ($line = $data->sql->get_next($res)) {
            $this->set('user_id', $line['id']);
            $this->set('first_name',$line['first_name']);
            $this->set('last_name',$line['last_name']);

            // Cas ou l'entreprise n'est pas definie dans la base de donnees
            if ($line['enterprise_id']=='') {
                $this->set('enterprise_id',-1);
                // on rattache au rôle USER ou ADMIN
/*                if ($this->security->isGranted('ROLE_ADMIN')) {
                    $this->set('team','ADMIN');
                }
                else {
                    $this->set('team','USER');
                }
*/            }
            else {
                $this->set('enterprise_id',$line['enterprise_id']);
            }
            $this->set('team_id',$line['team_id']);
            $this->set('team',$line['team']);
        }
        else {
            // On a un souci
        }
    }

    public function setModules(){
        $Modules = array();
        foreach (explode(',',$this->arii_modules) as $m) {
            array_push($Modules,$m);
        }
        
        // Si l'entreprise existe
        if ($this->enterprise_id>-1) {
            $qry = 'SELECT modules from ARII_ENTERPRISE where id='.$this->enterprise_id;

            $data = $this->db->Connector('data');
            $res = $data->sql->query($qry);
            while ($line = $data->sql->get_next($res)) {
                foreach (explode(',',$line['modules']) as $m) {
                    if (($m!='') and (!in_array($m,$Modules))) 
                        array_push($Modules,$m);
                }
            }
        }
        $this->set('arii_modules',implode(',',$Modules));
    }
    
    public function getModules(){
        return $this->get('arii_modules');
    }

    public function setDatabases()
    {
        // Cas ou la base est donnée en parametre 
        if ($this->enterprise_id==-1) {
            return $this->setDefaultDatabases();
        }
        
        // Si on est super admin, on se limite a arii
        $qry = 'SELECT DISTINCT ar.id,ar.name,ac.id as db,ac.host,ac.port,ac.login,ac.password,ac.driver,ac.vendor,ac.db_name
                    FROM ARII_REPOSITORY ar
                    LEFT JOIN ARII_CONNECTION ac
                    ON ar.db_id=ac.id ';
        if (!$this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $qry .= 'WHERE ar.enterprise_id='.$this->enterprise_id;
        }
        else {
            $qry .= 'WHERE ar.name="Ari\'i"';
        }
        $qry .= ' ORDER BY ar.name';

        $Databases = array();        
        $data = $this->db->Connector('data');
        if ($data) {
            $res = $data->sql->query($qry);
            while ($line = $data->sql->get_next($res))
            {
                array_push($Databases,$line);
            }
        }
        
        if (!empty($Databases)) {
            $this->set( 'Databases', $Databases );
        }
        else { // on utilise la base Arii (cela ne devrait jamais arriver)
            return $this->setDefaultDatabases();
        }            
        $current = current($Databases);
        $this->setDatabase( $current );
        return $current;
    }
    
    public function setDefaultDatabases() {
        // Nouveauté 1.6, on inègre les bases de données du fichier parameters.yml
        print "change"; exit();
        if (!empty($this->arii_databases)) {
            $Default = array();
            foreach ($this->arii_databases as $db) {
                $DB = $db;
                $DB['id'] = -1;
                $DB['db'] = -1;
                // compatibilité avec les versions precedentes
                $DB['login'] = $db['user'];
                $DB['db_name'] = $db['dbname'];
                array_push($Default,$DB);
            }
            $this->set( 'Databases', $Default);
            $this->setDatabase( $Default[0] );
            return $Default;
        }
        
        // devrait être un tableau
        $Default['id'] = -1;
        $Default['db'] = -1;
        $Default['name'] = $this->container->getParameter('repository_name');
        $Default['db_name'] = $this->container->getParameter('repository_dbname');
        $Default['host'] =  $this->container->getParameter('repository_host');
        $Default['port'] =  $this->container->getParameter('repository_port');
        $Default['login'] = $this->container->getParameter('repository_user');
        $Default['password'] = $this->container->getParameter('repository_password');
        $Default['driver'] = $this->container->getParameter('repository_driver');
        $Default['vendor'] = $this->container->getParameter('repository_driver');
        $this->set( 'Databases', array($Default));
        $this->setDatabase( $Default );
        return $Default;
    }
    
    // Donne la liste des base de données
    public function getDatabases()
    {
        return $this->get( 'Databases' );
    }
    
    public function setUserFilters()
    {
        // les filtres sont ceux de l'utilisateur et de son equipe
        $qry = "SELECT id,name,description "
                . "from ARII_USER_FILTER "
                . "where user_id=".$this->user_id
                . " order by name";

        $data = $this->db->Connector('data');
        $res = $data->sql->query($qry);
        $Filters = array();
        while ($line = $data->sql->get_next($res))
        {
            array_push($Filters,$line);
        }
        $this->set( 'UserFilters', $Filters );
        if (!empty($Filters)) {
            $this->setUserFilterById();
        }
    }
    
    public function GetUserFilters()
    { 
        return $this->get('Filters');
    }

    public function setUserFilter($current)
    {
        $this->set('user_filter',$current);
    }   

    public function getUserFilter()
    { 
        return $this->get('user_filter');
    }

    public function setUserFilterById($id=-1)
    {
        if ($id>=0) {
            $data = $this->db->Connector('data');        
            $qry = "SELECT id, name, spooler, job, job_chain, order_id, status  
                    FROM ARII_USER_FILTER
                    WHERE id=".$id;
            $res = $data->sql->query($qry);
            $this->set('user_filter',$data->sql->get_next($res));
        }
        else {
            $Filter['name'] = 'All';
            $Filter['spooler'] = '%';
            $Filter['job'] = '%';
            $Filter['job_chain'] = '%';   
            $Filter['order_id'] = '%';   
            $Filter['status'] = '%';
            $this->set('user_filter',$Filter);
        }
    }   

    public function setTeamRights()
    {
        // les filtres sont ceux de l'utilisateur et de son equipe
        $qry = "SELECT path,job,job_chain,order_id,process_class,spooler,repository,R,W,X "
                . "from ARII_TEAM_RIGHT "
                . "where team_id=".$this->team_id;

        $data = $this->db->Connector('data');
        $res = $data->sql->query($qry);
        $Rights = array();
        while ($line = $data->sql->get_next($res))
        {
            array_push($Rights,$line);
        }
        $this->set( 'TeamRights', $Rights );
    }

    public function getTeamRights()
    { 
        return $this->get('TeamRights');
    }

    public function setSpoolers()
    {
        // La notion de spooler est à revoir en fonction des modules
        if (!empty($this->arii_spoolers)) {
            $Spoolers = $this->arii_spoolers;
            $this->set( 'Spoolers', $Spoolers );            
            $this->setSpooler( $Spoolers[0] );
            return $Spoolers;
        }
        
        $db = $this->getDatabase();
        if (empty($db)) {
            // on doit logguer une erreur, ce n'est pas normal !
            return;
        }
        
        // On recupere les permissions en cours
/*        $filter_where = '';
        $filters = $this->getFilters();
        if (!empty($filters)) {
            foreach ($filters as $f) {
                $spooler = str_replace('*','%',$f['spooler']);
                if ($spooler=='%') { 
                    $filter_where = '';
                    continue;
                }
                $filter_where .= ' and (name like "'.$spooler.'")';
            }
        }
*/
        
        $qry = "SELECT id,name,scheduler as spooler_id,timezone from ARII_SPOOLER where db_id=".$db['id']." order by scheduler";

        $data = $this->db->Connector('data');
        $res = $data->sql->query($qry);
        $Spoolers = array();
        // on teste si le spooler courant fait parti de la liste
        while ($line = $data->sql->get_next($res)) {
            array_push($Spoolers,$line);
        }
        $this->set( 'Spoolers', $Spoolers );
        $this->setSpooler( current($Spoolers) );
    }

    public function GetSpoolers()
    { 
        return $this->get('Spoolers');
    }
    
    public function getSpooler()
    { 
        return $this->get('spooler');
    }

    public function setSpoolerById($id)
    {
        // cas particulier du -1
        if ($id<0) {
           $this->set('spooler',array('id'=>-1,'name'=>'spooler.all','timezone'=>'GMT')); 
           return;
        }
        $data = $this->db->Connector('data');        
        $qry = "SELECT id,scheduler as name,timezone  
                FROM ARII_SPOOLER
                WHERE id=".$id;
        $res = $data->sql->query($qry);
        $this->setSpooler($data->sql->get_next($res));
     }   

    public function setSpooler($spooler)
    { 
       $this->set('spooler',$spooler);
    }

    public function GetSites()
    { 
        return $this->get('Sites');
    }

    public function setSites()
    {
        // Cas ou la base est donnée en parametre 
        if ($this->enterprise_id==-1) {
            return $this->setDefaultSites();
        }
        
        $qry = "SELECT asi.id,asi.name,asi.country_code as iso,asi.timezone from ARII_SITE asi ";
        if (!$this->container->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            $qry .= ' WHERE asi.enterprise_id='.$this->enterprise_id;
        }
        $qry .= " order by asi.name";

        $data = $this->db->Connector('data');
        $res = $data->sql->query($qry);
        $Sites = array();
        $default = strtoupper(substr($this->container->getParameter('locale'),-2));
        $d['id'] = -1;
        $d['name'] =  'local';
        $d['iso'] =  $default;
        $d['timezone'] = 'GMT';
        $def = 0;
        while ($line = $data->sql->get_next($res))
        {
            if ($line['iso']==$default) {
                $d = $line;
                $def = 1;
            }
            array_push($Sites,$line);
        }
        $this->setSite($d);            
        if (empty($Sites)) {
            $this->set( 'Sites',  array( $d ));
        }
        else {
            $this->set( 'Sites', $Sites );
            if ($def==0) {
                $this->setSite( current($Sites) );
            }
        }
    }
    
    public function setDefaultSites() {
        $Default['id'] = -1;
        $Default['name'] = $this->container->getParameter('site_name');
        $Default['iso'] = strtoupper(substr($this->container->getParameter('locale'),-2));
        $Default['timezone'] =  ini_get('date.timezone'); // $this->container->getParameter('site_timezone');
        $this->set( 'Sites', array($Default) );
        $this->setSite( $Default ); 
        return;
    }
    
    public function getSite()
    { 
        return $this->get('site');
    }

    public function setSite($current)
    { 
        $this->set('site', $current );
    }

    public function setSiteById($id)
    {
        $data = $this->db->Connector('data');        
        $qry = "SELECT *  
                FROM ARII_SITE
                WHERE id=".$id;
        $res = $data->sql->query($qry);
        $this->set('site',$data->sql->get_next($res));
    }   

    public function get($id)
    {
        return $this->session->get($id);
    }
    
    public function set($id,$value)
    {
        $this->session->set($id, $value);
    }

    public function getUserId()
    {
        return $this->get('user_id');
    }
    
    public function getEnterprise()
    {
        return $this->get('enterprise');
    }

    public function getEnterpriseId()
    {
        return $this->get('enterprise_id');
    }

    public function setEnterprise($enterprise)
    {
        $this->set('enterprise',$enterprise);
    }
    
    public function getDatabase() {
       return $this->get('database');
    }   
    
    public function setDatabase($current)
    {
        $this->set('database',$current);
    }
    
    public function setDatabaseById($id)
    {
        $data = $this->db->Connector('data');        
        $qry = "SELECT ac.id,ac.title as name,ac.host,ac.port,ac.description,ac.login,ac.password,ac.driver,ac.vendor,ac.db_name
                FROM ARII_CONNECTION ac
                WHERE ac.id=".$id;
        $res = $data->sql->query($qry);
        $this->setDatabase($data->sql->get_next($res));
    }   
     
    public function setRefresh($refresh)
    {
       $this->set('refresh', $refresh );
    }   
    
    public function getUsername()
    {
       return $this->username;
    }

    public function getRefresh()
    {
       return $this->get( 'refresh' );
    }    

 /* Date de reference */
    public function getRefDate()
    {
        return $this->get( 'ref_date' );
    }
    
    public function setRefDate($date)
    {
        $this->set( 'ref_date', $date );
        // on recalcule les bornes inferieurs et superieures
        $this->setPast($this->CalcDate( $date, $this->get('ref_past') ));
        $this->setFuture($this->CalcDate( $date, $this->get( 'ref_future') ));
    }

    public function getRefTimestamp()
    {
        return $this->get( 'ref_timestamp' );
    }

    public function setRefTimestamp($timestamp)
    {
        $this->set( 'ref_timestamp', $timestamp );
    }

 /* Passé */
    public function getRefPast()
    {
       return $this->get( 'ref_past' );
        
    }    
    public function setRefPast($hours)
    {
        $this->set('ref_past', $hours);
        $this->setPast( $this->CalcDate( $this->get( 'ref_date'), $hours ));
    }
    
 /* Futur */
    public function getRefFuture()
    {
        return $this->get('ref_future');
    }    
    
    public function setRefFuture($hours)
    {
        $this->set( 'ref_future', $hours );
        $this->setFuture( $this->CalcDate( $this->get('ref_date'), $hours ));
    }
    
    public function getPast()
    {
        return $this->get('past');
    }   

    public function setPast($past)
    {
        $this->set('past', $past );
    }   

    public function getFuture()
    {
        return $this->get('future');
    }   

    public function setFuture($future)
    {
       $this->set('future', $future);
    }   

    private function CalcDate($date,$days) 
    {
        $year = substr($date,0,4);
        $month = substr($date,5,2);
        $day = substr($date,8,2);
        $hour = substr($date,11,2);
        $min = substr($date,14,2);
        $sec = substr($date,17,2);
        $tm = localtime(mktime($hour, $min, $sec, $month, $day, $year )+($days*86400),true);
        return sprintf("%04d-%02d-%02d %02d:%02d:%02d",$tm['tm_year']+1900,$tm['tm_mon']+1,$tm['tm_mday'],$tm['tm_hour'],$tm['tm_min'],$tm['tm_sec']);
    }

    private function CalcDateHours($date,$hours) 
    {
        $year = substr($date,0,4);
        $month = substr($date,5,2);
        $day = substr($date,8,2);
        $hour = substr($date,11,2);
        $min = substr($date,14,2);
        $sec = substr($date,17,2);
        $tm = localtime(mktime($hour, $min, $sec, $month, $day, $year )+($hours*3600),true);
        return sprintf("%04d-%02d-%02d %02d:%02d:%02d",$tm['tm_year']+1900,$tm['tm_mon']+1,$tm['tm_mday'],$tm['tm_hour'],$tm['tm_min'],$tm['tm_sec']);
    }
}
