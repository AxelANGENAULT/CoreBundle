<?php
namespace Arii\CoreBundle\Service;
use Arii\CoreBundle\Service\AriiSession;

class AriiSQL
{
    private $session;
    
    protected $driver;
    protected $owner;
    
    protected $spooler;
    protected $job_name;
    protected $job_chain;
    protected $order_id;
    protected $status;
    protected $ref_date;
    protected $ref_past;
    protected $ref_future;
    protected $ref_timestamp;
    
    protected $past;
    protected $future;
    
    protected $enterprise;
    
    protected $Access;
    
    public function __construct(AriiSession $session)
    {
        $this->session = $session;        
        $this->enterprise = $session->getEnterprise();
        
        // Securité !
        $this->Access = $session->getTeamRights();
        // on retraite les champs d'accès
        $n = 0;
        while (isset($this->Access[$n]['path'])) {
            $this->Access[$n]['job_name'] = $this->AppendPath($this->Access[$n]['path'],$this->Access[$n]['job']);
            $this->Access[$n]['job_chain'] = $this->AppendPath($this->Access[$n]['path'],$this->Access[$n]['job_chain']);
            $n++;
        }
        
        $db = $session->getDatabase();
        $this->driver = $db['driver'];
        if (isset($db['owner']))
            $this->owner = $db['owner'];
        else 
            $this->owner = '';
        
        $Spooler =$session->getSpooler( );
        $this->spooler = $Spooler['name'];
        if ($this->spooler == 'spooler.all')
            $this->spooler = '%';
        
        $this->ref_date   = $session->getRefDate();
        $this->ref_past   = $session->getRefPast();
        $this->ref_future = $session->getRefFuture();
        $this->ref_timestamp = $session->getRefTimestamp();

        // Correction des dates 
        switch ($this->driver) {
            case 'oci8':
            case 'oracle':
            case 'pdo_oci':    
                $this->past = "to_date('".$session->getPast()."','yyyy-mm-dd hh24:mi:ss')";
                $this->future = "to_date('".$session->getFuture()."','yyyy-mm-dd hh24:mi:ss')";
                break;
            default:
                $this->past   = '"'.$session->getPast().'"';
                $this->future = '"'.$session->getFuture().'"';
                break;
        }

        // filtre en cours 
        $Filter = $session->getUserFilter();
        $this->spooler   =  str_replace('*','%',$Filter['spooler']);
        $this->job_name   = str_replace('*','%',$Filter['job']);
        $this->job_chain  = str_replace('*','%',$Filter['job_chain']);
        if ($this->job_chain=='') $this->job_chain='%';
        $this->order_id  = str_replace('*','%',$Filter['order_id']);
        if ($this->order_id=='') $this->order_id='%';
        $this->status     = str_replace('*','%',$Filter['status']);
		
        # valeur par defaut, au cas ou...
        if ($this->ref_past=="")
            $this->ref_past=-4;
        if ($this->ref_future=="")
            $this->ref_future=2;
        if ($this->spooler=="")
            $this->spooler="%";
    }
    
    // Force le driver 
    public function setDriver($driver) {
        $this->driver = $driver;
    }
    
    private function AppendPath($path,$obj,$noroot = true) {
        if ($noroot) {
            if (substr($path,0,1)=='/') 
                    $path = substr($path,1);
        }
        $new = str_replace('%%','%',$path.$obj);
        return $new;
    }
    
    /* Couche d'abstraction */
    public function Select( $Columns = array(), $option='' ) {   
        $Select = array();
        foreach ($Columns as $c) {
            if (($p = strpos($c,' as '))>0) {
                array_push($Select,$this->ColumnSelect(trim(substr($c,0,$p+1)),trim(substr($c,$p+4))));
            }
            else {
                array_push($Select,$this->ColumnSelect(trim($c)));
            }
        }
        return 'select '.$option.' '.implode(',',$Select);
    }

    /* Couche d'abstraction */
    public function Update( $Table ) {   
       return 'update '.join(',',$Table);
    }

   # Fonction owner
   private function DBO( $Table ) {
        if ($this->owner=='')
           return $Table;
       
        $DBO = array();
        foreach ($Table as $t) {
            array_push($DBO,$this->owner.'.'.$t);
        }
        return $DBO;
    }
    
