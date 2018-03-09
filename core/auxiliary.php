<?php


       
$db_main = mysqli_connect("localhost","root","","thecontainer");  
mysqli_set_charset($db_main, "utf8mb4"); 

//time configurationn
mysqli_query($db_main, "SET time_zone='+0:00'");
define('TIMEZONE', 'Asia/Manila');
define('dflt_fmt',"F j, Y, g:i A");


function search_for_string( &$array_ref, $search_string = "" )	{
		 		if ( ( is_array( $array_ref ) && in_array( $search_string, $array_ref ) ) ||
					( !is_array( $array_ref ) && preg_match( "#" . preg_quote( $search_string ) . "#", $array_ref ) ) ) {
				  		return true;
				} 
				return false;
    }
function hack_free( $data, $extras = "" ){   //extras are exceptions
    global $swiss_army;		
		if ( strlen( $extras ) > 0 ) {
					 $extras = preg_split( "#,#", $extras );
  	} 
		global $db_main;
		if ( is_array( $data ) ) { // ...
						array_map( "hack_free", $data ); //how did I not know about this function?
						// how to call a function inside object scope with this function? :s
		} 
		else {
  					/*
	  				* le filters
		  			*/
			 		
				  	$data = !search_for_string( $extras, "htmlspecialchars" ) ? htmlspecialchars( $data ) : $data;
					  /*
						* posting format code goes here
						*/
						 $data = search_for_string( $extras, "break-lines" ) ? str_replace( "\n", "<br>", $data ) : $data;
  					 // that was bizzare
	  				/*
		  			* and then filters again
			  		*/
				  	$data = !search_for_string( $extras, "mysqli_real_escape_string" ) ? mysqli_real_escape_string( $db_main, $data ) : $data;
					  $data = !search_for_string( $extras, "stripslashes" ) ? stripslashes( $data ) : $data;
					  $data = !search_for_string( $extras, "trim" ) ? trim( $data ) : $data;			
	  } 
    return $data;
}
if ( $_SERVER['REQUEST_METHOD'] === "POST" ) {
		foreach( $_POST as $key => $room ) {
				$_SPIN[$key] = hack_free( $_POST[$key], "break-lines,stripslashes" ); //for content to be displayed
        $_DATA[$key] = hack_free( $_POST[$key] ); //but otherwise it's just pure data
		} 
} 
if ( isset( $_SESSION ) ) {
		foreach( $_SESSION as $mkey => $mvalue ) { // reiterate each session and array
				  $_MONITORED[$mkey] = hack_free( $mvalue );
					// $_MONITORED[$mkey] = mysqli_real_escape_string($db_main, $_MONITORED[$mkey]);
		} 
} 
foreach( $_GET as $mkey => $mvalue ) {
		$_FILTERED[$mkey] = hack_free( $mvalue );
}
    
    class preset extends internal_var {
        public $log = "";          
        public function adjusted($timestamp){
            return (isset($_SESSION['offset'])) ? intval($timestamp) + $_SESSION['offset'] : $timestamp;
        }         
        public function log($action,$message = ""){
            switch($action){
                case "log":
                    $this->log = $this->log . $message . "\n";
                break;
                case "show":
                    return $this->log;
                break;
            }    
        }                
        public function error_notice($type){
            switch($type){
                case "image not found":
                    $this->log("log","image not found");
                    return "";
                break;
            }
        }
        public function plural( $s ){
						if ( $s > 1 ) {
								return "s";
						} 
				} 
        public function dflt_dfmt($timestamp){ 
            return date(dflt_fmt,$this->adjusted($timestamp));
        }
        public function time_rounds( $date ){
            $date_res = (preg_match("#^[0-9]+$#",$date)) ? $date : strtotime( $date ); 
    				$diff = microtime(true) - $date_res;
    				if ( $diff < 60 ) {								
    						 	$date2 = "Less than a minute ago";
    								} 
    				if ( $diff < 3600 && $diff > 59 ) {
    								$scheck = $this->plural( floor( $diff / ( 3600 * 24 ) ) );
    								$date2 = floor( $diff / ( 60 ) ) . " minute" . $this->plural( floor( $diff / ( 60 ) ) ) . " ago";
    								} 
    				if ( $diff < 3600 * 24 && $diff > 3599 ) {
    								$scheck = $this->plural( floor( $diff / ( 3600 * 24 ) ) );
    								$date2 = floor( $diff / ( 3600 ) ) . " hour" . $this->plural( floor( $diff / ( 3600 ) ) ) . " ago";
    								} 
    				if ( $diff < 3600 * 24 * 7 && $diff > ( 3600 * 24 )-1 ) {
    								$scheck = $this->plural( floor( $diff / ( 3600 * 24 ) ) );
    								$date2 = floor( $diff / ( 3600 * 24 ) ) . " day" . $scheck . " ago";
    								} 
    				if ( $diff < 3600 * 24 * 30.46 && $diff > ( 3600 * 24 * 7 )-1 ) {
    								$scheck = $this->plural( floor( $diff / ( 3600 * 24 * 7 ) ) );
    								$date2 = floor( $diff / ( 3600 * 24 * 7 ) ) . " week" . $scheck . " ago";
    								} 
    				if ( $diff < 3600 * 24 * 7 * 365 && $diff > ( 3600 * 24 * 30.46 )-1 ) {
    								$scheck = $this->plural( floor( $diff / ( 3600 * 24 * 30 ) ) );
    								$date2 = floor( $diff / ( 3600 * 24 * 30 ) ) . " month" . $scheck . " ago";
    								} 
    				if ( $diff > ( 3600 * 24 * 7 * 365 )-1 ) {
    								$scheck = $this->plural( floor( $diff / ( 3600 * 24 ) ) );
    								$date2 = floor( $diff / ( 3600 * 24 * 7 * 365 ) ) . " year" . $scheck . " ago";
    								} 
    				return $date2;
				} 
    }
    class quick_commands extends internal_var{
        public static $last_msg = "";
        public static $last_props = array();
        public static $log = array();
        public static $qr_data = array();
        public static $qr_status = "";
        public static $array_links = array();
        public static $qr_temp = array();
        public static $qr_count = 0;
        public static $rel_log = array();
        public static $last_sql_error = "";
        public static $count = 0;
        public static $tb_views = array();
        public function var_dump(...$variable){
            foreach($variable as $dt){
                echo "<pre>";
                var_dump($dt);
                echo "</pre>";
            }
        }
        public function truth_hrc($type,...$bools){
            /*
               false priority: first value found false, and it'll return false. No values returned false and it's true.
               true priority: first value found true, and it'll return true. No values returned true and it's false.
               I'll add some logical xor's later not sure if I really need it though
            */
            if(count($bools) === 0){return false;}
            else{
                switch($type){
                    case "false priority":
                        foreach($bools as $key=>$val){
                            if($val !== true) return false;    
                        }
                        return true;
                    break;
                    case "true priority":
                        foreach($bools as $key=>$val){
                            if($val === true) return true;    
                        }
                        return false;
                    break;
                }
            }
        }
        public function add_to_links($array_key,$str){
            //set the row
            self::$array_links[$array_key] = isset(self::$array_links[$array_key]) && is_array(self::$array_links[$array_key]) ? array_merge(self::$array_links[$array_key],[$str]) : [$str];
        }
        public function create_reference($str_name,$lastmsg = "   "){
            $lastmsg = $lastmsg !== "   " ? $lastmsg : $this->last_msg; 
            self::$log = array_merge(self::$log,[$str_name => $lastmsg]);
        }
        public function ref($ref,$action = "view"){
            return self::$log[$ref];
        }
        public static function dbq_relay($str,$opt = "return"){
            if(isset($str)){
                switch($opt){
                    case "return":
                        if(isset(self::$rel_log[$str])){
                            return self::$rel_log[$str];
                        }
                    break;    
                    case "declare":
                        $random_hash_lol = "a_2_z_" . md5(rand(10345,1231233));
                        self::$rel_log[$str] = $random_hash_lol;
                    break;
                }
            }
        }
        
        /*public function arview_sync($array,$key_list = ""){
            $num = 0;
            if($num === 0){
                $array_in_limbo = [];
            }
            if(isset($this->array_links[$key_list])){
                if($num === 0){
                    $key_list = $this->array_links[$key_list];
                }
                $current = $key_list[$num];
                if($num > count($key_list)){
                    $array_in_limbo[$current] = self::$qr_temp[$current];
                    $num++;
                    self::arview_sync($array_in_limbo,$)
                }
                
            }else{
                return false;
            }
            
        }
        
        
        
        
        
        
        public function expand($command,$continue = false,$name = "sub_d1t1",$limit = 50){
            self::$qr_count = self::$qr_count === 0 ? 0 : self::$qr_count;
            $expand_count = self::$qr_count;
            //this would be the equivalent of a less efficient RIGHT/INNER JOIN except the secondary table is going to be a subdata of the first. I kinda need it
            //probably one of the more brilliant things i've concocted in here, which is saying a lot lol     
            //you need an array with a key-value pair, then value will be array with first value looking for pattern on aforementioned key and second with the SELECT query to expand the data with
            //e.g. this is the format that $command should bne in $command = {"dbtid","1", "SELECT * FROM blablabla"}
            //{"parent_table_column_name_reference","value","SQL query to expand"}
            //This function will look for all dbtid's with 1 in the referenced data and then return the selected query from the second if it works.
            //In the future i'll add in a function where you can set the check from the second data but I don't need it right now
            //$name cannot match any possible column name so make it ridiculous
             /*
            
            foreach(self::$rel_log as $key2 => $val2){ 
                        if(preg_match("#".$val2."#",$command[2])){ //check to see if it matches the SQL statement hash                                                               
                            $replacement = $val[$key2]; 
                            $command[2] = preg_replace("/".$val2."/i",$replacement,$command[2]);   
                        }
                    }
            
             ->expand([
                                  "dbid_ref",
                                  $actual_dbid,
                                  "SELECT * FROM db_columns WHERE type != 'choice' AND dbtid_ref='".$quick_commands->dbq_relay('dbfid')."'"
                              ],true)
                              
                              
                              ->expand([
                                  "type",
                                  "selection",
                                  "SELECT * FROM db_columns WHERE type='choice' AND maxlength='". $quick_commands->dbq_relay('dbcid')."'"
                                  ])   
            
            
            
            $prop = [
                'column_ref' => $command[0],
                'value_ref' => $command[1],
                'sql_q' => $command[2],
            ];
             
            $array_q = self::$qr_data;                                                 

            //qr_temp will be temporary array to be appended to array_q or qr_data;
            if(self::$qr_status === ""){
                foreach($array_q as $key => $val){
                    foreach(self::$rel_log as $key2 => $val2){//replace the synced(dbq_relay()'ed) value from the SQL statement earlier
                        //idk how to describe it technically so im just gonna say sync
                        if(preg_match("#".$val2."#",$command[2])){ //check to see if it matches the SQL statement hash                                                               
                            $replacement = $val[$key2]; 
                            $prop['sql_q'] = preg_replace("/".$val2."/i",$replacement,$prop['sql_q']);   
                        } 
                    }
                    if(self::$qr_status === ""){
                        $children = $this->quick_dbq($prop['sql_q'],"all");
                        if(is_array($children)){
                            $array_q[$key][$name] = $children;   
                            self::add_to_links($key,$key);   
                            self::add_to_links($key,$name);  
                            self::$qr_data[$key][$name] = $children;  
                            self::$qr_temp[$key][$name] = $children; 
                        }
                    }
                }         
            }
            if(self::$qr_status === true){
                //qr_temp will be cleared every chained expand past the first to accomodate for the new ones
                //qr_links will be deleted per iteration
                //3 levels of arrays after 2nd method chain
              
                foreach(self::$array_links as $key => $val){
                    foreach($val as $ke => $va){
                        $pv = isset($pv) ? $pv[$key][$va] : self::$qr_temp[$key];
                    }
                    $pr[$key] = $pv;
                }
                foreach($pr as $ky => $vl){
                    $last = count(self::$array_links[$ky]) - 1;
                    foreach(self::$rel_log as $key2 => $val2){//replace the synced(dbq_relay()'ed) value from the SQL statement earlier
                        //idk how to describe it technically so im just gonna say sync
                        if(preg_match("#".$val2."#",$command[2])){ //check to see if it matches the SQL statement hash                                                               
                            $replacement = $vl[$last_sub][$key2]; 
                            $prop['sql_q'] = preg_replace("/".$val2."/i",$replacement,$prop['sql_q']);   
                        }
                    }
                }
                return $pv;
                /*foreach(self::$qr_temp as $key => $val){
                    $last = count(self::$array_links[$key]) - 1;
                    $last_sub = self::$array_links[$key][$last];
                    
                    foreach($val[$last_sub] as $ke => $va){
                        foreach(self::$rel_log as $key2 => $val2){//replace the synced(dbq_relay()'ed) value from the SQL statement earlier
                            //idk how to describe it technically so im just gonna say sync
                            if(preg_match("#".$val2."#",$command[2])){ //check to see if it matches the SQL statement hash                                                               
                                $replacement = $va[$key2]; 
                                $prop['sql_q'] = preg_replace("/".$val2."/i",$replacement,$prop['sql_q']);   
                            } 
                        }
                        //two or more expands
                        //we just need the last two keys. Ergo the two most recent
                        $children = $this->quick_dbq($prop['sql_q'],"all");  
                        if(is_array($children){
                            self::$qr_temp[$key][$last_sub][$name] = $children; 
                            
                        }
                        
                         
                    }
                       
                }                     
                self::$qr_temp = [];          
            }              
            if($continue === true){
                self::$qr_status = true;
                return $this;
            }
            if($continue === false){ 
                return self::$qr_data;
            }    
        }*/
        
        public function dbqr($type,$name){
            global $db_main;
            switch($type){
                case "all":
                case "num_rows":
                case "boolean":
                case "first_only":            
                    return isset(self::$qr_data[$name][$type]) ? self::$qr_data[$name][$type] : "none";
                break;    
                case "clear":
                    unset(self::$qr_data[$name]);
                break;
            }
        }        
        public function quick_dbq($sql_st,$return_as = "first_only",$column_name="none",$reserve = false){
            global $db_main;
            
            if(is_array($sql_st)){
                //STILL SANITIZE YOUR SHIT
                /*  for now i'm just gonna do inserts, the rest seem unnecessary tbh
                    SCHEMA
                        "INSERT"
                         => "table_name", [
                             "column name" => "value",
                             "column name" => "value"...
                         ]
                        
                
                */
                if(isset($sql_st["INSERT"][0]) && isset($sql_st["INSERT"][1]) && gettype($sql_st["INSERT"][0]) === "string" && gettype($sql_st["INSERT"][1]) === "array"){
                    $cols = ""; $vals = "";
                    foreach($sql_st["INSERT"] as $key=>$val){
                        $cols .= $key;
                        $vals .= $val;
                    }
                    $execution = $this->quick_dbq("INSERT INTO ".$sql_st["INSERT"][0]."($cols) VALUES($vals)");
                    return $execution;
                }
            }
            else{ 
                if(gettype($sql_st) === "string"){     
                $statement = mysqli_query($db_main, $sql_st); //SANITIZE YOUR SHIT
                if(!$statement){
                    $this->last_sql_error = mysqli_error($db_main);
                    return mysqli_error($db_main);
                }                      
                else{                       
                    if(preg_match("#^[ \t]{0,}(SELECT|select)#", $sql_st)){
                            switch($return_as){
                                case "first_only":
                                    $q_token = mysqli_fetch_assoc($statement);
                                    if($column_name !== "none"){
                                        return isset($q_token[$column_name]) ? $q_token[$column_name] : false;
                                    }
                                    return $q_token;
                                break;
                                case "all":
                                case "resultant":
                                    
                                    while($q_token = mysqli_fetch_assoc($statement)){
                                        if($column_name !== "none" && $return_as === "all"){ //indexing by column values
                                            $q_cache[$q_token[$column_name]] = $q_token;    
                                        }else{
                                            $q_cache[] = $q_token;
                                        }
                                    }
                                    
                                    switch($return_as){
                                        case "all":
                                            if($reserve === true && isset($q_cache)){
                                                self::$qr_data = $q_cache;
                                            }
                                            
                                            $return_val = !isset($q_cache) ? "none" : $q_cache;
                                        break;
                                        case "resultant":
                                            if(count(self::$qr_data) === 0){
                                                self::$qr_data[$column_name]["first_only"] = mysqli_fetch_assoc($statement);     
                                                self::$qr_data[$column_name]["all"] = isset($q_cache) ? $q_cache : [];
                                                self::$qr_data[$column_name]["num_rows"] = mysqli_num_rows($statement);
                                                self::$qr_data[$column_name]["boolean"] = (mysqli_num_rows($statement) > 0);
                                                return $this;
                                            }else{
                                                return "Error: resultant already in place.";
                                            }
                                        break;
                                    }
                                    
                                    return ($reserve === false) ? $return_val : $this;
                                break;
                                case "num_rows";
                                    return mysqli_num_rows($statement);
                                break;
                                case "boolean";
                                    return (mysqli_num_rows($statement) > 0);
                                break;
                            }                                
                            if(mysqli_num_rows($statement) === 0){
                                switch($return_as){
                                    case "":
                                    break;
                                    default:                              
                                        $this->last_msg = "executed";
                                        $this->last_props = ($return_as !== "num_rows") ? ["status" => "none_returned"] : ["status" => "executed", "rows" => 0];
                                    break;
                                }
                            }     
                        }
                        else{
                            $this->last_msg = "executed";
                            $this->last_props = ["status" => "none_returned"];
                            return ($reserve === true) ? $this : "executed";
                        }
                        
                        mysqli_free_result($statement);             
                    }
                } 
            }      
        }//end quick_db
        public function increment(&$num){
            $num = $num + 1;
            return $num;
        }
        public function regexify($string){
        
        }
        public function check_pw($password,$salt,$hash){
            return hash_equals(substr(hash("sha512",$salt.$password.substr(md5($password.strlen($password)),0,50)),0,150),$hash); //seems wacky enough right ;)
        }
    }     
    $quick_commands = new quick_commands();    
    if(isset($_SESSION['logged_q'])){
        $user_dt = $quick_commands->quick_dbq("SELECT username,uid,db_affinity,column_propz FROM userz WHERE username = '$_MONITORED[logged_q]' AND salt ='$_MONITORED[salt_q]'","first_only"); 
    }
    class extra_commands extends quick_commands{
        public function submission_process($redir,$forwhom,$extra_opts = "none"){ global $_SPIN;
            $_SESSION['submit_q'][$forwhom] = [];
            if($extra_opts !== "no_data"){
                foreach($_POST as $key => $var){ 
                    $_SESSION['submit_q'][$forwhom][$key] = $_SPIN[$key];
                }
                $_SESSION['message'][$forwhom] = $extra_opts;
            }
            $_SESSION['redir_location'] = $redir;
            header("Location: " .$redir);
        }
        public function generate_hash($password,&$salt = "",&$pw_hash = ""){
            if($salt === ""){
                $salt = substr(md5(microtime(true)."e".microtime(true)),0,25);
                $pw_hash = substr(hash("sha512",$salt.$password.substr(md5($password.strlen($password)),0,50)),0,150);
            }else{
                $salt = substr(md5(microtime(true)."e".microtime(true)),0,25);
                $pw_hash = substr(hash("sha512",$salt.$password.substr(md5($password.strlen($password)),0,50)),0,150);
            }
        }
        public function db_scan($id,$typeof_scan,$array_id = "default"){
            switch($id){
                case "any":
                break;                                           
                default:
                    if(preg_match("#^[0-9]+$#",$id)){
                        switch($typeof_scan){
                            case "epi": //extra person info
                                $array_dt = $this->quick_dbq("SELECT * FROM extra_person_info WHERE pi_affinity='$id'","all");
                                return $array_dt;
                            break;
                        }
                    }
                break;
            }
        }
        public function dbc_scout($dbcid,$dbtfid = 0,$result ="boolean"){ global $user_dt;
            $dbcid = intval($dbcid);
            $dbtfid = ($dbtfid === "native") ? intval($user_dt['db_affinity']) : intval($dbtfid) ;
           
            switch($result){
                case "boolean": 
                    if($dbtfid === 0) {
                        return ($this->quick_dbq("SELECT * FROM db_columns WHERE dbcid='$dbcid'","boolean"));
                    }                            
                    else{                                                                                               
                        return ($this->quick_dbq("SELECT * FROM db_columns WHERE dbcid='$dbcid' AND dbtid_ref='$dbtfid'","boolean"));
                    }
                break;
                case "info":
                    if($this->dbc_scout($dbcid)){
                        $dbcdt = $this->quick_dbq("SELECT * FROM db_columns WHERE dbcid='$dbcid'","first_only");
                        if($dbcdt["type"] === "selection"){
                            $dbcdt["selection"] = $this->quick_dbq("SELECT * FROM db_columns WHERE type='choice' AND maxlength='$dbcdt[dbcid]'","all");       
                        }
                        return $dbcdt;
                    }
                break;
            }
            
        }
        public function perm_check($permission_code,$privilege_q,$dbfid){ global $user_dt;
            if($user_dt['uid'] !== "0"){
                $permissions = str_split($permission_code); 
                switch($permission_code){
                    case "SCAN":  
                    case "SCAN2":              
                        $valuee = $permission_code === "SCAN" ? "'".hack_free($dbfid)."'" : "(SELECT dbf_ref FROM actual_data WHERE ad_id='".hack_free($dbfid)."')";
                        $permission_code = $quick_commands->quick_dbq("SELECT permission_val FROM dbform WHERE dbfid=$valuee","first_only","permission_val");
                        if($permission_code === false) return false;
                    break;
                }
                
                
                //let's declare permissions first
                //3321 is default value
                //1 stands for admin only, 2 stands for mods and admin, and 3 stands for everyone
                //every first digit(3) declares who can post
                //every second digit declares who can view 
                //every third digit declares who can edit content
                //every fourth digit declares who can change settings and view schema 
                
                if(intval($user_dt['db_affinity']) === intval($dbfid)){
                    switch($user_dt['db_position']){
                        case "admin":
                            $rank = 1;
                        break;
                        case "mod":
                            $rank = 2;
                        break;
                        case "member":
                            $rank = 3;
                        break;
                    }
                    foreach($permissions as $key => $val){
                        $permissions[$key] = intval($val);
                    }
                    switch($privilege_q){
                        case "post":
                            return ($rank <= $permissions[0]) ? true : false;
                        break;
                        case "view":    
                            return ($rank <= $permissions[1]) ? true : false;
                        break;
                        case "edit":    
                            return ($rank <= $permissions[2]) ? true : false;
                        break;
                        
                        case "manage":
                            return ($rank <= $permissions[3]) ? true : false;
                        break;
                    }
                }
                
            }else{//super root admin~~ this seems so vulnerable lol
                return ($_SESSION['logged_q'] === $user_dt['username']) ? true : false;
            }
        }
        
        public function optz_x($key,$key2="",$val="true",$option = "check"){ 
        
            //a function for the database table view.
            global $db_main,$quick_commands,$_MONITORED;
            $source = $this->quick_dbq("SELECT column_propz FROM userz WHERE username='$_MONITORED[logged_q]'");
            $real_option = json_decode($source['column_propz'],true); 
          
            switch($key){ 
            default:
            switch($option){
                case "disp":
                            
                break;
                case "check":
                    //$real_option json return scheme
                    /*     
                        [0] => //this will be the id name of the dbform
                        open_state => opened
                        default_option => 0 //could be 0, 1 or 2 and refers to what default tab is opened
                    */
                    //optz_x($_FILTERED['id_name'],"true",0
                
                    if(isset($real_option[$key]['default_option']) && $real_option[$key]['default_option'] !== "$key2"){
                        return " clear";
                    }else{
                        if(isset($real_option[$key]['default_option']) && $real_option[$key]['default_option'] === "$key2"){
                            return "";
                        }else{
                            return ($val === "false") ? "" : " clear";
                        }
                    }
                break;
                case "check_status":
                    if(count($real_option) === 0){
                        return false;
                    }
                    else{  
                        if(isset($real_option[$key]['default_option'])){
                            
                            return ($real_option[$key]['default_option'] === "$key2") ;
                        }else{
                            if($val === "false"){
                                return true;
                            }
                        }
                    }
                break;
                case "tab_check":
                    if(count($real_option) === 0){
                        echo false;
                    }
                    else{
                        echo ($real_option[$key]['open_state'] === $key_2) ;
                    }
                break;
            }
            break;
            case "\\":
                return $real_option;
            break;
            }
        }
        public function get_data($id,$type="id_only",$columns="all"){   
            switch($type){
            
                case "duplicate_check":
                    if(gettype($columns) === "array"){
                        foreach($columns as $key => $val){ //sanitize your shit
                            $append = isset($append) ? " row_value='$val'" : " OR row_value='$val'";
                        }
                        $query = $this->get_data($id,"SELECT * FROM actual_data WHERE dbc_ref='".hack_free($id)."' AND ($append)");
                        return ($query === false); //false means no duplicates
                    }
                break;            
                case "id_only":        
                    $get_adt = $this->quick_dbq("SELECT * FROM actual_data WHERE ad_ref='".intval($id)."' AND ad_id != '".intval($id)."' ORDER BY dbc_ref ASC LIMIT 100","all");      
                    
                    if(is_array($columns)){
                    }
                    else{
                        if(is_array($get_adt)){     
                            foreach($get_adt as $key=>$val){
                                $dbcrr = $val["ad_ref"];
                                $adt[$dbcrr][$key] = $val;                              
                            }
                            return $adt;
                        }else{
                            return false;
                        }
                    }
                case "scan":        
                    if(preg_match("#^[0-9]+$#",$id) === 0){
                        //sanitize ur shit pls
                       $sql = $id;     
                    }else{
                        $sql = "SELECT * FROM actual_data WHERE ad_ref=ad_id AND dbf_ref='$id'";        
                    }
                    
                    $get_adt = $this->quick_dbq($sql,"all");
                     
                        if(!is_array($get_adt)){
                                return false;
                            }
                            else{
                            
                        foreach($get_adt as $key3 => $val3){
                                
                                $rek = $this->get_data($val3["ad_ref"],"id_only");           
                                $array_dump = isset($array_dump) ? array_merge($rek,$array_dump) : $rek;
                            
                        }
                        }             
                    return $array_dump;
                break;
            }       
        }
        public function return_sel_tab($id,$x,$default = "true"){ global $real_option;
            return ($this->optz_x($id,"$x","true","check_status") === true || ($default === "false" && !isset($real_option[$id]["default_option"]))) ? " selected_tab" : "";
        }
        public function value_check($val_a,$val_b,$return_if_t = "",$return_if_f = ""){
            if($val_a === $val_b){
                return ($return_if_f !== "") ? $return_if_t : true;
            }else{
                return ($return_if_f !== "") ? $return_if_f : false;
            }
        }
        public function a_d_snippet($ad_ref){
            $data = $this->quick_dbq("SELECT * FROM actual_data WHERE ad_ref=(SELECT ad_ref from actual_data WHERE ad_id='".intval($ad_ref)."') AND ad_ref != ad_id","all","ad_id");
            foreach($data as $ad_id => $val){
                $dbc_info = $this->dbc_scout($val["dbc_ref"],"native","info");
                $data[$ad_id]["column_info"] = $dbc_info;
            }
            return (is_array($data)) ? $data : false;
        }
        public function examine_perm($permission_code,$rank,$privilege_q,$return_val = "none"){
            $permissions = str_split($permission_code);
            
            foreach($permissions as $key => $val){
                $permissions[$key] = intval($val);
            }
          
            
            switch($privilege_q){
                case "edit":    
                    if($return_val !== "none"){
                        return ($rank === $permissions[2]) ? $return_val : "";
                    }
                    return ($rank === $permissions[2]) ? true : false;
                break;
                case "view":   
                    if($return_val !== "none"){
                        return ($rank === $permissions[1]) ? $return_val : "";
                    } 
                    return ($rank === $permissions[1]) ? true : false;
                break;
                case "manage":
                    if($return_val !== "none"){
                        return ($rank === $permissions[3]) ? $return_val : "";
                    }
                    return ($rank === $permissions[3]) ? true : false;
                break;
                case "post":
                    if($return_val !== "none"){
                        return ($rank === $permissions[0]) ? $return_val : "";
                    }
                    return ($rank === $permissions[0]) ? true : false;
                break;
            }
        }
        public function view_table($id,$table = []){ global $_MONITORED,$text,$db_main,$user_dt,$text_i,$regex_check; 
            $get_dt = (count($table) !== 0) ? $get_dt : $this->db_schema($id);         
            
            if($this->perm_check($get_dt['permission_val'],"view",$id)){
                $real_option = json_decode($user_dt['column_propz'],true);  
                $real_option = $real_option === NULL ? [] : $real_option; 
                $default = $get_dt['default_view'];    
                $default2 = $get_dt['redir_after_submit'];
                echo "<div class='tabz' rel='$id'>";
                //set links visibility according to admin/mod permissions
                //perm_check($permission_code,$privilege_q,$dbid)      
                $edit_check = $this->perm_check($get_dt['permission_val'],"edit",$get_dt['dbfid']); 
                $view_check = $this->perm_check($get_dt['permission_val'],"view",$get_dt['dbfid']); 
                $post_check = $this->perm_check($get_dt['permission_val'],"post",$get_dt['dbfid']); 
                $manage_check = $this->perm_check($get_dt['permission_val'],"manage",$get_dt['dbfid']);
                
                echo "<div class='options'>";   
                                 
                echo (!$post_check) ? "" : "<a href='javascript:void(0)' rel='new_data' ref='tabz' class='spec2".$this->return_sel_tab($id,0,"false")."'>$text[add_new_data]</a>";
                echo (!$view_check) ? "" :  "<a href='javascript:void(0)' rel='manage_data' ref='tabz' class='spec2".$this->return_sel_tab($id,1)."'>$text[manage_data]</a>";
                echo (!$manage_check) ? "" :  "<a href='javascript:void(0)' rel='dbf_settings' ref='tabz' class='spec2".$this->return_sel_tab($id,2)."'>$text[dbf_settings]</a>";
                
                //priority on viewing 
                //then check user's own options
                //then check default options on database
                
     
                if(count($real_option) === 0){
                    $real_option[$id]['default_option'] = "0";
                    $wrapped_ro = mysqli_real_escape_string($db_main,json_encode($real_option)); //condoms are necessary    
                    $this->quick_dbq("UPDATE userz SET column_propz='".$wrapped_ro."' WHERE username='$_MONITORED[logged_q]'"); 
                }

                
                if($manage_check){ 
                    $open_checker = (
                        (isset($real_option[$id]['open_state']) && $real_option[$id]['open_state'] === "open" ) 
                        ||
                        ($get_dt["is_open"] === true)
                    ) ? " checked='checked'" : "";                                             
                    echo "<div class='view_optz'>" . $text['view_opts'];
                    echo "<input type='checkbox' name='def_open' value='$id'$open_checker>";
                    echo "</div>";                
                }
                echo "</div>";   //off #options
                
                if($post_check){
                    echo "<div class='tabby".$this->optz_x($id,0,"false")."' rel='new_data'>";   //new data
                    echo "<div class='view_optz'>" . $text['view_opts2'];
                
                    $check_mark = ($this->optz_x($id,0,"false","check_status") === true) ? " checked='checked'" : "";
                    echo "<input type='radio' name='tab_$id' class='tab_switcher' rel='$id' value='0'$check_mark>";
                    echo "</div>";      
                    foreach($get_dt["columns"] as $key=>$val){
                        $require_note = "";
                        if($val['is_required'] === "true"){     
                            $require_note = "<span class='required_note'>(is required)</span>";
                        }
                        if($get_dt["intangible_dbf"] === $val['dbcid']){
                            $require_note .="<span class='required_note'>(is a unique identifier)</span>";
                        }
                        echo "<div class='form_box'>";
                        echo "<h4>$val[dbc_name] $require_note</h4>";
                        switch($val["type"]){//input methods
                            case "selection":
                                echo "<select name='inputa".$id."_".$val['dbcid']."'>";
                                
                                foreach($val["selections"] as $ke=>$va){
                                    echo "<option value='$va[dbcid]'>$va[dbc_name]</option>";
                                }
                                echo "</select>";
                            break;
                            default:
                                switch($val["type"]){
                                    case "integer":
                                        $regex = $regex_check["integer"];
                                    break;
                                    case "string":
                                        $regex = "^.{2,".intval($val['maxlength'])."}$";
                                    break;
                                }
                                echo "<input name='inputa".$id."_".$val['dbcid']."' class='spec5' ref='regex_check' rel='/".$regex."/' type='text'>";
                            break;
                        }
                        
                        echo "</div>";    
                    }
                    
                    if(!isset($real_option["redir_after_post"])){
                        $check_mark2 = ($default2 === "true") ? " checked='checked'" : "";
                    }else{
                        $check_mark2 = ($real_option["redir_after_post"] === "true") ? " checked='checked'" : "";
                    }
                    
                    echo "<div class='option'><input type='checkbox'$check_mark2 name='redir_opt' class='spec2'> $text[redir_opt]</div>";
                    
                    echo "<input type='submit' name='submita' ref='submit_new_dt' class='spec2' rel='$id'>";
    
                    
                    echo "</div>";
                    /*echo "<div class='form_box'><h4>{}</h4>";
                    echo "<input type='text' name='itx{}' value='{}' class='flick'>";
                    echo "</div>";
                
                    echo "<div class='form_box intangible'><h4>{}</h4>";
                    echo "<button class='intangible_button spec2'>...</button>";
                    echo "<input type='text' name='intngbl{}'>";
                    echo "</div>";   
                
                    echo "<div class='form_box selection'><h4>{}</h4>";
                    echo "<select name='slctn{}'>";
                    echo "<option value='choice{}'>text{}</option>";
                    echo "</select>";
                    echo "</div>";
                    ;*/ //end new data
                }
                if($view_check){
                    echo "<div class='tabby".$this->optz_x($id,1)."' rel='manage_data'>";   //begin manage data
                    echo "<div class='view_optz'>" . $text['view_opts2'];
           
                    $check_mark = ($this->optz_x($id,1,"true","check_status") === true) ? " checked='checked'" : "";
                    echo "<input type='radio' name='tab_$id' class='tab_switcher' rel='$id' value='1'$check_mark>";
                    echo "</div>";    
                    echo "<div class='search_bar'>";
                    echo "<div>";
                    echo "<select class='parameters spec2' ref='order' rel='$id'>";
                    echo "<option value='none' class='default'>(Search Query Condition)...</option>";
                    echo "<option value='begins_with'>Begins with...</option>";
                    echo "<option value='ends_with'>Ends with...</option>";
                    echo "<option value='excludes'>Excludes the following...</option>";
                    echo "</select>";
                    
                    echo "<input type='text' value='$text[search_x]' class='flick largeform query' rel='$id'>";
                    $cell_num = 0;
                    echo "<select class='selector' rel='$id'><option value='all'>Search on column... (All currently)</option>";
                    foreach($get_dt["columns"] as $key => $val){
                        echo "<option value='$val[dbcid]' type='$val[type]'>$val[dbc_name]</option>";
                    }
                    echo "</select>";   
                    echo "</div><div>";
                    echo "<select class='ordering' rel='$id'><option value='none'>Order by column...</option>";
                    foreach($get_dt["columns"] as $key => $val){
                        $typeof_order = ($val["type"] === "integer") ? "(numerical)" : "(alphabetical)" ;
                        echo "<option value='$val[dbcid]'>$val[dbc_name] $typeof_order</option>";
                    }
                    echo "</select>";
                    echo "<select class='typeof_order' rel='$id'><option value='desc'>Descending Order</option><option value='desc'>Ascending Order</option></select>";
                    echo "<button class='button_help spec2' ref='button_help' rel='dt_search'>";
                    echo "</div>";    
                    
                    echo "<button class='submit_clone spec2' ref='content_search' rel='".$id."'>Search!</button>";
                    
                    echo "</div>";         
                    
                    $this->data_table($id,$get_dt["columns"]);   
                
                    echo "</div>";  //end manage_data
                }
                
                if($manage_check){
                    $_SESSION['table_mod_new_col'][$get_dt["dbfid"]] = isset($_SESSION['table_mod_new_col'][$get_dt["dbfid"]]) ? $_SESSION['table_mod_new_col'][$get_dt["dbfid"]] : 1; 
                    echo "<div class='tabby".$this->optz_x($id,2)."' rel='dbf_settings'>";   //begin table settings    
                    echo "<div class='view_optz'>" . $text['view_opts2'];
                    $check_mark = ($this->optz_x($id,'2',"true","check_status") === true) ? " checked='checked'" : "";
                    echo "<input type='radio' name='tab_$id' class='tab_switcher' rel='$id' value='2'$check_mark>";
                    echo "</div>";             
                    
                    echo "<button class='submit_clone spec2 finish_edits' ref='finish_edits' rel='$get_dt[dbfid]'>Finish Edits</button>";
                    
                    
                    echo "<div class='dbf_settins'>";
                    
                    echo "<div class='div4'>";
                    echo "<h5>Table/Form Name</h5>";
                    echo "<input type='text' name='table_".$id."_name' value='$get_dt[table_name]'>";
                    echo "</div>";
                    
                    echo "<div class='div4'>";
                    echo "<h5>Default Permissions</h5>";
                    
                    echo "<div class='div5'>";
                     
                //3321 is default value
                //1 stands for admin only, 2 stands for mods and admin, and 3 stands for everyone
                //every first digit(3) declares who can post
                //every second digit declares who can view 
                //every third digit declares who can edit content
                //every fourth digit declares who can change settings and view schema 
                    
                     /*
                     
                     switch($privilege_q){
                        case "edit":    
                            return ($rank <= $permissions[2]) ? true : false;
                        break;
                        case "view":    
                            return ($rank <= $permissions[1]) ? true : false;
                        break;
                        case "manage":
                            return ($rank <= $permissions[3]) ? true : false;
                        break;
                        case "post":
                            return ($rank <= $permissions[0]) ? true : false;
                        break;
                    }
                     
                     //        public function examine_perm($permission_code,$perm_q,$return_val){
            $perm = str_split($permission_code);
            $perm = array_walk($permissions,"intval");
                    //let's declare permissions first
                     */
                    echo "<div class='plaintxt'><strong>Note:</strong> Selecting an option will also enable said permission for all permission groups to the right of said option.</div>";
                    
                    
                    
                    echo "<div class='right_opt'>";
                    echo "<h5>Post Data</h5>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>User</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_1'".$this->examine_perm($get_dt["permission_val"],3,"post","checked='checked '")." value='3'>";
                    echo "</div>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>Manager</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_1'".$this->examine_perm($get_dt["permission_val"],2,"post","checked='checked '")." value='2'>";
                    echo "</div>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>Admin</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_1'".$this->examine_perm($get_dt["permission_val"],1,"post","checked='checked'")." value='1'>";
                    echo "</div>";
                    echo "</div>";
                    
                    echo "<div class='right_opt'>";
                    echo "<h5>View Data</h5>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>User</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_2'".$this->examine_perm($get_dt["permission_val"],3,"view","checked='checked'")." value='3'>";
                    echo "</div>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>Manager</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_2'".$this->examine_perm($get_dt["permission_val"],2,"view","checked='checked'")." value='2'>";
                    echo "</div>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>Admin</strong>";      
                    echo "<input type='radio' name='table_".$id."_opt_2'".$this->examine_perm($get_dt["permission_val"],1,"view","checked='checked'")." value='1'>";
                    echo "</div>";
                    echo "</div>";
                    
                    
                    echo "<div class='right_opt'>";
                    
                    echo "<h5>Edit Data</h5>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>User</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_3'".$this->examine_perm($get_dt["permission_val"],3,"edit","checked='checked'")." value='3'>";
                    echo "</div>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>Manager</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_3'".$this->examine_perm($get_dt["permission_val"],2,"edit","checked='checked'")." value='2'>";
                    echo "</div>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>Admin</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_3'".$this->examine_perm($get_dt["permission_val"],1,"edit","checked='checked'")." value='1'>";
                    echo "</div>";
                    echo "</div>";
                    
                    echo "<div class='right_opt'>";
                    
                    echo "<h5>Change Table Settings</h5>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>User</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_4'".$this->examine_perm($get_dt["permission_val"],3,"manage","checked='checked'")." value='3'>";
                    echo "</div>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>Manager</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_4'".$this->examine_perm($get_dt["permission_val"],2,"manage","checked='checked'")." value='2'>";
                    echo "</div>";
                    
                    echo "<div class='opti2'>";      
                    echo "<strong>Admin</strong>";
                    echo "<input type='radio' name='table_".$id."_opt_4'".$this->examine_perm($get_dt["permission_val"],1,"manage","checked='checked'")." value='1'>";
                    echo "</div>";
                    
                    echo "</div>";
                    
                    echo "</div>";
                    
                    
                                                                       
                    echo "</div>";           //end a dbf_settin
                    
                    echo "<div class='div4'>";
                    echo "<h5>Will this tab be open by default?</h5>";
                    echo "<p>This will not affect the settings of current users, just future ones.</p>";
                    
                    $checks = [
                        "true" => $this->value_check("true",$get_dt["is_open"]," checked='checked'"," "),
                        "false" => $this->value_check("false",$get_dt["is_open"]," checked='checked'"," "),
                    ];
                    
                    echo "
                    
                    Yes <input type='radio' name='table_".$id."_def_open' value='true'$checks[true]>
                    
                    No <input type='radio' name='table_".$id."_def_open' value='false'$checks[false]>";  
                    
                    echo "<br></div>";
                    
                    echo "</div>";  //end the right side option panel 
                    
                    
                    
                    echo "<div class='form_box speci'>";
                    echo "<h4><a href='javascript:void(0)' ref='add_new_column' rel='$id' class='spec2'>Add New Column...</a></h4>";
                    echo "</div>";
                         
                    foreach($get_dt["columns"] as $key => $val){
                        
                        //display each column settings
                        echo "<div class='form_box optsz'>";
                        echo "<h4>$val[dbc_name]";
                        echo "<a href='javascript:void(0)' class='spec2 submit_clone' ref='delete_something' type='column' num='$val[dbcid]'>$text[delet_2]</a>"; 
                        echo"</h4>";  
                        
                        echo "<div class='div3'>";
                        echo "<strong>Column Name:</strong><br>";
                        echo "<input type='text' name='dbc_$val[dbcid]_name' class='spec5' ref='regex_check' r_test='test_alt' rel='/".$regex_check['column_name_mf']."/' value='$val[dbc_name]'>";
                        echo "</div>";
                        
                        echo "<div class='div3'>";
                        echo "<strong>Default Value:</strong><br>";                    //^(?!Column Name$|[ \t]+|.?$|.{50,})
                        echo "<input type='text' name='dbc_$val[dbcid]_defval' class='spec5' ref='regex_check' rel='/^(?!Default Value$|[ \t]+|.{".$val['maxlength'].",})/' value='$val[defval]'>";
                        echo "</div>";
                        
                        echo "<div class='div3'>";
                        echo "<strong>Is this column required to be filled out?</strong>";
                        $is_required = ($val['is_required'] === "false" || $val["is_required"] === "" || !isset($val["is_required"])) ? false : true;
                        echo "<br><select name='dbc_$val[dbcid]_is_req' rel='$id'>";
                        $vals = $is_required ? ["selected='selected'",""] : ["","selected='selected'"];
                        echo "<option value='true'$vals[0]>Yes</option>";
                        echo "<option value='false'$vals[1]>No</option>";                        
                        echo"</select>";
                        echo "</div>";
                        
                        if($val["type"] === "selection"){
                            $_SESSION["sel"]["s".$val["dbcid"]] = isset($_SESSION["sel"]["s".$val["dbcid"]]) ? $_SESSION["sel"]["s".$val["dbcid"]] : 1;
                            
                            echo "<div class='div3 spec_box' id='s$val[dbcid]'><h4>Selections:</h4>";
                            foreach($val["selections"] as $ky2 => $vl2){
                                echo "<div class='spr' id='az$vl2[dbcid]'>";
                                echo "<input type='text' class='selectmod spec5' value='$vl2[dbc_name]' ref='regex_check' rel='/".$regex_check['selection2']."/'  name='dbc_$val[dbcid]_slct_$vl2[dbcid]' >";
                                echo "<a href='javascript:void(0)' class='spec2 submit_clone' ref='delete_something' type='selection' num='$vl2[dbcid]'>$text[delet]</a>"; 
                                echo "</div>";
                            }
                              echo "<div class='spc'>";
                              echo "<h4>Add Selections:</h4>";
                                echo '<input type="text" value="Selection" class="flick spec5" ref="regex_check" rel="/'.$regex_check['selection2'].'/" name="dts'.$val["dbcid"].'_selection1" extra="Selection">';
                                echo '<button class="submit_clone spec2" type="modifying" ref="add_selection">Add Possible Selection</button>';
                                echo "</div>";
                            echo "</div>";
                            
                        }
                        
                        echo "</div>";
                    }
                    /*   
                    echo "<div class='form_box'><h4>{}</h4>";
                    echo "<input type='text' name='i2tx{}' value='{}' class='flick'>";
                    echo "</div>";
                    
                    echo "<div class='form_box intangible'><h4>{}</h4>";
                    echo "<button class='intangible_button spec2'>...</button>";
                    echo "<input type='text' name='i2ntngbl{}'>";
                    echo "</div>";
                    
                    echo "<div class='form_box selection'><h4>{}</h4>";
                    echo "<select name='slctn{}'>";
                    echo "<option value='choice{}'>text{}</option>";
                    echo "</select>";
                    echo "</div>";  
                    */                             
                    echo "</div>";   //end table settings
                }
                
                
                echo "</div>"; //end tabz    
            }
        }
        
        public function sort_bya($a,$b){ global $order_by;
            return $a[$order_by]["row_value"] < $b[$order_by]["row_value"];
        }        
        public function sort_byb($a,$b){
            return $a[$order_by]["row_value"] > $b[$order_by]["row_value"];
        }     
        public function data_table(&$id,&$column_data,$alt_data = "",$order_by = "none",$sort_order="DESC"){  global $text,$filler_temp;
            //the table      
                    if(is_array($column_data)){
                    
                    echo ($alt_data === "") ? "<div id='manage_data".$id."' class='table_view' rel='".$id."'>" : "";
                    
                    echo "<h4>$text[current_data]</h4>";
                    echo "<div class='table_itself unfixed'>"; 
                    echo "<table width='100%'>";
                    echo "<tr><th width='1%'>#</th>";        $cell_num = 0;
                    foreach($column_data as $key => $val){
                        
                        if(isset($val["selections"])){
                            foreach($val["selections"] as $sels => $v4ls){
                                $selz[$v4ls['dbcid']] =$v4ls;
                            }
                            $val["selections"] = $selz;
                            
                            unset($selz); 
                            
                            
                        }   
                        echo "<th type='$val[type]'>$val[dbc_name] <button class='spec2 spec4 noselect' ref='toggle_sort' rel='$cell_num' message='$text[sort_2]' rel2='$id'></th>";
                        $cell_num++;
                        $columns[$val['dbcid']] = $val;
                    }
                  
                    
                    
                    echo "</tr>";
                                 

                    $count = count($column_data) + 1; //for a colspan   

                    $get_adt = is_array($alt_data) ? $alt_data : $this->get_data($id,"scan");     
                    
                    
                    
                    
                    
                    if(!$get_adt){
                        echo "<tr><td colspan='$count' class='notice_msg'>$text[no_data]</td></tr>";
                    }else{
                    
                    

                        
                        foreach($get_adt as $key=>$val){ 
                            foreach($val as $ke=>$va){
                                $content[$va['ad_ref']][$va['dbc_ref']] = $va;
                                
                            }
            
                            
                        }          
                        
                        /*public function sort_bya($a,$b){
            return $a["row_value"] < $b["row_value"];
        }        
        public function sort_byb($a,$b){
            return $a["row_value"] > $b["row_value"];
        }    */
                               
                        if($order_by !== "none" && isset($columns[$order_by])) {   
                            $order_by = $order_by;  
                            switch($order_by){
                                
                                case "DESC":
                                    //usort($content,"sort_bya");
                                break;
                                case "ASC":
                                    //usort($content,"sort_byb");
                                break;
                            }
                        }
                      
                        $counter = 1;
                        
                        //$this->var_dump($content);
                        
                        foreach($content as $key => $val){
                            echo "<tr>";
                            $row_letter = (!isset($row_letter) || $row_letter === "b") ? "a" : "b";
                            echo "<th width='1%'>$counter</th> ";

                            foreach($columns as $dbcid => $cdt){
                                //$this->var_dump($cdt);            
                                                               

                                $column_letter = (!isset($column_letter) || $column_letter === "b") ? "a" : "b";  
                                if(!isset($content[$key][$dbcid])){
                                    echo "<td class='data_cell $row_letter$column_letter halfview'>No Data Inserted.</td>";
                                }else{
                                    $ad_id =  $content[$key][$dbcid]['ad_id'];
                                    $is_select = isset($cdt["selections"]) ? "true" : "false";
                                    if(isset($cdt["selections"])){

                                        $row_val = isset($cdt["selections"][$content[$key][$dbcid]['row_value']]['dbc_name']) ? $cdt["selections"][$content[$key][$dbcid]['row_value']]['dbc_name'] : ""; //ik, it's disgusting lol
                                        
                                    }else{
                                        $row_val = $content[$key][$dbcid]['row_value'];
                                    }
                                    echo "<td class='spec4 data_cell $row_letter$column_letter' ref='table_cell' ad_id='$ad_id' is_selection='$is_select'>";
                                    
                                    
                                    echo "<div class='row_val'>" .$row_val . "</div>"; 
                                    echo $filler_temp->sub("e_d_cell",$ad_id);
                                    echo "</td>";
                                    
                                }
                            }
                            
                            $counter++; 
                            echo "</tr>";   
                            
                            unset($column_letter);
                        }
                             
                    }
                                                            
                    
                    echo "</table>";
                    echo "</div>";
                    
                    
                    echo ($alt_data === "") ? "</div>" : "";  //end the table
                    }
        }
        public function login_status($name,$when = "now"){
            if(isset($name)){      global $user_dt;
                switch($name){
                    case "root_admin":
                        return isset($_SESSION['logged_q'],$_SESSION['salt_q'],$user_dt) && $user_dt['uid'] === "0";
                    break;
                    case "_current_user":
                        return isset($_SESSION['logged_q'],$_SESSION['salt_q']) ;
                    break;
                    default:
                    break;
                }
            }    
        }
        public function status($call){ //some text displays for when you're logged in, and need a function to display for different conditions
                                       //blabla
                                       global $text;
            switch($call){
                case "dbref_display":
                    $user_data = $this->quick_dbq("SELECT * FROM userz WHERE username='$_SESSION[logged_q]'","first_only");
                    if($user_data['db_affinity'] === "0"){
                        return $text['root_admin'];
                    }
                    switch($db_position){
                    
                    }
                break;
            }
        }
        public function db_schema($id,$display = ["table_info"]){                     
            if(count($display) !== 0){
                if(array_search("table_info",$display) !== false){
                    if(array_search("db_info",$display) !== false){
                        $prototype = [$this->quick_dbq("SELECT * FROM dbz WHERE dbid='".intval($id)."'"),
                                      $this->quick_dbq("SELECT * FROM dbform WHERE dbid_ref='".intval($id)."'","all")              
                        ];
                        foreach($prototype[1] as $key => $val){
                            $prototype[1][$key]["columns"] = $this->quick_dbq("SELECT * FROM db_columns WHERE dbtid_ref='$val[dbfid]' AND type != 'choice'","all");
                            foreach($prototype[1][$key]["columns"] as $ky => $vl){
                                if($vl['type'] === "selection"){
                                    $prototype[1][$key]["columns"][$ky]["selections"] = $this->quick_dbq("SELECT * FROM db_columns WHERE type='choice' AND maxlength='".$vl['dbcid']."'","all");   
                                }
                            }
                        }
                    }else{
                        $prototype = $this->quick_dbq("SELECT * FROM dbform WHERE dbfid='".intval($id)."'","first_only");  
                        $prototype["columns"] = $this->quick_dbq("SELECT * FROM db_columns WHERE dbtid_ref='$prototype[dbfid]' AND type != 'choice'","all");
                        if(count($prototype["columns"]) !== 0){
                            foreach($prototype["columns"] as $ky => $vl){
                                if($vl['type'] === "selection"){
                                    $prototype["columns"][$ky]["selections"] = $this->quick_dbq("SELECT * FROM db_columns WHERE type='choice' AND maxlength='".$vl['dbcid']."'","all");   
                                }
                            }
                        }
                    }
                }
                else{   
                    if(array_search("db_info",$display) !== false){
                        $prototype = [$this->quick_dbq("SELECT * FROM dbz WHERE dbid='".intval($id)."'"),
                                      $this->quick_dbq("SELECT * FROM dbform WHERE dbid_ref='".intval($id)."'","all")              
                        ];
                    }else{ 
                        if(count($display) === 1 && array_search("dbc_dt_only",$display) !== false){
                            $prototype = $this->quick_dbq("SELECT * FROM db_columns WHERE dbcid='".intval($id)."' AND type != 'choice'","first_only");
                            if($vl['type'] === "selection"){
                                $prototype["selections"] = $this->quick_dbq("SELECT * FROM db_columns WHERE type='choice' AND maxlength='".$vl['dbcid']."'","all");   
                            }
                        }
                    }
                }   
                return $prototype; 
            }    
        }
        public function initialize_APT($option,$username = ""){
            switch($option){
                case "admin_only":
                    $row_check = $this->quick_dbq("SELECT * FROM internal_settings WHERE it_setting_name='APT' AND it_value='admin_only'","first_only");
                    if(!is_array($row_check)){
                        $updatee = $this->quick_dbq("UPDATE internal_settings SET it_value='admin_only' WHERE it_setting_name='APT'");
                        return ($updatee === "executed") ? true : $updatee;
                    }
                break;
                case "all":
                    $row_check = $this->quick_dbq("SELECT * FROM internal_settings WHERE it_setting_name='APT' AND it_value='all'","first_only");
                    if(!is_array($row_check)){
                        $updatee = $this->quick_dbq("UPDATE internal_settings SET it_value='all' WHERE it_setting_name='APT'");
                        return ($updatee === "executed") ? true : $updatee;
                    }
                break;
                case "specific_user":
                    $row_check = $this->quick_dbq("SELECT * FROM internal_settings WHERE it_setting_name='APT' AND it_value='specific_user' AND prop_1='$username'","first_only");
                    if(!is_array($row_check)){
                        $updatee = $this->quick_dbq("UPDATE internal_settings SET it_value='all' AND it_value='specific_user' AND prop_1='".hack_free($username)."' WHERE it_setting_name='APT'");
                        return ($updatee === "executed") ? true : false;
                    }
                break;
                case "debug":
                break;
                default:
                break;
            }
        }
        public function apt($regarding,$message,$typeof_process){  global $user_dt;
            $apt_status = [
                $this->quick_dbq("SELECT * FROM internal_settings WHERE it_setting_name='APT'","first_only","it_value"),
                $this->quick_dbq("SELECT * FROM internal_settings WHERE it_setting_name='APT'","first_only","prop_1")
            ];                  
            switch($apt_status[0]){
                case "admin_only":
                    $checkz = ($user_dt["db_affinity"] === "0" || $user_dt["db_position"] === "admin") ? true : false;
                break;
                case "all":
                    $checkz = true;
                break;
                case "specific_user":
                    $checkz = ($apt_status[1] === $user_dt["username"]) ? true : false; 
                break;   
                case "range":
                    //i'll work on this later...
                break;
                default:
                  
                break;
            }
            if(!isset($checkz)){
                $checkz = false;
            }
            if($checkz){
                $z = $this->quick_dbq("INSERT INTO apt(function_name,message,typeof_process) VALUES('".hack_free($regarding)."','".hack_free($message)."','".hack_free($typeof_process)."')");
                if($z === "executed"){
                    return true;
                }else{
                    return $z;
                }
            }  
        }
    }
    $preset = new preset();
    $extra_commands = new extra_commands();
    $extra_commands->initialize_APT("admin_only");
    function å($regarding,$message,$typeof_process){ global $extra_commands;
        $extra_commands->apt($regarding,$message,$typeof_process);
    }

?>