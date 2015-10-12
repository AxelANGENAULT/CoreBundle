<?php

namespace Arii\NetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BatchInstaller
 *
 * @ORM\Table(name="NET_INSTALLATION")
 * @ORM\Entity(repositoryClass="Arii\CoreBundle\Entity\BatchInstallerRepository")
 */
class Installation
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="licence_options", type="string", length=20)
     */
    private $licence_options;

    /**
     * @var string
     *
     * @ORM\Column(name="licence", type="string", length=64)
     */
    private $licence;

    /**
     * @var string
     *
     * @ORM\Column(name="os_target", type="string", length=24)
     */
    private $os_target;

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
     * @var integer
     *
     * @ORM\Column(name="default_port", type="smallint")
     */
    private $default_port;

    /**
     * @var string
     *
     * @ORM\Column(name="allowed_hosts", type="string", length=255)
     */
    private $allowed_hosts;

    /**
     * @var string
     *
     * @ORM\Column(name="job_events", type="string", length=3)
     */
    private $job_events;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_on_error", type="string", length=3)
     */
    private $mail_on_error;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_on_warning", type="string", length=3)
     */
    private $mail_on_warning;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_on_success", type="string", length=3)
     */
    private $mail_on_success;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_from", type="string", length=255)
     */
    private $mail_from;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_to", type="string", length=255)
     */
    private $mail_to;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_cc", type="string", length=255)
     */
    private $mail_cc;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_bcc", type="string", length=255)
     */
    private $mail_bcc;



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
     * Set name
     *
     * @param string $name
     * @return BatchInstaller
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
     * Set title
     *
     * @param string $title
     * @return BatchInstaller
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
     * @return BatchInstaller
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
     * Set licence_options
     *
     * @param string $licenceOptions
     * @return BatchInstaller
     */
    public function setLicenceOptions($licenceOptions)
    {
        $this->licence_options = $licenceOptions;
    
        return $this;
    }

    /**
     * Get licence_options
     *
     * @return string 
     */
    public function getLicenceOptions()
    {
        return $this->licence_options;
    }

    /**
     * Set licence
     *
     * @param string $licence
     * @return BatchInstaller
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
     * Set os_target
     *
     * @param string $osTarget
     * @return BatchInstaller
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
     * Set install_path
     *
     * @param string $installPath
     * @return BatchInstaller
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
     * @return BatchInstaller
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
     * Set default_port
     *
     * @param integer $defaultPort
     * @return BatchInstaller
     */
    public function setDefaultPort($defaultPort)
    {
        $this->default_port = $defaultPort;
    
        return $this;
    }

    /**
     * Get default_port
     *
     * @return integer 
     */
    public function getDefaultPort()
    {
        return $this->default_port;
    }

    /**
     * Set allowed_hosts
     *
     * @param string $allowedHosts
     * @return BatchInstaller
     */
    public function setAllowedHosts($allowedHosts)
    {
        $this->allowed_hosts = $allowedHosts;
    
        return $this;
    }

    /**
     * Get allowed_hosts
     *
     * @return string 
     */
    public function getAllowedHosts()
    {
        return $this->allowed_hosts;
    }

    /**
     * Set job_events
     *
     * @param string $jobEvents
     * @return BatchInstaller
     */
    public function setJobEvents($jobEvents)
    {
        $this->job_events = $jobEvents;
    
        return $this;
    }

    /**
     * Get job_events
     *
     * @return string 
     */
    public function getJobEvents()
    {
        return $this->job_events;
    }

    /**
     * Set mail_on_error
     *
     * @param string $mailOnError
     * @return BatchInstaller
     */
    public function setMailOnError($mailOnError)
    {
        $this->mail_on_error = $mailOnError;
    
        return $this;
    }

    /**
     * Get mail_on_error
     *
     * @return string 
     */
    public function getMailOnError()
    {
        return $this->mail_on_error;
    }

    /**
     * Set mail_on_warning
     *
     * @param string $mailOnWarning
     * @return BatchInstaller
     */
    public function setMailOnWarning($mailOnWarning)
    {
        $this->mail_on_warning = $mailOnWarning;
    
        return $this;
    }

    /**
     * Get mail_on_warning
     *
     * @return string 
     */
    public function getMailOnWarning()
    {
        return $this->mail_on_warning;
    }

    /**
     * Set mail_on_success
     *
     * @param string $mailOnSuccess
     * @return BatchInstaller
     */
    public function setMailOnSuccess($mailOnSuccess)
    {
        $this->mail_on_success = $mailOnSuccess;
    
        return $this;
    }

    /**
     * Get mail_on_success
     *
     * @return string 
     */
    public function getMailOnSuccess()
    {
        return $this->mail_on_success;
    }

    /**
     * Set mail_from
     *
     * @param string $mailFrom
     * @return BatchInstaller
     */
    public function setMailFrom($mailFrom)
    {
        $this->mail_from = $mailFrom;
    
        return $this;
    }

    /**
     * Get mail_from
     *
     * @return string 
     */
    public function getMailFrom()
    {
        return $this->mail_from;
    }

    /**
     * Set mail_to
     *
     * @param string $mailTo
     * @return BatchInstaller
     */
    public function setMailTo($mailTo)
    {
        $this->mail_to = $mailTo;
    
        return $this;
    }

    /**
     * Get mail_to
     *
     * @return string 
     */
    public function getMailTo()
    {
        return $this->mail_to;
    }

    /**
     * Set mail_cc
     *
     * @param string $mailCc
     * @return BatchInstaller
     */
    public function setMailCc($mailCc)
    {
        $this->mail_cc = $mailCc;
    
        return $this;
    }

    /**
     * Get mail_cc
     *
     * @return string 
     */
    public function getMailCc()
    {
        return $this->mail_cc;
    }

    /**
     * Set mail_bcc
     *
     * @param string $mailBcc
     * @return BatchInstaller
     */
    public function setMailBcc($mailBcc)
    {
        $this->mail_bcc = $mailBcc;
    
        return $this;
    }

    /**
     * Get mail_bcc
     *
     * @return string 
     */
    public function getMailBcc()
    {
        return $this->mail_bcc;
    }
}