   public function From( $Table ) {
       return ' from '.join(',',$this->DBO($Table));
   }

   public function Delete( $Table ) {
       return 'delete from '.join(',',$this->DBO($Table));
   }

   public function LeftJoin( $table, $Columns ) {
       $Cols = array();
       foreach ($Columns as $c ) {
           array_push($Cols,$this->Column($c));
       }
       if ($this->owner<>'')
           $table = $this->owner.'.'.$table;
       return ' left join '.$table.' on '.implode('=',$Cols);
   }

   public function Where( $Fields = array(), $sql = 'where' ) {
       $Where = array();
       foreach ($Fields as $k=>$v) {
           switch($k) {
               case '{enterprise}':
                   if ($this->enterprise != '') {
                        array_push( $Where, 
                           '('.$this->Column($Fields['{enterprise}']).' = "'.$this->enterprise.'" )' );
                           // .' or isnull('.$this->Column($Fields['{ordered}']).'))' );
                   }
                   elseif (isset($Fields['{enterprise}'])) {
                       array_push( $Where,
                           '( isnull('.$this->Column($Fields['{enterprise}']).'))' );                       
                   }
                   break;
               case '{id}':
                    array_push($Where, ' '.$this->Column($Fields['{id}']).'= ');
                    break;
               case '{spooler}':
                    if (($this->spooler != '')  and ($this->spooler != '%')) {
                        array_push($Where, '( '.$this->Column($Fields['{spooler}']).' like "'.$this->spooler.'" )' );
                    }
                    break;
               case '{!(spooler)}':
                   array_push($Where, '( '.$this->Column($Fields['{!(spooler)}'])." <> '(Spooler)' )" );
                   break;
               case '{!pending}':
                   array_push($Where, '( '.$this->Column($Fields['{!pending}'])." <> 'pending' )" );
                   break;
               case '{start_time}':
/*
                    // Les tranches consomment et n'apportent rien
                    array_push( $Where, 
                           '( ( '.$this->Date($Fields['{start_time}'],' >= ',$this->past).' )'
                            .' and ( '.$this->Date($Fields['{start_time}'],' <= ',$this->future ).' ) )' );
 */
                    array_push( $Where, 
                           '( '.$this->Date($Fields['{start_time}'],' >= ',$this->past).' )' );
                   break;
               case '{start_timestamp}':
                    array_push( $Where, 
                           '( '.$this->Date($Fields['{start_timestamp}'],' >= ',($this->ref_timestamp+($this->ref_past*86400))).' )' );
                   break;
               case '{next_start_time}':
                    array_push( $Where, 
                           '( ( '.$this->Date($v,' >= ',$this->past).' )'
                            .' and ( '.$this->Date($v,' <= ',$this->future ).' ) )' );
                   break;
               case '{end_time}':
                    array_push( $Where, 
                        /*   '( ( '.$this->Date($Fields['{end_time}'],' >= ',$this->past).' )'
                            .' and ( '.$this->Date($Fields['{end_time}'],' <= ',$this->future ).' )'
                       */   '( ( '.$this->Date($Fields['{end_time}'],' <= ',$this->future ).' )'
                            .' or '.$this->SqlFunction( 'isnull', $Fields['{end_time}'] ).' ) ' );
                   break;
               case '{max_error_id}':
                    array_push( $Where, 
                           '('.$this->Column($Fields['{max_error_id}'])
                            .' in (select max('.$this->Column('ID').') from SCHEDULER_HISTORY where '.$this->Column('EXIT_CODE').'>0) )' );
                   break;
               case '{standalone}':
                    array_push( $Where, 
                           '(('.$this->Column($Fields['{standalone}'])."<>'order'))" );
                           // .' or isnull('.$this->Column($Fields['{standalone}']).'))' );
                   break;
               case '{standalone2}':
                    array_push( $Where, 
                           '(('.$this->Column($Fields['{standalone2}'])."=0))" );
                           // .' or isnull('.$this->Column($Fields['{standalone}']).'))' );
                   break;
               case '{ordered}':
                    array_push( $Where, 
                           '('.$this->Column($Fields['{ordered}'])."= 'order' )" );
                           // .' or isnull('.$this->Column($Fields['{ordered}']).'))' );
                   break;
// permission
               case '{job_name}':
                   if (isset($Fields['job_name'])) {
                    array_push($Where, 'f( '.$this->Column($Fields['{job_name}'])." like '".$this->Column($Fields['job_name'])."' )" );                   
                   }
                    // Plus le filtre utilisateur
                   if (($this->job_name != '%') and ($this->job_name != ''))
                        array_push($Where, '( '.$this->Column($Fields['{job_name}'])." like '".$this->job_name."' )");
                   break;
               case '{job_chain}':
                    // Plus le filtre utilisateur
                   if ($this->job_chain != '%')
                        array_push($Where, '( '.$this->Column($Fields['{job_chain}'])." like '".$this->job_chain."' )");
                   break;
               case '{job|job_chain}':
                   list($j,$jc)=explode('|',$Fields['{job|job_chain}']);
                   $S = array();
                   if ($this->job_name != '%')
                      array_push($S,'( '.$this->Column($j)." like '".$this->job_name."' )");
                   if ($this->job_chain != '%')
                      array_push($S,'( '.$this->Column($jc)." like '".$this->job_chain."' )");
                   if (!empty($S))
                    array_push($Where, '('.implode(' or ',$S).')' );
                   break;
               case '{order_id}':
                   // Plus le filtre utilisateur
                   if ($this->job_chain != '%')
                        array_push($Where, '( '.$this->Column($Fields['{order_id}'])." like '".$this->order_id."' )");
                   break;
               case '{status}':
               case '{error}':
                   break;
               default:
                   if ($v == '(null)') {
                        // array_push($Where,'(isnull('.$this->Column($k)."))");
                        array_push($Where,'('.$this->SqlFunction('isnull', $k).')');
                   }
                   elseif ($v == '(!null)') {
                        array_push($Where,'('.$this->SqlFunction('isnotnull', $k).')');
                   }
                   elseif (strpos(" $v",'%'))  {
                        array_push($Where,'( '.$this->Column($k)." like '".$v."')");
                   }
                   elseif (($p=strpos($k,'|'))>0) {    
                        array_push($Where,'( '.$this->Column(substr($k,0,$p)).substr($k,$p+1)."'".$v."')");
                   }
                   else {    
                        array_push($Where,'( '.$this->Column($k)."='".$v."')");
                   }
                   
            }
       }
       $Result = array();
       if (!empty($Where))
            array_push($Result,'('.implode(' and ',$Where).')');
     
       $access = $this->Access($Fields);
       if ($access != '')
            array_push($Result, $access );
       if (empty($Result)) return '';

       return " $sql ".implode(' and ',$Result);
   }

