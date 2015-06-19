<?php
// src/Sdz/BlogBundle/Service/SdzAntispam.php
 
namespace Arii\CoreBundle\Service;

class AriiDB
{
    protected $host;
    protected $port;
    protected $database;
    protected $user;
    protected $password;

    public function __construct($driver,$host,$port,$database,$user,$password)
    {      
        # Le serveur en cours est dans la variable de session OSJS_DB
        # Si ce n'est pas le cas, il faut la regenerer
        # La connexion passe par la session
        $this->driver       = $driver;
        $this->host         = $host;
        $this->port         = $port;
        $this->database     = $database;
        $this->user         = $user;
        $this->password     = $password;
        $this->dhtmlx_path  = '../vendor/dhtmlx/dhtmlxConnector/codebase';
    }

    public function getAriiDatabase( ) {
        $DB['id'] = 0;
        $DB['name'] = 'Arii';
        $DB['host'] = $this->host;
        $DB['port'] = $this->port;
        $DB['database'] = $this->database;
        $DB['login'] = $this->user;
        $DB['password'] = $this->password;
        $DB['driver'] = $this->driver;
        return $DB;
    }
	
    public function Config()
    {
        require_once '../vendor/dhtmlx/dhtmlxConnector/codebase/grid_config.php';
        return new \GridConfiguration();
    }
	
    /**
     * Utilisation du connecteur 
     *
     * @param string $connector
     */
    public function Connector( $type = 'data' )
    {        
        // require_once $this->dhtmlx_path.'/data_connector.php';
        switch ($this->driver) {
            case 'pdo_pgsql':
                require_once  $this->dhtmlx_path.'/db_postgre.php';
                $driver = "Postgre";
                break;
            default:
                require_once  $this->dhtmlx_path.'/db_mysqli.php';
                $driver = "MySQLi";
                break;
        }
        
        switch ($type) {
            case 'data':
                require_once $this->dhtmlx_path.'/data_connector.php';
                break;
            case 'grid':
                require_once $this->dhtmlx_path.'/grid_connector.php';
                break;
            case 'form':
                require_once $this->dhtmlx_path.'/form_connector.php';
                break;
            case 'select':
                require_once $this->dhtmlx_path.'/options_connector.php';
                break;
            case 'combo':
                require_once $this->dhtmlx_path.'/combo_connector.php';
                break;
            case 'tree':
                require_once $this->dhtmlx_path.'/tree_connector.php';
                break;
            case 'treegrid':
                require_once $this->dhtmlx_path.'/treegrid_connector.php';
                break;
            case 'treegridgroup':
                require_once $this->dhtmlx_path.'/treegridgroup_connector.php';
                break;
            case 'chart':
                require_once $this->dhtmlx_path.'/chart_connector.php';
                break;
            case 'scheduler':
                require_once $this->dhtmlx_path.'/scheduler_connector.php';
                break;
            case 'gantt':
                require_once $this->dhtmlx_path.'/gantt_connector.php';
                break;
            case 'dataview':
                require_once $this->dhtmlx_path.'/dataview_connector.php';
                break;
            case 'chart':
                require_once $this->dhtmlx_path.'/chart_connector.php';
            default:
                $this->get('session')->getFlashBag()->add('error', $type );
                return;
                break;
        }
        
        switch ($this->driver) {
            case 'pdo_mysql':
            case 'mysqli':
            case 'mysql':
                $conn= @mysqli_connect( $this->host, $this->user,  $this->password, $this->database );
                mysqli_query($conn, "SET NAMES 'utf8'");
                break;
            case 'pdo_pgsql':
                $conn= pg_connect( "host=".$this->host." port=".$this->port." dbname=".$this->database." user=".$this->user." password=".$this->password );
                break;
        }

        switch ($type) {
            case 'data':
                return new \Connector($conn,$driver);
                break;
            case 'grid':
                return new \GridConnector($conn,$driver);
                break;
            case 'form':
                return new \FormConnector($conn,$driver);
                break;
            case 'combo':
                return new \ComboConnector($conn,$driver);
                break;
            case 'select':
                return new \SelectOptionsConnector($conn,$driver);
                break;
            case 'tree':
                return new \TreeConnector($conn,$driver);
                break;
            case 'treegrid':
                return new \TreeGridConnector($conn,$driver);
                break;
            case 'treegridgroup':
                return new \TreeGridGroupConnector($conn,$driver);
                break;
            case 'chart':
                return new \ChartConnector($conn,$driver);
                break;
            case 'scheduler':
                return new \SchedulerConnector($conn,$driver);
                break;
            case 'gantt':
                return new \JSONGanttConnector($conn,$driver);
                break;
            case 'dataview':
                return new \DataViewConnector($conn,$driver);
                break;
            default:
                break;
        }
    }
}
