<?php
// src/Sdz/BlogBundle/Service/AriiSQL.php
 
namespace Arii\CoreBundle\Service;
use Arii\CoreBundle\Service\AriiSession;
use Symfony\Component\Translation\TranslatorInterface;

class AriiDate
{
    private $session;

    protected $TZLocal;
    protected $TZSpooler;
    protected $TZOffset;
    protected $DefaultOffset;
    protected $CurrentDate;
    protected $translator;

    public function __construct(AriiSession $session, TranslatorInterface $translator)
    {
        $this->session = $session;
        
        // date par defaut
        $this->CurrentDate = date('Y-m-d');
        $this->translator = $translator;
        
        $Site =  $session->getSite();
        $this->TZLocal = $Site['timezone'];

        // si le timezone est vide, on prend le timezone php
        if ($this->TZLocal=='') {
            $this->TZLocal = date_default_timezone_get ();
        }
        $target_offset = $this->getOffset($this->TZLocal);
        /* Devient inutile, les ordonnanceurs stockent les dates en GMT
        foreach ($session->getSpoolers() as $k=>$v) {     
            $s = $v['name'];
            $t = $v['timezone'];
            $this->TZSpooler[$s] = $t;
            $this->TZOffset[$s] = $target_offset - $this->getOffset($t);
        }
         */
        // pour les spoolers par defaut en 1.5
        $this->DefaultOffset = $this->getOffset($this->TZLocal);
    }

    private function getOffset( $tz ) {
        if ($tz=='') return 0; # Heure GMT
        return timezone_offset_get( new \DateTimeZone( $tz ), new \DateTime() );
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

    public function FormatTime($d,$duration = true) {
       // Si negatif, on le garde de cote
       $prefix = "";
       if ($d<0) {
           $prefix = "-";
           $d *= -1;
       }
       $str = '';
       $l = 0;
       if ($d>31557600) {
           $n = (int) ($d/31557600);

           // Si chiffre énorme (> 20ans) et duration == true, on est dans une duree en cours
           if (($n>20) and ($duration)) {
               $d = (time() - $d);
               $prefix = "";
           }
           else {
            $d %= 31557600;
            $str .= ' '.$n.$this->translator->trans('date.y');
            $l++;
           }
       }
       if ($l>1) return $str;
       if ($d>2592000) {
           $n = (int) ($d/2592000);
           $d %= 2592000;
           $str .= ' '.$n.$this->translator->trans('date.m');
           $l++;           
       }
       if ($l>1) return $str;
       if ($d>86400) {
           $n = (int) ($d/86400);
           $d %= 86400;
           $str .= ' '.$n.$this->translator->trans('date.d');
           $l++;           
       }
       if ($l>1) return $str;
       if ($d>3600) {
           $n = (int) ($d/3600);
           $d %= 3600;
           $str .= ' '.$n.$this->translator->trans('time.h');           
           $l++;
       }
       if ($l>1) return $str;
       if ($d>60) {
           $n = (int) ($d/60);
           $d %= 60;
           $str .= ' '.$n.$this->translator->trans('time.m');         
           $l++;
       }
       if ($l>1) return $str;
       
       if ($d>0) 
        $str .= ' '.round($d).$this->translator->trans('time.s');
       return $prefix.$str;
    }
    
    public function Duration($start,$end = '' ) {
       if ($end == '') {
           $end = time();
       }
       $d = $end - $start;
       return $this->FormatTime($d);
    }

    // obsolete !! Aucun ordonnanceur ne loggue en localtime
    public function Time2Local($time,$spooler='',$gmt=false) {
        return $this->ShortDate( date( 'Y-m-d H:i:s', $time) );
    }
    
    public function Date2Local($date,$spooler,$short=false) {
        if (isset($this->TZOffset[$spooler])) 
            $offset = $this->TZOffset[$spooler];
        else 
            $offset = $this->DefaultOffset; // heure GMT par defaut
        $time = strtotime( substr($date,0,10).' '.substr($date,11,8 ));
        if ($short)
            return $this->ShortDate(date( 'Y-m-d H:i:s', $time + $offset));
        else 
            return date( 'Y-m-d H:i:s', $time + $offset);            
    }
}