    public function Set( $Fields = array() ) { 
       $Set = array();
       foreach ($Fields as $k=>$v) {
            switch($k) {
                case 'START_TIME':
                    switch ($this->driver) {
                        case 'pdo_oci':
                            array_push( $Set,$this->Column($k)."= TO_DATE('$v', 'yyyy-mm-dd hh24:mi:ss')");
                            break;
                        default:
                            array_push($Set,$this->Column($k)."='".$v."'");
                    }
                   break;
               default:
                    array_push($Set,$this->Column($k)."='".$v."'");
                   break;
            }
       }
       return ' set '.implode(', ',$Set);
    }

   public function OrderBy( $List ) {
        $Order = array();
        foreach ($List as $c) {
            if (($p = strpos($c,' '))>0) {
                array_push($Order,$this->Column(trim(substr($c,0,$p+1))).' '.substr($c,$p+1));
            }
            else {
                array_push($Order,$this->Column(trim($c)));
            }
        }
        return ' order by '.implode(',',$Order);
   }
   
   public function GroupBy( $List ) {
        $Order = array();
        foreach ($List as $c) {
            array_push($Order,$this->Column(trim($c)));
        }
        return ' group by '.implode(',',$Order);
   }

   public function Limit( $size, $offset=0 ) {
        switch ($this->driver) {
            case 'postgre':
            case 'postgres':
            case 'pdo_pgsql':
                return " limit $size offset $offset";
                break;
            case 'oci8':
            case 'oracle':
            case 'pdo_oci':
                return ""; // il faudra intégrer le row num!
                break;            
            default:
                return " limit $offset, $size";
        }

   }

