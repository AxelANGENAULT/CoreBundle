<?php
namespace Arii\CoreBundle\Service;

class AriiDHTMLX
{
    protected $id;
    protected $host;
    protected $port;
    protected $database;
    protected $user;
    protected $password;

    public function __construct(AriiSession $session)
    {      
        # Le serveur en cours est dans la variable de session OSJS_DB
        # Si ce n'est pas le cas, il faut la regenerer
        # La connexion passe par la session
        $this->session = $session;
        $db = $session->getDatabase();

        if (!$db) {
            // Pas de session en cours
            // on prend celle par defaut
            $db = $session->setDefaultDatabases();
        }
        $this->id           = $db['id'];
        $this->driver       = $db['driver'];
        $this->host         = $db['host'];
        $this->port         = $db['port'];
        $this->database     = $db['db_name'];
        $this->user         = $db['login'];
        $this->password     = $db['password'];
        $this->dhtmlx_path  = '../vendor/dhtmlx/dhtmlxConnector/codebase';
    }

    public function Id( )
    {   
        return $this->id;
    }
    /**
     * Utilisation du connecteur 
     *
     * @param string $connector
     */
    public function Connector( $type = 'data' )
    {   
        // Si aucune database n'est definie, on sort
        if (!($this->database)) {
            print "Database configuration ?!";
            exit();
        }
        switch ($this->driver) {
            case 'postgre':
            case 'postgres':
                require_once  $this->dhtmlx_path.'/db_postgre.php';
                $driver = "Postgre";
                break;
            case 'oci8':
            case 'oracle':
            case 'pdo_oci':
                require_once  $this->dhtmlx_path.'/db_oracle.php';
                $driver = "Oracle";
                break;
            case 'mysqli':
                require_once  $this->dhtmlx_path.'/db_mysqli.php';
                $driver = "MySQLi";
                break;
            default:
                require_once  $this->dhtmlx_path.'/db_pdo.php';
                $driver = "PDO";
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
            case 'options':
                require_once $this->dhtmlx_path.'/scheduler_connector.php';
                break;
            default:
                $this->get('session')->getFlashBag()->add('error', $type );
                return;
                break;
        }

        switch ($driver) {
            case 'Oracle':
//                print $this->user.' '.$this->password.' '.$this->host.':'.$this->port.'/'.$this->database ;
                $conn= oci_connect( $this->user,  $this->password, $this->host.':'.$this->port.'/'.$this->database  );
                break;
            case 'Postgre':
                $conn= pg_connect("host=".$this->host." port=".$this->port." dbname=".$this->database." user=".$this->user." password=".$this->password);
                break;
            case 'MySQLi':
            case 'pdo_mysql':
            case 'mysqli':
            case 'mysql':
                $conn= @mysqli_connect( $this->host, $this->user,  $this->password, $this->database );
                mysqli_query($conn, "SET NAMES 'utf8'");
                mysqli_query($conn, "SET CHARACTER SET 'utf8'");
                break;
            default:
                $dsn = substr($this->driver,4).':host='. $this->host.';dbname='.$this->database;
                // $dsn = 'oci:host='. $this->host.';dbname='.$this->database;
                try {
                    $conn = new \PDO( $dsn, $this->user, $this->password, array( \PDO::ATTR_PERSISTENT => true ));
                } catch ( Exception $e ) {
                    echo $e->getMessage();
                    die();
                }
                $conn->exec("SET CHARACTER SET utf8");
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
            case 'options':
                return new \OptionsConnector($conn,$driver);
                break;            
            default:
                break;
        }
    }
}
