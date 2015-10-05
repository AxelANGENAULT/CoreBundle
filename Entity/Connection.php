<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Connection
 *
 * @ORM\Table(name="ARII_CONNECTION")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\ConnectionRepository")
 */
class Connection
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Enterprise")
    * @ORM\JoinColumn(nullable=true)
    */
    private $enterprise;

    /**
     * @ORM\OneToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     *      */
    private $proxy;

    /**
     * @var string
     *
     * @ORM\Column(name="env", type="string", length=16)
     */        
    private $env;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=100)
     */        
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="host", type="string", length=32, nullable=true)
     */
    private $host;

	/**
     * @var string
     *
     * @ORM\Column(name="interface", type="string", length=15, nullable=true)
     * for one host which is a machine, we maybe have different interfaces, several net cards
     */
    private $interface;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="port", type="integer")
     */
    private $port;

    /**
     * @var string
     *
     * @ORM\Column(name="protocol", type="string", length=32, nullable=true)
     */
    private $protocol;

    /**
     * @var string
     *
     * @ORM\Column(name="login", type="string", length=32, nullable=true)
     */
    private $login;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=32, nullable=true)
     */
    private $password;
    
    /**
     * @var string
     *
     * @ORM\Column(name="driver", type="string", length=32, nullable=true)
     */
    private $driver;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor", type="string", length=32, nullable=true)
     */
    private $vendor;

    /**
     * @var string
     *
     * @ORM\Column(name="db_name", type="string", length=32, nullable=true)
     */
    private $database;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Set title
     *
     * @param string $title
     * @return Connection
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Connection
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set ip_address
     *
     * @param string $ip_address
     * @return Connection
     */
    public function setIpAddress($ip_address)
    {
        $this->ip_address = $ip_address;
    
        return $this;
    }

     /**
     * Set protocol
     *
     * @param string $protocol
     * @return Connection
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    
        return $this;
    }

    /**
     * Get protocol
     *
     * @return string 
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

   /**
     * Get ip_address
     *
     * @return string 
     */
    public function getIpAddress()
    {
        return $this->ip_address;
    }

    /**
     * Set host
     *
     * @param string $host
     * @return Connection
     */
    public function setHost($host)
    {
        $this->host = $host;
    
        return $this;
    }

    /**
     * Get host
     *
     * @return string 
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set port
     *
     * @param integer $port
     * @return Connection
     */
    public function setPort($port)
    {
        $this->port = $port;
    
        return $this;
    }

    /**
     * Get port
     *
     * @return integer 
     */
    public function getPort()
    {
        return $this->port;
    }
    
    /**
     * Set login
     *
     * @param integer $login
     * @return Connection
     */
    public function setLogin($login)
    {
        $this->login = $login;
    
        return $this;
    }

    /**
     * Get login
     *
     * @return integer 
     */
    public function getLogin()
    {
        return $this->login;
    }
        
    /**
     * Set password
     *
     * @param integer $password
     * @return Connection
     */
    public function setPassword($password)
    {
        $this->password = $password;
    
        return $this;
    }

    /**
     * Get password
     *
     * @return string 
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set vendor
     *
     * @param integer $vendor
     * @return Connection
     */
    public function setVendor($vendor)
    {
        $this->vendor = $vendor;
    
        return $this;
    }

    /**
     * Get vendor
     *
     * @return string 
     */
    public function getVendor()
    {
        return $this->vendor;
    }

    /**
     * Set driver
     *
     * @param integer $driver
     * @return Connection
     */
    public function setDriver($driver)
    {
        $this->driver = $driver;
    
        return $this;
    }

    /**
     * Get driver
     *
     * @return string 
     */
    public function getDriver()
    {
        return $this->driver;
    }

    

    /**
     * Set ip_adress
     *
     * @param string $ipAdress
     * @return Connection
     */
    public function setIpAdress($ipAdress)
    {
        $this->ip_adress = $ipAdress;
    
        return $this;
    }

    /**
     * Get ip_adress
     *
     * @return string 
     */
    public function getIpAdress()
    {
        return $this->ip_adress;
    }

    /**
     * Set proxy
     *
     * @param \Arii\CoreBundle\Entity\Connection $proxy
     * @return Connection
     */
    public function setProxy(\Arii\CoreBundle\Entity\Connection $proxy = null)
    {
        $this->proxy = $proxy;
    
        return $this;
    }

    /**
     * Get proxy
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getProxy()
    {
        return $this->proxy;
    }

    /**
     * Set space
     *
     * @param string $space
     * @return Connection
     */
    public function setSpace($space)
    {
        $this->space = $space;
    
        return $this;
    }

    /**
     * Get space
     *
     * @return string 
     */
    public function getSpace()
    {
        return $this->space;
    }

    /**
     * Set database
     *
     * @param string $database
     * @return Connection
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    
        return $this;
    }

    /**
     * Get database
     *
     * @return string 
     */
    public function getDatabase()
    {
        return $this->database;
    }

    /**
     * Set path
     *
     * @param string $path
     * @return Connection
     */
    public function setPath($path)
    {
        $this->path = $path;
    
        return $this;
    }

    /**
     * Get path
     *
     * @return string 
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set ssl_port
     *
     * @param integer $sslPort
     * @return Connection
     */
    public function setSslPort($sslPort)
    {
        $this->ssl_port = $sslPort;
    
        return $this;
    }

    /**
     * Get ssl_port
     *
     * @return integer 
     */
    public function getSslPort()
    {
        return $this->ssl_port;
    }

    /**
     * Set network
     *
     * @param \Arii\CoreBundle\Entity\Network $network
     * @return Connection
     */
    public function setNetwork(\Arii\CoreBundle\Entity\Network $network = null)
    {
        $this->network = $network;
    
        return $this;
    }

    /**
     * Get network
     *
     * @return \Arii\CoreBundle\Entity\Network 
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * Set enterprise
     *
     * @param \Arii\CoreBundle\Entity\Enterprise $enterprise
     * @return Connection
     */
    public function setEnterprise(\Arii\CoreBundle\Entity\Enterprise $enterprise = null)
    {
        $this->enterprise = $enterprise;
    
        return $this;
    }

    /**
     * Get enterprise
     *
     * @return \Arii\CoreBundle\Entity\Enterprise 
     */
    public function getEnterprise()
    {
        return $this->enterprise;
    }

    /**
     * Set interface
     *
     * @param string $interface
     * @return Connection
     */
    public function setInterface($interface)
    {
        $this->interface = $interface;
    
        return $this;
    }

    /**
     * Get interface
     *
     * @return string 
     */
    public function getInterface()
    {
        return $this->interface;
    }
}