   public function ColumnSelect($col,$as='') {

        // est ce que c'est une fonction ?
        $fct = $params = '';
        if (($p = strpos($col,'('))>0) {
            $fct = substr($col,0,$p);
            $col = substr($col,$p+1,strpos($col,')')-$p-1);
            // est ce qu'il y a des parametres ?
            if (($p = strpos($col,','))>0) {
                $params =  substr($col,$p+1);
                $col = substr($col,0,$p);
            }
        }

        // gestion des guillements
        switch ($this->driver) {
            case 'postgre':
            case 'postgres':
            case 'pdo_pgsql':
                $q = '"';
                break;
            default:
                $q = '';
        }
        // est ce que c'est prefixé ?
        if (($p = strpos($col,'.'))>0) {
			$var = substr($col,$p+1);
            $col = substr($col,0,$p+1).$q.$var.$q;

        }
        else {
			$var = $col;
            $col = $q.$col.$q;
        }

        $col = $this->SqlFunction($fct,$col,$params);
        
        // Dates
        switch ($this->driver) {
            case 'oci8':
            case 'oracle':
            case 'pdo_oci':
                // Fonction de date Oracle
                if (in_array($var,array('START_TIME','END_TIME'))) {
                        if ($as == '') {
                                if ($p>0) {
                                        $as = substr($col,$p+1);
                                }
                                else {
                                        $as = $col;
                                }
                        }
                        $col = "to_char( $col, 'yyyy-mm-dd hh24:mi:ss')"; 
                }
                break;
        }
        
        if ($as != '') {
            $col .= ' as "'.$as.'"';
        }
        return $col;
   }
   
   public function Column($col,$as='') {
        // est ce que c'est une fonction ?
        $fct = $params = '';
        if (($p = strpos($col,'('))>0) {
            $fct = substr($col,0,$p);
            $col = substr($col,$p+1,strpos($col,')')-$p-1);
            // est ce qu'il y a des parametres ?
            if (($p = strpos($col,','))>0) {
                $params =  substr($col,$p+1);
                $col = substr($col,0,$p);
            }
        }
        // gestion des guillements
        switch ($this->driver) {
            case 'postgre':
            case 'postgres':
            case 'pdo_pgsql':
                $q = '"';
                break;
            default:
                $q = '';
        }
        // est ce que c'est prefixé ?
        if (($p = strpos($col,'.'))>0) {
            $col = substr($col,0,$p+1).$q.substr($col,$p+1).$q;
        }
        else {
            $col = $q.$col.$q;
        }
		
		$col = $this->SqlFunction($fct,$col,$params);

        if ($as != '') {
            $col .= ' as "'.$as.'"';
        }
        return $col;
   }

