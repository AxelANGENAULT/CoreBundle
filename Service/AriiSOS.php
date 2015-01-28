<?php
// src/Sdz/BlogBundle/Service/AriiSQL.php
 
namespace Arii\CoreBundle\Service;

class AriiSOS
{
    protected $method; 
    protected $audit;
    protected $log;
    
    public function __construct( \Arii\CoreBundle\Service\AriiAudit $audit,  \Arii\CoreBundle\Service\AriiLog $log ) {
        $this->audit = $audit;
        $this->log = $log;
        $this->method = 'POST';
    }

    public function XMLCommand($spooler, $host,$port,$path,$protocol,$cmd, $priority = 'tag')
    {        
        if ($path =='') $path='/';

        //$url = $protocol.'://'.$_SERVER['SERVER_NAME'].'/js/'.$spooler.'/'.$cmd;
        //return $url;
        // Nouveauté 1.7
        if ($this->method=='POST') {
            $url = $protocol."://".$host.":".$port;
            $opts = array (
                    'http' => array (
                      'method' => "POST",
                      'header'=>"Content-Type: text/xml\r\n",
                      'content' => $cmd          
            //             'header' => $auth,
            //            'user_agent' => RESTClient :: USER_AGENT,
                    )
            );
            $context = stream_context_create($opts);
            $fp = @fopen($url,'r', false, $context);
        } 
        else {
            $url = $protocol."://".$host.":".$port.$path.\rawurlencode($cmd);
            $fp = @fopen($url,'r');
        }
        
        if (!$fp)
        {
            $this->audit->AuditLog($host, $cmd, "ERROR","JID", "!ERROR: Can not Open URL: ".$url);
            $this->log->createLog("!ERROR: Can not Open URL: ".$url, 0, __FILE__, __LINE__, "ERROR at: ".__FILE__." function: ".__FUNCTION__." line: ".__LINE__.": !ERROR: Connection failed! Please make sure the JobScheduler have started!", $_SERVER['REMOTE_ADDR']);
            return array('ERROR'=>'CONNECT ON '.$url);
        }
        $content = "";
        while (!feof($fp))
        {
            $content .= fgets($fp);
        }
        if ($priority=='xml') {
            return $content;
        }

        // probleme de trim
        if ($content=='') return '';
        $msg = '';
        if (strlen($content)<1024) {
            $msg = $content;
        }
        else {
            $msg = "...";
        }
        $this->audit->AuditLog($host, $cmd, "SUCCESS","JID", $msg);
        
        $result = $this->xml2array($content, 1, $priority);
        if ($result != null)
        {
            return $result;
        }
    }
    
    // nouveautés en 1.7
    private function PostUrl($service_url, $data = array(), $action = "POST") {

        $ch = curl_init($service_url);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $action );
//        $data = array("status" => 'R');
//        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
        $response = curl_exec($ch);
        if ($response === false) {
            $info = curl_getinfo($ch);
            curl_close($ch);
            die('error occured during curl exec. Additioanl info: ' . var_export($info));
        }
        curl_close($ch);
        return  $response;
/*
         $decoded = json_decode($response);
 
        if (isset($decoded->response->status) && $decoded->response->status == 'ERROR') {
            die('error occured: ' . $decoded->response->errormessage);
        }
        echo 'response ok!';
        var_export($decoded->response);        
*/
        }
    
    public function xml2array($contents, $get_attributes=1, $priority = 'tag') {
        if(!$contents) return array();

        if(!function_exists('xml_parser_create')) {
            //print "'xml_parser_create()' function not found!";
            return array();
        }

        //Get the XML parser of PHP - PHP must have this module for the parser to work
        $parser = xml_parser_create('');
        xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); # http://minutillo.com/steve/weblog/2004/6/17/php-xml-and-character-encodings-a-tale-of-sadness-rage-and-data-loss
        xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
        xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
        xml_parse_into_struct($parser, trim($contents), $xml_values);
        xml_parser_free($parser);

        if(!$xml_values) return;//Hmm...

        //Initializations
        $xml_array = array();
        $parents = array();
        $opened_tags = array();
        $arr = array();

        $current = &$xml_array; //Refference

        //Go through the tags.
        $repeated_tag_index = array();//Multiple tags with same name will be turned into an array
        foreach($xml_values as $data) {
            unset($attributes,$value);//Remove existing values, or there will be trouble

            //This command will extract these variables into the foreach scope
            // tag(string), type(string), level(int), attributes(array).
            extract($data);//We could use the array by itself, but this cooler.

            $result = array();
            $attributes_data = array();

            if(isset($value)) {
                if($priority == 'tag') $result = $value;
                else $result['value'] = $value; //Put the value in a assoc array if we are in the 'Attribute' mode
            }

            //Set the attributes too.
            if(isset($attributes) and $get_attributes) {
                foreach($attributes as $attr => $val) {
                    if($priority == 'tag') $attributes_data[$attr] = $val;
                    else $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
                }
            }

            //See tag status and do the needed.
            if($type == "open") {//The starting of the tag '<tag>'
                $parent[$level-1] = &$current;
                if(!is_array($current) or (!in_array($tag, array_keys($current)))) { //Insert New tag
                    $current[$tag] = $result;
                    if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                    $repeated_tag_index[$tag.'_'.$level] = 1;

                    $current = &$current[$tag];

                } else { //There was another element with the same tag name

                    if(isset($current[$tag][0])) {//If there is a 0th element it is already an array
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                        $repeated_tag_index[$tag.'_'.$level]++;
                    } else {//This section will make the value an array if multiple tags with the same name appear together
                        $current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                        $repeated_tag_index[$tag.'_'.$level] = 2;

                        if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well
                            $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            unset($current[$tag.'_attr']);
                        }

                    }
                    $last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                    $current = &$current[$tag][$last_item_index];
                }

            } elseif($type == "complete") { //Tags that ends in 1 line '<tag />'
                //See if the key is already taken.
                if(!isset($current[$tag])) { //New Key
                    $current[$tag] = $result;
                    $repeated_tag_index[$tag.'_'.$level] = 1;
                    if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

                } else { //If taken, put all things inside a list(array)
                    if(isset($current[$tag][0]) and is_array($current[$tag])) {//If it is already an array...

                        // ...push the new element into that array.
                        $current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;

                        if($priority == 'tag' and $get_attributes and $attributes_data) {
                            $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        }
                        $repeated_tag_index[$tag.'_'.$level]++;

                    } else { //If it is not an array...
                        $current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                        $repeated_tag_index[$tag.'_'.$level] = 1;
                        if($priority == 'tag' and $get_attributes) {
                            if(isset($current[$tag.'_attr'])) { //The attribute of the last(0th) tag must be moved as well

                                $current[$tag]['0_attr'] = $current[$tag.'_attr'];
                                unset($current[$tag.'_attr']);
                            }

                            if($attributes_data) {
                                $current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                            }
                        }
                        $repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                    }
                }

            } elseif($type == 'close') { //End of tag '</tag>'
                $current = &$parent[$level-1];
            }
        }

        return($xml_array);
    } 

}
