<?php

namespace Arii\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Spooler
 *
 * @ORM\Table(name="ARII_SPOOLER")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\SpoolerRepository")
 */
class Spooler
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="scheduler", type="string", length=100)
     */
    private $scheduler;

    /**
     * @var string
     *
     * @ORM\Column(name="member", type="string", length=100, nullable=true)
     */
    private $member;

    /**
     * @var string
     *
     * @ORM\Column(name="cluster_options", type="string", length=64)
     */
    private $cluster_options;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $connection;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $transfer;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $db;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $smtp;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $http;

   /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $https;

    
   /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Connection")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $osjs;

    
    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Spooler")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $supervisor;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Spooler")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $primary;

    /**
     * @ORM\ManyToOne(targetEntity="Arii\CoreBundle\Entity\Site")
     * @ORM\JoinColumn(nullable=true)
     *      
     */
    private $site;

    /**
     * @var string
     *
     * @ORM\Column(name="licence", type="string", length=30)
     */
    private $licence;

    /**
     * @var string
     *
     * @ORM\Column(name="install_path", type="string", length=255)
     */
    private $install_path;

    /**
     * @var string
     *
     * @ORM\Column(name="user_path", type="string", length=255)
     */
    private $user_path;

    /**
     * @var boolean
     *
     * @ORM\Column(name="events", type="boolean")
     */
    private $events;

    /**
     * @var string
     *
     * @ORM\Column(name="os_target", type="string", length=12)
     */
    private $os_target;

    /**
     * @var string
     *
     * @ORM\Column(name="version", type="string", length=12 )
     */
    private $version;

    /**
     * @var date
     *
     * @ORM\Column(name="install_date", type="date" )
     */
    private $install_date;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=12, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="status_date", type="date" )
     */
    private $status_date;

    /**
     * @var string
     *
     * @ORM\Column(name="timezone",  type="string", length=20 )
     */
    private $timezone;

   /**
     * @var string
     *
     * @ORM\Column(name="remote",  type="boolean", nullable=true )
     */
    private $remote;

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
     * Set hostname
     *
     * @param string $hostname
     * @return Spooler
     */
    public function setHostname($hostname)
    {
        $this->hostname = $hostname;
    
        return $this;
    }

    /**
     * Get hostname
     *
     * @return string 
     */
    public function getHostname()
    {
        return $this->hostname;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Spooler
     */
    public function setIp($ip)
    {
        $this->ip = $ip;
    
        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set port
     *
     * @param integer $port
     * @return Spooler
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
     * Set type
     *
     * @param string $type
     * @return Spooler
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set db_connection
     *
     * @param integer $dbConnection
     * @return Spooler
     */
    public function setDbConnection($dbConnection)
    {
        $this->db_connection = $dbConnection;
    
        return $this;
    }

    /**
     * Get db_connection
     *
     * @return integer 
     */
    public function getDbConnection()
    {
        return $this->db_connection;
    }

    /**
     * Set smtp_connection
     *
     * @param integer $smtpConnection
     * @return Spooler
     */
    public function setSmtpConnection($smtpConnection)
    {
        $this->smtp_connection = $smtpConnection;
    
        return $this;
    }

    /**
     * Get smtp_connection
     *
     * @return integer 
     */
    public function getSmtpConnection()
    {
        return $this->smtp_connection;
    }

    /**
     * Set licence
     *
     * @param string $licence
     * @return Spooler
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;
    
        return $this;
    }

    /**
     * Get licence
     *
     * @return string 
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set install_path
     *
     * @param string $installPath
     * @return Spooler
     */
    public function setInstallPath($installPath)
    {
        $this->install_path = $installPath;
    
        return $this;
    }

    /**
     * Get install_path
     *
     * @return string 
     */
    public function getInstallPath()
    {
        return $this->install_path;
    }

    /**
     * Set user_path
     *
     * @param string $userPath
     * @return Spooler
     */
    public function setUserPath($userPath)
    {
        $this->user_path = $userPath;
    
        return $this;
    }

    /**
     * Get user_path
     *
     * @return string 
     */
    public function getUserPath()
    {
        return $this->user_path;
    }

    /**
     * Set events
     *
     * @param boolean $events
     * @return Spooler
     */
    public function setEvents($events)
    {
        $this->events = $events;
    
        return $this;
    }

    /**
     * Get events
     *
     * @return boolean 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set os_target
     *
     * @param string $osTarget
     * @return Spooler
     */
    public function setOsTarget($osTarget)
    {
        $this->os_target = $osTarget;
    
        return $this;
    }

    /**
     * Get os_target
     *
     * @return string 
     */
    public function getOsTarget()
    {
        return $this->os_target;
    }

    /**
     * Set connection
     *
     * @param \Arii\CoreBundle\Entity\Connection $connection
     * @return Spooler
     */
    public function setConnection(\Arii\CoreBundle\Entity\Connection $connection = null)
    {
        $this->connection = $connection;
    
        return $this;
    }

    /**
     * Get connection
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set transfer
     *
     * @param \Arii\CoreBundle\Entity\Connection $transfer
     * @return Spooler
     */
    public function setTransfer(\Arii\CoreBundle\Entity\Connection $transfer = null)
    {
        $this->transfer = $transfer;
    
        return $this;
    }

    /**
     * Get transfer
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getTransfer()
    {
        return $this->transfer;
    }

    /**
     * Set db
     *
     * @param \Arii\CoreBundle\Entity\Connection $db
     * @return Spooler
     */
    public function setDb(\Arii\CoreBundle\Entity\Connection $db = null)
    {
        $this->db = $db;
    
        return $this;
    }

    /**
     * Get db
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getDb()
    {
        return $this->db;
    }

    /**
     * Set smtp
     *
     * @param \Arii\CoreBundle\Entity\Connection $smtp
     * @return Spooler
     */
    public function setSmtp(\Arii\CoreBundle\Entity\Connection $smtp = null)
    {
        $this->smtp = $smtp;
    
        return $this;
    }

    /**
     * Get smtp
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getSmtp()
    {
        return $this->smtp;
    }

    /**
     * Set supervisor
     *
     * @param \Arii\CoreBundle\Entity\Spooler $supervisor
     * @return Spooler
     */
    public function setSupervisor(\Arii\CoreBundle\Entity\Spooler $supervisor = null)
    {
        $this->supervisor = $supervisor;
    
        return $this;
    }

 
    /**
     * Get supervisor
     *
     * @return \Arii\CoreBundle\Entity\Spooler 
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return Spooler
     */
    public function setVersion($version)
    {
        $this->version = $version;
    
        return $this;
    }

    /**
     * Get version
     *
     * @return string 
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set install_date
     *
     * @param \DateTime $installDate
     * @return Spooler
     */
    public function setInstallDate($installDate)
    {
        $this->install_date = $installDate;
    
        return $this;
    }

    /**
     * Get install_date
     *
     * @return \DateTime 
     */
    public function getInstallDate()
    {
        return $this->install_date;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Spooler
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status_date
     *
     * @param \DateTime $statusDate
     * @return Spooler
     */
    public function setStatusDate($statusDate)
    {
        $this->status_date = $statusDate;
    
        return $this;
    }

    /**
     * Get status_date
     *
     * @return \DateTime 
     */
    public function getStatusDate()
    {
        return $this->status_date;
    }

    /**
     * Set cluster_options
     *
     * @param string $clusterOptions
     * @return Spooler
     */
    public function setClusterOptions($clusterOptions)
    {
        $this->cluster_options = $clusterOptions;
    
        return $this;
    }

    /**
     * Get cluster_options
     *
     * @return string 
     */
    public function getClusterOptions()
    {
        return $this->cluster_options;
    }

    /**
     * Set http
     *
     * @param \Arii\CoreBundle\Entity\Connection $http
     * @return Spooler
     */
    public function setHttp(\Arii\CoreBundle\Entity\Connection $http = null)
    {
        $this->http = $http;
    
        return $this;
    }

    /**
     * Get http
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getHttp()
    {
        return $this->http;
    }

    /**
     * Set site
     *
     * @param \Arii\CoreBundle\Entity\Site $site
     * @return Spooler
     */
    public function setSite(\Arii\CoreBundle\Entity\Site $site = null)
    {
        $this->site = $site;
    
        return $this;
    }

    /**
     * Get site
     *
     * @return \Arii\CoreBundle\Entity\Site 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set https
     *
     * @param \Arii\CoreBundle\Entity\Connection $https
     * @return Spooler
     */
    public function setHttps(\Arii\CoreBundle\Entity\Connection $https = null)
    {
        $this->https = $https;
    
        return $this;
    }

    /**
     * Get https
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getHttps()
    {
        return $this->https;
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     * @return Spooler
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    
        return $this;
    }

    /**
     * Get timezone
     *
     * @return string 
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * Set scheduler
     *
     * @param string $scheduler
     * @return Spooler
     */
    public function setScheduler($scheduler)
    {
        $this->scheduler = $scheduler;
    
        return $this;
    }

    /**
     * Get scheduler
     *
     * @return string 
     */
    public function getScheduler()
    {
        return $this->scheduler;
    }

    /**
     * Set member
     *
     * @param string $member
     * @return Spooler
     */
    public function setMember($member)
    {
        $this->member = $member;
    
        return $this;
    }

    /**
     * Get member
     *
     * @return string 
     */
    public function getMember()
    {
        return $this->member;
    }

    /**
     * Set primary
     *
     * @param \Arii\CoreBundle\Entity\Spooler $primary
     * @return Spooler
     */
    public function setPrimary(\Arii\CoreBundle\Entity\Spooler $primary = null)
    {
        $this->primary = $primary;
    
        return $this;
    }

    /**
     * Get primary
     *
     * @return \Arii\CoreBundle\Entity\Spooler 
     */
    public function getPrimary()
    {
        return $this->primary;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Spooler
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Spooler
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
     * Set osjs
     *
     * @param \Arii\CoreBundle\Entity\Connection $osjs
     * @return Spooler
     */
    public function setOsjs(\Arii\CoreBundle\Entity\Connection $osjs = null)
    {
        $this->osjs = $osjs;
    
        return $this;
    }

    /**
     * Get osjs
     *
     * @return \Arii\CoreBundle\Entity\Connection 
     */
    public function getOsjs()
    {
        return $this->osjs;
    }

    /**
     * Set remote
     *
     * @param boolean $remote
     * @return Spooler
     */
    public function setRemote($remote)
    {
        $this->remote = $remote;
    
        return $this;
    }

    /**
     * Get remote
     *
     * @return boolean 
     */
    public function getRemote()
    {
        return $this->remote;
    }
}