    private function SqlFunction($fct,$col,$params='') {
        if ($fct=='') return $col;
        switch ($fct) {
            case 'day':
                switch ($this->driver) {
                    case 'postgre':
                    case 'postgres':
                    case 'pdo_pgsql':
                        return 'extract( day from '.$col.')';
                    case 'oci8':
                    case 'oracle':
                    case 'pdo_oci':
                        return "TO_CHAR( $col, 'YYYY-MM-DD HH24:MI')"; 
                    default:
                        return 'day('.$col.')';
                }
                break;
            case 'max_day':
                switch ($this->driver) {
                    case 'oci8':
                    case 'oracle':
                    case 'pdo_oci':
                        return "TO_CHAR( MAX($col), 'YYYY-MM-DD HH24:MI')"; 
                    default:
                        return 'max(day('.$col.'))';
                }
                break;
            case 'max_date':
                switch ($this->driver) {
                    case 'oci8':
                    case 'oracle':
                    case 'pdo_oci':
                        return "TO_CHAR( MAX($col), 'YYYY-MM-DD')"; 
                    default:
                        return 'max(left('.$col.'))';
                }
                break;
            case 'getdate':
                switch ($this->driver) {
                    case 'postgre':
                    case 'postgres':
                    case 'pdo_pgsql':
                        return 'date('.$col.')';
                    default:
                        return 'left('.$col.',13)';
                }
                break;            
            case 'getmonth':
                switch ($this->driver) {
                    case 'postgre':
                    case 'postgres':
                    case 'pdo_pgsql':
                        return 'extract( month from '.$col.')';
                    default:
                        return 'month('.$col.')';
                }
            case 'isnull':
                switch ($this->driver) {
                    case 'oci8':
                    case 'oracle':
                    case 'pdo_oci':
                        return "$col is null";
                    case 'postgre':
                    case 'postgres':
                    case 'pdo_pgsql':
                        return '"'.$col.'" is null';
                    default:
                        return 'isnull('.$col.')';
                }			
                break;            
            case 'isnotnull':
                switch ($this->driver) {
                    case 'oci8':
                    case 'oracle':
                    case 'pdo_oci':
                        return "$col is not null";
                    case 'postgre':
                    case 'postgres':
                    case 'pdo_pgsql':
                        return '"'.$col.'" is not null';
                    default:
                        return 'not(isnull('.$col.'))';
                }			
                break;            
            }
        return $this->SimpleFunction( $fct,$col,$params);     
      }
      
      private function SimpleFunction( $fct,$col,$params) {
                $col = $fct.'('.$col;
                if ($params!='') {
                    $col .= ','.$params;
                }
                $col .= ')';
                return $col;
      }
      private function Date($col,$ope,$val) {
        $date = '';
        switch ($this->driver) {
            case 'oci8':
            case 'oracle':
            case 'pdo_oci':    
                $date = $this->Column($col).' '.$ope.' '.$val;
                break;
            case 'postgre':
            case 'postgres':
            case 'pdo_pgsql':
                $date = $this->Column($col).' '.$ope." '$val'";
                break;
            default:
                $date = $this->Column($col).' '.$ope.' '.$val;
                break;
        }
        return $date;
   }

    /**
     * Utilisation du connecteur 
     *
     * @param string $connector
     */
    public function Filter( $Fields = array() )
    {   
        $Filter = array();
        if (isset($Fields['{spooler}']) and ($this->spooler != '')  and ($this->spooler != '%')) {
            array_push($Filter, '( '.$Fields['{spooler}'].' like "'.$this->spooler.'" )' );
        }

        if (isset($Fields['start_time']))
            array_push($Filter, '( '.$Fields['start_time'].' >= '.$this->past.' )' );

			array_push($Filter, $Fields['spooler']." like '%'");
        return implode(' and ',$Filter);
    }
    
    public function Spoolers($Fields = array()) {
        if ($this->spooler =='%')
            return 1;
        return $Fields['spooler'].' = "'.$this->spooler.'"';
    }
 
    public function AndOr($Fields = array(),$ope = 'and') {
        $List = array();
        foreach ($Fields as $k => $v) {
            array_push($List,$this->Column($k)."='".$v."'");
        }
        return ' ('.implode(' '.$ope.' ',$List).') ';
    }

    public function NextStartTime($Fields = array()) {
        if (isset($Fields['next_start_time'])) {
            return '(( '.$Fields['next_start_time'].' >= '.$this->past.' ) '
                    . 'and ( '.$Fields['next_start_time'].' <= '.$this->future.' ))';
        }
        return 1;
    }

