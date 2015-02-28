<?php
// src/Sdz/BlogBundle/Service/AriiSQL.php
 
namespace Arii\CoreBundle\Service;
use Arii\CoreBundle\Service\AriiSession;

class AriiDate
{
    private $session;

    protected $TZLocal;
    protected $TZSpooler;
    protected $TZOffset;
    protected $DefaultOffset;
    protected $CurrentDate;
        
    public function __construct(AriiSession $session)
    {
        $this->session = $session;
        
        // date par defaut
        $this->CurrentDate = date('Y-m-d');
                
        $Site =  $session->getSite();
        $this->TZLocal = $Site['timezone'];
        // si le timezone est vide, on prend le timezone php
        if ($this->TZLocal=='') {
            $this->TZLocal = date_default_timezone_get ();
        }
        $target_offset = $this->getOffset($this->TZLocal);
        foreach ($session->getSpoolers() as $k=>$v) {
            $s = $v['name'];
            $t = $v['timezone'];
            $this->TZSpooler[$s] = $t;
            $this->TZOffset[$s] = $target_offset - $this->getOffset($t);
        }
        // pour les spoolers par defaut en 1.5
        $this->DefaultOffset = $this->getOffset($this->TZLocal);
    }

    private function getOffset( $tz ) {
        if ($tz=='') {
            // ATTENTION !!!!!!!!!!!!!!!!!!!!!!!!!!!
            $tz = 'GMT';
        }
        if ($tz == 'GMT') {
            $offset = 0;
        }
        else {
            $offset =  timezone_offset_get( new \DateTimeZone( $tz ), new \DateTime() );
        }
        return $offset;
    }

    public function getLocaltimes( $start, $end, $next='', $spooler='', $short=true ) {

        if (isset($this->TZOffset[$spooler])) 
            $offset = $this->TZOffset[$spooler];
        else 
            $offset = $this->DefaultOffset; // heure GMT par defaut
        $start_time = strtotime($start);
        if ($short) 
            $start_time_str = $this->ShortDate( date( 'Y-m-d H:i:s', $start_time + $offset) );
        else 
            $start_time_str = date( 'Y-m-d H:i:s', $start_time + $offset);            
        if ($end == '') {
            $end_time = '';
            $end_time_str = '';
        }
        else {
            $end_time = strtotime($end);
            $end_time_str = date( 'Y-m-d H:i:s', $end_time  + $offset );
            if ($short) {
                if (substr($end_time_str,0,10)==substr($end_time_str,0,10))
                    $end_time_str = substr($end_time_str,11);
            }
        }
        
        $y = substr($next,0,4);
        if (($next != '') and ($y!='2038')  and ($y!='0000') ) {          
           $next = date( 'Y-m-d H:i:s', strtotime($next) + $offset);
        }
        if ($short) {
            return array( $start_time_str, 
                      $end_time_str, 
                      $this->ShortDate( $next ) , $this->Duration($start_time,$end_time ) );
        }
        else {
            return array( $start_time_str, 
                      $end_time_str, 
                      $next, $this->Duration($start_time,$end_time ) );
        }    
    }
            
    public function ShortDate($date) {
      $y = substr($date,0,4); 
      if ($y=='2038')
                return '...';
      if ($y=='0000')
                return '';
      if ($y=='1970')
                return '';
      if (substr($date,0,10)==$this->CurrentDate)
                return substr($date,11);
        return $date;
    }

    public function ShortISODate($date,$spooler) {
        if (isset($this->TZOffset[$spooler])) 
            $offset = $this->TZOffset[$spooler];
        else 
            $offset = $this->DefaultOffset; // heure GMT par defaut
        $date = strtotime( substr($date,0,10).' '.substr($date,11,8 ));
        return $this->ShortDate( date( 'Y-m-d H:i:s', $date + $offset) );
    }

    public function FormatTime($d) {
       $str = '';
       $l = 0;
       if ($d>86400) {
           $n = (int) ($d/86400);
           $d %= 86400;
           $str .= ' '.$n.'d'; 
           $l++;           
       }
       if ($l>1) return $str;
       if ($d>3600) {
           $n = (int) ($d/3600);
           $d %= 3600;
           $str .= ' '.$n.'h';           
           $l++;
       }
       if ($l>1) return $str;
       if ($d>60) {
           $n = (int) ($d/60);
           $d %= 60;
           $str .= ' '.$n.'m';           
           $l++;
       }
       if ($l>1) return $str;
       
       if ($d>0) 
        $str .= ' '.round($d).'s';
       return $str;        
    }
    
    private function Duration($start,$end = '' ) {
       if ($end == '') {
           $end = time();
       }
       $d = $end - $start;
       return $this->FormatTime($d);
    }

    public function Time2Local($time,$spooler) {
        print "($time)";
        if (isset($this->TZOffset[$spooler])) 
            $offset = $this->TZOffset[$spooler];
        else 
            $offset = $this->DefaultOffset; // heure GMT par defaut
        return $this->ShortDate( date( 'Y-m-d H:i:s', $time + $offset) );
    }
    
    public function Date2Local($date,$spooler) {
        if (isset($this->TZOffset[$spooler])) 
            $offset = $this->TZOffset[$spooler];
        else 
            $offset = $this->DefaultOffset; // heure GMT par defaut
        $time = strtotime( substr($date,0,10).' '.substr($date,11,8 ));
        return date( 'Y-m-d H:i:s', $time + $offset);
    }
}