     public function History( $Fields = array())
    {   
        $Filter = array();
        if (isset($Fields['spooler'])) {
            if ($this->spooler !='%') {
                array_push($Filter, '( '.$this->Spoolers($Fields).' )' );
            }
        }
        
        # Historique simple 
        # on prend une fenetre glissante
        if (isset($Fields['start_time'])) {
            array_push($Filter, 
                   '
                   (( '.$Fields['start_time'].' >= '.$this->past.' ) and ( '.$Fields['start_time'].' <= '.$this->future.' )
                            )' );
        }
        // print implode(" AND ",$Filter);
        // exit();
        if (count($Filter)==0) return 1;
        return implode(" AND ",$Filter);
    }
    
    public function Status($status = array())
    {
        $Filter = array();
        if($status['SUCCESS']==1)
        {
            array_push($Filter, "(status='SUCCESS')");
        }
        if($status['STOPPED']==1)
        {
            array_push($Filter, "(sh.STOPPED=1)");
        }
        if($status['RUNNING']==1)
        {
            array_push($Filter, "(sh.END_TIME=='')");
        }
        if($status['FAILURE']==1)
        {
            array_push($Filter, "(sh.ERROR>0)");
        }
        if($status['LATE']==1)
        {
            array_push($Filter, "(status='LATE')");
        }
        return implode(" AND ",$Filter);
    }

     public function Order( $Fields = array() )
    {   
        $Filter = array();
        if (isset($Fields['spooler'])) {
            array_push($Filter, '( '.$Fields['spooler'].' like "'.$this->spooler.'" )' );
        }
        
        # Historique simple 
        # on prend une fenetre glissante
        if (isset($Fields['start_time'])) {
            array_push($Filter, 
                   '((( '.$Fields['start_time'].' <= '.$this->past.' ) 
                        and ( '.$Fields['end_time'].' >= '.$this->past.' ))
                   OR (( '.$Fields['start_time'].' >= '.$this->past.' ) 
                        and ( '.$Fields['start_time'].' <= '.$this->future.' )
                            ))' );
        }
        if (isset($Fields['created_time'])) {
            $filter = '( ( ('.$Fields['created_time'].' >= '.$this->past.' )
                       and ( '.$Fields['created_time'].' <= '.$this->future.' ) ) ';            
            if (isset($Fields['mod_time'])) {
                $filter .= 'or ( ( '.$Fields['mod_time'].' >= '.$this->past.' )
                            and ( '.$Fields['mod_time'].' <= '.$this->future.' ) ) '; 
            }
            $filter .=')';
            array_push( $Filter, $filter );
        }
        return implode(" AND ",$Filter);
    }

   // restriction des accès en fonction des colonnes disponibles
   private function Access( $Fields = array() ) {
       $Access = $this->Access;

       if (empty($Access)) return '';

       // prendre en compte le repository ?
       $Right = array();
       /*
           if (isset($Fields['{job|job_chain}'])) {
            list($Fields['{job_name}'],$Fields['{job_chain}']) = explode('|',$Fields['{job|job_chain}']);
       }
       */
       foreach (array('job_name','job_chain','order_id','spooler','job|job_chain') as $k) {
           $key = '{'.$k.'}';
           if (isset($Fields[$key])) {
                $Right[$k] = $Fields[$key];
           }
       }

       if (empty($Right)) return '';
       // On recrée les filtres des droits
       // c'est ici qu'on doit prendre en compte le repository
       $Lines = array();
       foreach ($Access as $line) {
           $Cols = array();
           if ($line['R']==1) {
               $pass = ''; 
           }
           else { 
               $pass = 'not';
           }
           foreach (array_keys($Right) as $k ) {
                if ($k == 'job|job_chain') {
                   $F = explode('|',$Right[$k]);
                   $Special = array();
                   $n = 0;
                   foreach (array('job','job_chain') as $s ) {
                        if (isset($line[$s])) {
                             $val = $line[$s];
                             if (strpos(" $val",'%')>0) $operator='like';
                                 else $operator='=';
                             array_push($Special,'('.$F[$n]." $operator '$val')" );
                        }
                        $n++;
                   }
                   array_push($Cols,$pass.'('.implode(' or ',$Special).')');
                }
                else {
                    if (($line[$k]!='') and ($line[$k]!='%')) {
                        $val = $line[$k];
                        if (strpos(" $val",'%')>0) $operator='like';
                            else $operator='=';
                        if (!(($operator == 'like') and ($val == '%'))) {
                            array_push($Cols,$pass.'('.$Right[$k]." $operator '$val')" );
                        }
                    }
                }
            }
            if (!empty($Cols))
                array_push($Lines,'( '.implode(' and ',$Cols).')');
       }

       if (!empty($Lines))
            return '( '.implode( ' or ',$Lines).' )';
       return '';
   }
   
}
