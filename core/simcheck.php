<?php


session_start();     
ob_start();
require_once("directory_check.php");
require_once("auxiliary.php");
require_once("template/template_default.php");
require_once("template/text.php");
require_once("text_internal.php");
if(isset($_SESSION['logged_q'],$_GET["action"]) && ($user_dt['uid'] === "1" || $user_dt['uid'] === "0")){ //root admin shit only
    switch($_GET['action']){
        case "db_search_ad":
            if(isset($_GET['search_q'])){
                $get_dbs = $quick_commands->quick_dbq("SELECT * FROM dbz WHERE id LIKE '%$_FILTERED[search_q]%' OR name LIKE '%$_FILTERED[search_q]%'","all");        
                if(is_array($get_dbs) && count($get_dbs) > 0){
                    for($i=0 ; $i <= count($get_dbs) -1; $i++){           
                        echo "<a href='javascript:void(0)' class='dropdown_opt spec ra_db_slct' ref='".$get_dbs[$i]['dbid']."'>
                        <div class='dd_block'><h3>".$get_dbs[$i]['name']."</h3>
                        <strong>".$text['date_created']."</strong> ". $get_dbs[$i]['date_created'] ."
                        </div></a>";
                    }
                }
            }
        break;  //end db_search_ad
        case "admin_db_save":
            $_SESSION['ra_db_selected'] = $_FILTERED['db_id']; 
            
        break;
    }
}

if(isset($_GET['action'])){ //applies to everyone      
    switch($_GET['action']){
        case "fetch":
            if(isset($_GET['fetch'])){
                switch($_GET['fetch']){
                    case "user_data":            
                        $has_db_selected = isset($_SESSION['ra_db_selected']) ? true : false;
                        echo json_encode(["user_details" => isset($user_dt) ? $user_dt : "none", "has_selected_db" => $has_db_selected]);
                    break;             
                }
            }
        break;
        case "declare_timestamp":
            $_SESSION['offset'] = 0 - (intval($_GET['offset']) * 60);
        break;
        case "filler_request":
            if(isset($_GET['filler'])){
                switch($_GET['filler']){
                    case "signup":
                        ?>
                        <h4>Sign up at TheContainer!</h4>
                        <h5>Username</h5><input type="text" name="username2"><h5>Password</h5><input type="password" name="password2"><h5>Email Address</h5><input type="text" name="email2"><div class="option">I am registering into a database. <input type="checkbox" name="reginto2"></div>
                                                
                        <?php
                    break;
                }
            }
        break;
    }
}

if(isset($_SESSION['logged_q'],$_GET["action"])){ //function for logged users
    switch($_GET['action']){
        case "get_latest_note":  
            if(isset($_SESSION['new_note'])){
                $json = $quick_commands->quick_dbq("SELECT * FROM notes WHERE note_id='$_MONITORED[new_note]'");
                $json["note_date"] = $preset->dflt_dfmt($json['note_date']);
                unset($_SESSION['new_note']);
                echo json_encode($json);    
            }
        break;
        case "submit_new_note":
            if(isset($_POST['note']) && strlen($_POST['note']) < 1500){
               
                $submit = $quick_commands->quick_dbq("INSERT INTO notes(note_by,note,note_date) VALUES($user_dt[uid],'$_SPIN[note]',UNIX_TIMESTAMP())");
                $_SESSION['new_note'] = $quick_commands->quick_dbq("SELECT * FROM notes WHERE note_by=$user_dt[uid] ORDER BY note_date DESC","first_only","note_id");
                echo $text['submitted'];
            }else{
                if(strlen($_POST['note']) > 1500){
                    echo $text['too_long'];
                }
            }
        break;
        case "json_req":
            switch($_GET['dt']){
                case "table_search":
                    $results = $quick_commands->quick_dbq("SELECT * FROM dbform LEFT JOIN dbz ON dbid=dbid_ref WHERE table_name LIKE '%$_FILTERED[table_name]%' AND is_intangible ='true'","all");
                    echo json_encode(($results === "none") ? ["results" => "none"] : ["results" => $results]);
                break;
            }
        break;
        case "confirm":
            if(isset($_SESSION['current_queue'])){
                preg_match("#^(edit|delete)_(n|ad)([0-9]+)$#",$_SESSION['current_queue'],$parsed);
                if(count($parsed) === 4){
                    $action = ($parsed[1] === "delete") ? "DELETE" : "INSERT INTO";
                    $identifier = ($parsed[2] === "n") ? ["notes","note_id"] : ["actual_data","ad_id"];
                    $user_id = ($parsed[2] === "n") ? " AND note_by='$user_dt[uid]'" : "";
                    $check_dt = $quick_commands->quick_dbq("SELECT * FROM $identifier[0] WHERE $identifier[1] = '$parsed[2]'$user_id") !== "none";
                    $ad_pc = ($parsed[2] === "n") ? true : $quick_commands->perm_check("SCAN2",$parsed[1],"edit");
                    if($check_dt && $ad_pc){
                        switch($parsed[1]){
                            case "edit":
                                
                            break;
                            case "delete":
                                
                            break;
                        }
                    }
                }
                echo json_encode(["message" => $parsed]);
            }    
        break; //end confirm
        case "fetch":   
            $query = "";                  
            switch($_GET['fetch']){
                
                case "advanced_adfetch":
                    /*
                        GET search_parsing = type of search
                        GET search_query = statement to match or match against
                        ... search_by_column(optional)
                        ... order_by_column
                        ... sort_by descending or ascending order
                        then ofc $_GET["id"] and $_GET['column']
                        search_parsing:searchh.parsing,
                search_query:searchh.query,
                search_by_column:searchh.slctr,
                sort_by:searchh.order_by,
                id:id,
                sort:searchh.sort,
                    */
                    
                    
                    
                   
                    if($_GET['search_by_column'] !== "all" || $_GET['order_by_column'] !== "none"){
                        if($_GET['order_by_column'] !== "none"){
                            $col_o_data = $extra_commands->dbc_scout($_FILTERED['order_by_column'],"native","info");
                            
                        }
                        if($_GET['search_by_column'] !== "all"){        
                            $col_s_data = $extra_commands->dbc_scout($_FILTERED['search_by_column'],"native","info");
                            //$quick_commands->var_dump($_FILTERED['search_by_column'],$col_s_data); 
                            if($col_s_data["type"] === "selection"){
                                $typeof_search = "selection";
                                switch($_GET['search_parsing']){
                                    case "none":
                                        $marks = ["",""];
                                    break;
                                    case "begins_with":
                                        $marks = ["^",""];
                                    break;
                                    case "ends_with":
                                        $marks = ["","$"];
                                    break;
                                }
                                foreach($col_s_data["selection"] as $key=>$val){
                                    $regex_check = preg_match("#".$marks[0].$_GET['search_query'].$marks[1]."#",$val["dbc_name"]);
                                    $regex_check = ($_GET['search_parsing'] === "excludes") ? !$regex_check : $regex_check;
                                    if($regex_check === true){
                                        $array_list[] = $val["dbcid"];
                                    }
                                } 
                            }else{
                                $typeof_search = "string";
                            }                    
                        }
                    }
                    $typeof_search = "string";
                    $col_checks = isset($col_checks) ? $col_checks : true;                  
                    $sort = (isset($_GET['sort']) && $_GET['sort'] === "DESC") ? "DESC" : "ASC";
                    if($extra_commands->perm_check("SCAN","view",$_FILTERED['id']) && $col_checks){ 
                        
                        $needs_others = ($_GET['search_by_column'] === "all");
                        switch($_GET['search_parsing']){
                            case "none":
                                //switch()
                                $sql_query = $needs_others ? "SELECT * FROM actual_data ad1 WHERE ad1.row_value LIKE '%$_FILTERED[search_query]%' AND dbf_ref='$_FILTERED[id]' ORDER BY ad1.ad_ref,ad1.dbc_ref ASC" : "SELECT * FROM actual_data ad1 WHERE ad1.dbc_ref='$_FILTERED[search_by_column]' AND ad1.row_value LIKE '%$_FILTERED[search_query]%' ORDER BY ad1.ad_ref,ad1.dbc_ref ASC";
                            break;
                            case "begins_with":
                                $sql_query = $needs_others ? "SELECT * FROM actual_data ad1 WHERE ad1.row_value LIKE '$_FILTERED[search_query]%' AND dbf_ref='$_FILTERED[id]' ORDER BY ad1.ad_ref,ad1.dbc_ref ASC" : "SELECT * FROM actual_data ad1 WHERE ad1.dbc_ref='$_FILTERED[search_by_column]' AND ad1.row_value LIKE '$_FILTERED[search_query]%' ORDER BY ad1.ad_ref,ad1.dbc_ref ASC";
                            break;
                            case "ends_with":
                                $sql_query = $needs_others ? "SELECT * FROM actual_data ad1 WHERE ad1.row_value LIKE '%$_FILTERED[search_query]' AND dbf_ref='$_FILTERED[id]' ORDER BY ad1.ad_ref,ad1.dbc_ref ASC" : "SELECT * FROM actual_data ad1 WHERE ad1.dbc_ref='$_FILTERED[search_by_column]' AND ad1.row_value LIKE '%$_FILTERED[search_query]' ORDER BY ad1.ad_ref,ad1.dbc_ref ASC";
                            break;
                            case "excludes":
                                $sql_query = $needs_others ? 
                                "SELECT * FROM actual_data ad1 WHERE NOT EXISTS(SELECT * FROM actual_data ad2 WHERE row_value LIKE '%$_FILTERED[search_query]%' AND ad1.ad_ref=ad2.ad_ref) AND ad1.dbf_ref='$_FILTERED[id]' AND ad1.ad_ref != ad1.ad_id ORDER BY ad1.ad1.ad_ref,ad1.dbc_ref ASC" 
                                : 
                                "SELECT * FROM actual_data ad1 WHERE ad1.dbc_ref='$_FILTERED[search_by_column]' AND NOT EXISTS(SELECT * FROM actual_data ad2 WHERE row_value LIKE '%$_FILTERED[search_query]%' AND ad1.ad_ref=ad2.ad_ref) AND ad1.dbf_ref='$_FILTERED[id]' AND ad1.ad_ref != ad1.ad_id ORDER BY ad1.ad_ref,ad1.dbc_ref ASC";
                            break;
                        }
                        
                  
                        $schema = $extra_commands->db_schema($_FILTERED['id']);
                        $data1 = $extra_commands->get_data($sql_query,"scan");
                        
                        $order_by = ($_GET['order_by_column'] !== "none") ? $_FILTERED['order_by_column'] : "none";
                        
                        
                        if($data1 !== false){ 
                            $extra_commands->data_table($_FILTERED['id'],$schema["columns"],$data1,$order_by,$sort);   
                        }else{  
                            echo $text['n0_results'];
                        }                    
                       
                      
                    } 
                break;
            }
        break; //end advanced_adfetch
        case "cmd":           
            $is_valid = false;
            switch($_GET['cmd']){
                case "modify_cell":
                    switch($_GET['task']){
                        case "delete_cell":
                            switch($_GET['type']){
                                case "notes":
                                    $note_data = $quick_commands->quick_dbq("SELECT * FROM notes WHERE note_id='$_FILTERED[cell]' AND note_by='$user_dt[uid]'");
                                    if($note_data !== "none"){
                                        $data_idee = "note #".$note_data['note_id'];
                                        $is_valid = true;
                                        $label = "n";         
                                    }
                                break;
                                case "none":
                                    $cell_data = $extra_commands->a_d_snippet($_GET['cell']);  
                                    if($cell_data !== "none"){
                                        $data_idee = "data #".$_FILTERED['cell'];
                                        $is_valid = true;
                                        $label = "ad";
                                        
                                    }
                                break;
                            }
                            if($is_valid === true){
                                $_SESSION['current_queue'] = "delete_$label$_FILTERED[cell]";
                                $_SESSION['hash'] = substr(md5(microtime()),0,25);
                                echo $text['delete_prompt'].$data_idee."   <button class='submit_clone spec2' ref='confirm' rel='$_SESSION[hash]'>$text[c0nfirm]</button>";
                            }
                        break;   
                    }
                    if(isset($_GET['type'],$_GET['cell'])){
                        switch($_GET['type']){
                            case "notes":
                                //notes dont get HTML tag stripping, it is posted as-is. The display converts it.
                                $note_data = $quick_commands->quick_dbq("SELECT * FROM notes WHERE note_id='$_FILTERED[cell]'");
                                switch($_GET['task']){
                                    case "edit_cell":
                                        $_SESSION['current_queue'] = "edit_n$_FILTERED[cell]";
                                        $_SESSION['hash'] = substr(md5(microtime()),0,25);
                                        $message = preg_replace("#<br>#","\n",$note_data["note"]);
                                        $tags = ["<textarea cell_id='".intval($_GET['cell'])."'>","</textarea>"]; 
                                        $message = preg_replace("#<[ \t]{0,}/textarea[ \t]{0,}>#",stripslashes("</textarea>"),$message);
                                        
                                        echo $tags[0].$message.$tags[1];
                                        echo "<button class='submit_clone spec2' ref='confirm' rel='$_SESSION[hash]'>$text[finish_edits]</button>";
                                    break;
                                }
                            break;
                            case "none": 
                            switch($_GET['task']){
                                case "edit_cell":  
                                    $_SESSION['current_queue'] = "edit_ad$_FILTERED[cell]"; 
                                    $_SESSION['hash'] = substr(md5(microtime()),0,25); 
                                    $cell_data = $extra_commands->a_d_snippet($_GET['cell']);        
                                    $message = $cell_data[$_GET['cell']]['row_value'];   
                                    if($extra_commands->perm_check("SCAN","edit",$cell_data[$_GET['cell']]['dbf_ref']) === true){
                                        $column_info = $cell_data[$_GET['cell']]["column_info"];
                                        //var_dump($column_info);
                                        switch($column_info["type"]){
                                            case "selection":
                                                $selected = intval($message);
                                                echo "<select rel='".intval($_GET['cell'])."'>"; 
                                                foreach($column_info["selection"] as $index => $sel_info){
                                                    $select_in = ($selected === intval($sel_info['dbcid'])) ? " selected='selected'" : "";
                                                    echo "<option value='".intval($sel_info['dbcid'])."'$select_in>$sel_info[dbc_name]</option>";
                                                }
                                                echo "</select>";
                                            break;
                                            default:
                                                $maxlength = intval($column_info["maxlength"]);
                                                switch($column_info["type"]){
                                                    case "integer":
                                                        $regex = $regex_check["integer"];
                                                    break;
                                                    case "string":
                                                        $regex = "^.{2,".intval($column_info['maxlength'])."}$";
                                                    break;
                                                }
                                                $tags =  ($maxlength > 150) ? ["<textarea cell_id='".intval($_GET['cell'])."' ref='regex_check' rel='/".$regex."/' class='spec5'>","</textarea>"] : ["<input type=\"text\" ref='regex_check' rel='/".$regex."/' class='spec5' cell_id=\"".intval($_GET['cell'])."\" value=\"","\">"] ;
                                                
                                                
                                                $message = ($maxlength > 150) ? preg_replace("#<[ \t]{0,}/textarea[ \t]{0,}>#",stripslashes("</textarea>"),$message) : $message;
                                                $output = ($maxlength > 150) ? $message : preg_replace("#[\"]#","&quot;",$message); 

                                                echo $tags[0].$output.$tags[1];
                                                
                                            break;
                                        }  
                                        
                                        echo "<button class='submit_clone spec2' ref='confirm' rel='$_SESSION[hash]'>$text[finish_edits]</button>";
                                        
                                    }
                                 break;
                                 }
                            }
                        break;
                    }
                break;
                case "delete_something":
                    if(isset($_SESSION['promptz'][$_GET['type']][$_GET['num']])){
                    
                        unset($_SESSION['promptz'][$_GET['type']][$_GET['num']]);
                        $w = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbcid='$_FILTERED[num]'");
                        
                        $x = $quick_commands->quick_dbq("DELETE FROM db_columns WHERE dbcid='$_FILTERED[num]'");
                        if($x === "executed"){
                            echo $text["deltd"];
                        }else{
                            echo $x;
                        }
                    }else{
                        $_SESSION['promptz'][$_GET['type']][$_GET['num']] = "one";  
                        echo $text["prompt"];    
                    }
                break;
            }
        break;
        case "quick_submit":      
            if(filter_input(INPUT_SERVER, 'REQUEST_METHOD' ) === "POST"){   
                switch($_GET['req']){
                    case "new_dbform": 
                        
                        å("new_dbform","creating new database form: " . $user_dt['username'],"initialization");
                        //0 = all default personal information columns
                        //settings = self-explanatory
                        //1+: all custom columns
                        //if intangible is set to true, get the selected intangible column
                        //    - properties: column name, type of data, default value, is editable
                        //        - if selection: list all selection
                        //        - if datetime: get UNIX timestamp
                        
                        $props = $_DATA['settings'];           
                        $beginning_num = isset($_POST[0]) ? 0 : 1;     
                        //var_dump($_POST,count($_POST));
                        
                        //make sure they have the authority to do this first!!
                        $statement = "INSERT INTO dbform(table_name,admin,is_editable,is_intangible,dbid_ref) VALUES('$props[form_name]','$_MONITORED[logged_q]','$props[is_editable]','$props[is_intangible]','$_MONITORED[ra_db_selected]')";  
                        å("new_dbform","created new table: " . $user_dt['username'],"initialization");
                        $dbf = $quick_commands->quick_dbq($statement,"execute","none",true)->create_reference("check");  
                                                                    
                        $error = "";
                        if($quick_commands->ref("check") === "executed"){
                            $dbf_id = $quick_commands->quick_dbq("SELECT * FROM dbform WHERE dbid_ref ='$_MONITORED[ra_db_selected]' ORDER BY dbfid DESC","first_only","dbfid");                                  
                        å("new_dbform","added new process: " . $user_dt['username'],"organization");          
                        //if($error !== ""){
                        for($i = $beginning_num; $i <= count($_POST) -1 ; $i++){
                            if(preg_match("#".$regex_check['column_name']."#",$_DATA[$i]["dt".$i."_name"]) === 0){
                            
                                if($props["is_intangible"] === "true" && $i === intval($_DATA["intangible_ref"])){
                                    $marked = $i;
                                }
                                
                                if("true" !== $_DATA[$i]['dt'.$i.'_is_editable2']){$_DATA[$i]['dt'.$i.'_is_editable2'] = "";}
                                if("true" !== $_DATA[$i]['dt'.$i.'_is_required2']){$_DATA[$i]['dt'.$i.'_is_required2'] = "false";}
                                                                                          
                                    switch($_POST[$i]['dt'.$i.'_type1']){
                                        case "selection": 
                                            $new_slct_ref = $quick_commands->quick_dbq("INSERT INTO db_columns(dbtid_ref,dbc_name,type,maxlength,permissions,has_intangible,is_required,is_editable) VALUES('$dbf_id','".$_DATA[$i]["dt".$i."_name"]."','selection','130','3','0','".$_DATA[$i]['dt'.$i.'_is_required2']."','".$_DATA[$i]['dt'.$i.'_is_editable2']."')","execute","none");
                                              
                                        //then get the ID of the new selection reference
                                            $dbcid = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE type='selection' AND dbtid_ref='$dbf_id' ORDER BY dbcid DESC","first_only","dbcid");
                                            
                                            $x = 0;
                                            if($new_slct_ref === "executed"){

                                                foreach ($_DATA[$i] as $key => $val){
                                                    if(preg_match("#selection[0-9]+$#",$key)){
                                                        if(preg_match("#".$regex_check['selection']."#",$val) === 1 || $x > 26){ //matching against certain strings
                                                            $error .= $text_i['selection_error'] . "\n";
                                                            if($x > 25){
                                                                $error .= "Too much selections. You are limited to 25 selections.";
                                                                
                                                            }else{
                                                                
                                                            }
                                                            
                                                        } 
                                                        //selection choices will be classified as type "choice"
                                                        //database column id will be under "maxlength" cuz im lazy
                                                        $submit = $quick_commands->quick_dbq("INSERT INTO db_columns(dbtid_ref,dbc_name,type,maxlength,permissions,has_intangible) VALUES('$dbf_id','".$val."','choice','$dbcid','3','0')");    
                                                        å("new_dbform","added choice(under $dbf_id, selection id $dbcid): " . $user_dt['username'],"organization");            
                                                    }                                                           
                                                }       
                                            }
                                        break;
                                        case "string":
                                            if($_DATA[$i]['dt'.$i.'_defval'] === "Default Value"){ $_DATA[$i]['dt'.$i.'_defval'] = " ";}
                                            if(preg_match("#".$regex_check['string']."#",$_DATA[$i]["dt".$i."_defval"]) === 0){
                                                $error .= $text_i['string_error']. "\n";
                                                
                                            }
                                            else{
                                                $submit = $quick_commands->quick_dbq("INSERT INTO db_columns(dbtid_ref,dbc_name,type,maxlength,permissions,has_intangible,defval,is_required,is_editable) VALUES('$dbf_id','".$_DATA[$i]['dt'.$i.'_name']."','string','130','3','0','".$_DATA[$i]['dt'.$i.'_defval']."','".$_DATA[$i]['dt'.$i.'_is_required2']."','".$_DATA[$i]['dt'.$i.'_is_editable2']."')");  
                                            }
                                        break;
                                        case "paragraph":
                                            if($_DATA[$i]['dt'.$i.'_defval'] === "Default Value"){ $_DATA[$i]['dt'.$i.'_defval'] = " ";}
                                            if(preg_match("#".$regex_check['paragraph']."#",$_DATA[$i]["dt".$i."_defval"])){
                                                $error = $text_i['paragraph_error']. "\n";
                                                
                                            }else{
                                                    $submit = $quick_commands->quick_dbq("INSERT INTO db_columns(dbtid_ref,dbc_name,type,maxlength,permissions,has_intangible,is_required,is_editable) VALUES('$dbf_id','".$_DATA[$i]['dt'.$i.'_name']."','string','25000','3','0','".$_DATA[$i]['dt'.$i.'_defval']."','".$_DATA[$i]['dt'.$i.'_required2']."','".$_DATA[$i]['dt'.$i.'_is_editable2']."')");  
                                            }
                                        break;
                                        case "int":
                                            if($_POST[$i]['dt'.$i.'_defval'] === "Default Value") { $_DATA[$i]['dt'.$i.'_defval'] = 0;}
                                            if(!preg_match("#".$regex_check['integer']."#",$_DATA[$i]["dt".$i."_defval"])){
                                                $error .= $text_i['integer_error']. "\n";  
                                            }else{
                                                $submit = $quick_commands->quick_dbq("INSERT INTO db_columns(dbtid_ref,dbc_name,type,maxlength,permissions,has_intangible,defval,is_required,is_editable) VALUES('$dbf_id','".$_DATA[$i]['dt'.$i.'_name']."','integer','130','3','0','".$_DATA[$i]['dt'.$i.'_defval']."','".$_DATA[$i]['dt'.$i.'_is_required2']."','".$_DATA[$i]['dt'.$i.'_is_editable2']."')");  
                                            }
                                        break;
                                        case "datetime":
                                            if(!preg_match("#".$regex_check['datetime_format']."#",$_DATA[$i]["dt".$i."_defval"])){
                                                $error .= $text_i['datetime_error']. "\n";
                                                
                                            }else{
                                                $submit = $quick_commands->quick_dbq("INSERT INTO db_columns(dbtid_ref,dbc_name,type,maxlength,permissions,has_intangible,is_required,is_editable) VALUES('$dbf_id','".$val."','datetime','250','3','0','".$_DATA[$i]['dt'.$i.'_is_required2']."','".$_DATA[$i]['dt'.$i.'_is_editable2']."')");  
                                            }
                                        break;
                                        case "intangible":
                                            //i'll do this later
                                        break;
                                    }
                                    å("new_dbform","added new database column(under $dbf_id): " . $user_dt['username'],"organization"); 
                                    if(isset($marked)){
                                        $numm = $quick_return->quick_dbq("SELECT * FROM db_columns WHERE dbtid_ref = '$_MONITORED[ra_db_slct]' ORDER BY dbcid DESC","first_only","dbcid");
                                        if(gettype($numm) === "integer"){
                                            $update_dbf = $quick_return->quick_dbq("UPDATE dbform SET intangible_dbf = '$numm'");
                                            if($update_dbf === "executed"){
                                                å("new_dbform","set intangible for new table: " . $user_dt['username'],"organization"); 
                                            }
                                        }
                                    }
                                  
                                
                                }else{
                                    $error .= $text_i['cname_error'];
                                }
                            }
                            
                            $get_recent = [
                                $quick_commands->quick_dbq("SELECT * FROM dbform WHERE admin='$_MONITORED[logged_q]' ORDER BY dbfid DESC")
                            ];
                            
                            if(is_array($get_recent)){
                                
                            }
                            
                            //if it got this far and there's no $error it means there's no problems.
                            if($error !== ""){
                                echo $error;
                            }else{
                                echo $text_i['new_table'];
                                $_SESSION['new_dbf'] = true;
                            } 
                        }else{
                            $error = $quick_commands->ref("check");
                        }
                    break; //end new_dbform 
                    case "submit_dtee": 
                        //var_dump($_POST);
                        å("submit_dtee","begin data submission for " . $user_dt['username'],"initialization"); 
                        
                        $regex = "#^inputa([0-9]+)_([0-9]+)$#";
                        
                        foreach($_DATA as $key => $val){
                            $dataa_id = [
                                preg_replace($regex,"$1",$key),
                                preg_replace($regex,"$2",$key),
                            ];
                            $dbf_dt = $quick_commands->quick_dbq("SELECT * FROM dbform WHERE dbfid='$dataa_id[0]'","first_only"); 
                            $is_intangible = $dbf_dt['is_intangible'] === "true" ? true : false ;
                            $idbf = intval($dbf_dt['intangible_dbf']);
                            $idbf_name = ($dbf_dt['is_intangible'] === "true") ? $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbcid='$idbf'","first_only","dbc_name") : "";
                            if($extra_commands->perm_check("SCAN","manage",$dataa_id[0])){
                                
                                $check = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbcid='$dataa_id[1]' AND dbtid_ref='$dataa_id[0]'");
                                if(is_array($check)){
                                    $errors = "";
                                    //submit data
                                    å("submit_dtee","column recognition successful: " . $user_dt['username'],"organization"); 
                                    
                                    if(($idbf === $dataa_id[1] && $this->get_data($dataa_id[1],"duplicate_check",$val)) || $idbf !== $dataa_id[1]){
                                    
                                    if(!isset($has_reserve)){
                                        $reserve = $quick_commands->quick_dbq("INSERT INTO `actual_data` (`dbc_ref`, `row_value`,  `ad_ref`, `dbf_ref`, `user_ref`, `timestamp`) VALUES ('$dataa_id[1]', 'reserve', -2, '$dataa_id[0]', '$user_dt[uid]', UNIX_TIMESTAMP())");
                                        $dee3 = $quick_commands->quick_dbq("SELECT * FROM actual_data WHERE user_ref='$user_dt[uid]' ORDER BY ad_id DESC","first_only","ad_id");
                                        $has_reserve = "";
                                        $reserve2 = $quick_commands->quick_dbq("UPDATE actual_data SET ad_ref = '$dee3' WHERE ad_ref='-2' AND user_ref='$user_dt[uid]'");
                                        //var_dump($reserve,$dee3,$reserve2);
                                        å("submit_dtee","reserved actual_data reference: " . $user_dt['username'],"organization"); 
                                    }
                                    if(isset($has_reserve)){
                                        $inputs = $quick_commands->quick_dbq("INSERT INTO `actual_data` (`dbc_ref`, `row_value`,  `ad_ref`, `dbf_ref`, `user_ref`, `timestamp`) VALUES ('$dataa_id[1]', '".hack_free($val,'htmlspecialchars')."', '$dee3', '$dataa_id[0]', '$user_dt[uid]', UNIX_TIMESTAMP())");
                                    }else{
                                        $errors = $reserve. "\n";
                                    } 
                                    }else{
                                        $errors .= "Duplicate value exists.";
                                    }
                                                                           
                                }
                            }   
                        }
                        
                        
                        if($errors === ""){ 
                            å("submit_dtee","Successful data submission(no errors) : " . $user_dt['username'],"completion");      
                        }else{ //has to be deleted lol. I know it's inefficient but whatever
                            $quick_commands->quick_dbq("DELETE * FROM actual_data WHERE ad_ref='$dee3'");
                            å("submit_dtee","Submission error(s): ".hack_free($errors),"completion");     
                            echo $errors; 
                        }
                        
                    break;  //finish submit_new_dt
                    case "finish_edits":
                        /*  glossary:
                    ---------------------------------------------------
                            table_{int}_name: options
                            dbcn_{int}: new column properties
                            
                            dbc_{int}: current columns
                            dbc_{int}_slct_{int2}: current selection  
                            dts{int}_selection{int2}: new selection on current column
                            dtc{int}_selection{int2}: new selection on new column
                            dtc{int}_intlink: new intangible data on new column
                        */                                      
                        
                        å("finish_edits","begin finish edits for ". $user_dt['username'],"initialization");
                        
                        $regex = "#^(dbc|dts|dtc|table|dbcn)_?([0-9]+)_(slct|defval|selection|intlink|name|opt|def_open|type)_?([0-9]{0,})$#";
                        foreach($_DATA as $key => $val){
                            $key_pair = [
                                preg_replace($regex,"$1",$key),
                                preg_replace($regex,"$2",$key),
                                preg_replace($regex,"$3",$key),
                                preg_replace($regex,"$4",$key)
                            ];
                            if($key_pair[3] !== ""){
                                $pairz[$key_pair[0]][$key_pair[1]][$key_pair[2]][$key_pair[3]] = $val;
                            }else{
                                $pairz[$key_pair[0]][$key_pair[1]][$key_pair[2]] = $val;
                            }
                        }
                        
                        å("finish_edits","finish organization of keys: " . $user_dt['username'],"organization");
                        
                        if($extra_commands->perm_check("SCAN","manage",$_GET["id"])){ 
                            $error = "";
                            $latest_id = []; 
                            if(isset($pairz["dbcn"])){  //echo "a";
                            foreach($pairz["dbcn"] as $id => $class){   //echo "b";
                                if(is_array($class)){  //(int|string|selection|paragraph|intangible|datetime
                                    if(preg_match("#".$regex_check["data_types"]."#",$class["type"])){
                                        //echo "c";
                                        if(count($quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbc_name = '$class[name]' AND dbtid_ref='$_FILTERED[id]'")) === 0){   
                                        switch($class["type"]){ //change the values and declare errors
                                            case "string":
                                            case "paragraph":   
                                            case "selection":
                                            //echo "d";
                                                if(preg_match("#".$regex_check["defval_check"]."#",$class["defval"])){
                                                    $class["defval"] = " ";   
                                                }
                                                if(preg_match("#^[ \t]{0,}$#",$class["name"])){
                                                    $error .= "Name Error.\n";
                                                    å("finish_edits","name error: " . $user_dt['username'],"error");
                                                }    
                                            break;
                                            case "int":
                                                if(preg_match($regex_match["defval_check"],$class["defval"])){
                                                    $class["defval"] = 0;
                                                }
                                            break;
                                            case "intangible":
                                                if($class["defval"] === $text["select_table"]){
                                                    $error .= $text_i["selection_error"] . "\n";
                                                    å("finish_edits","selection error: " . $user_dt['username'],"error");
                                                }
                                            break;
                                            case "datetime": //i'll deal with this later
                                                
                                            break;
                                            
                                        }
                                        }else{
                                            $error .= "Column Name already used by another column in this table.";
                                        }
                                        if($error === ""){     //echo "e";
                                            $new_col = $quick_commands->quick_dbq("INSERT INTO db_columns(dbc_name,type,defval,dbtid_ref) VALUES('$class[name]','$class[type]','$class[defval]','$_FILTERED[id]')");
                                            å("finish_edits","inserted columns on finish_edits: " . $user_dt['username'],"submission");
                                            if($new_col === "executed" && $class["type"] === "selection"){     //echo "f";
                                                $latest_id[$id] = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE type='selection' AND dbtid_ref='$_FILTERED[id]' ORDER BY dbcid DESC","first_only","dbcid");
                                                if($latest_id[$id] === false){ $error .= "Selection Column Search Error" . "\n";
                                                å("finish_edits","selection column search error: " . $user_dt['username'],"error");
                                                }
                                            }
                                            
                                            if($new_col !== "executed"){
                                                $error .= $new_col;
                                                å("finish_edits","SQL error($error): " . $user_dt['username'],"error");
                                            }
                                        }
                                    }
                                }
                            }
                              
                            }
                            
                            if(isset($pairz["dts"])){
                            
                            å("finish_edits","has new selections on existing column: " . $user_dt['username'],"organization"); 
                            
                            foreach($pairz["dts"] as $id => $obj){
                                if(preg_match("#^[0-9]+$#",$id)){
                                $check_3 = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbtid_ref='$_FILTERED[id]' AND dbcid='$id'","all");
                                if(is_array($check_3) && is_array($obj)){
                                    foreach($obj as $pro => $proo){
                                        foreach($proo as $num => $prop){
                                        $check_4 = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE type='choice' AND dbtid_ref='$_FILTERED[id]' AND dbc_name='$prop'");
                                        if($prop !== "Selection"){
                                        if(!preg_match("#".$regex_check['selection']."#",$prop) && !is_array($check_4)){
                                        $submit_selections = $quick_commands->quick_dbq("INSERT INTO db_columns(dbc_name,type,maxlength,dbtid_ref) VALUES('$prop','choice','$id','$_FILTERED[id]')");
                                        if($submit_selections !== "executed"){
                                            $error .= $submit_selections."\n";
                                            å("finish_edits","SQL error on submission(".hack_free($submit_selections)."): " . $user_dt['username'],"organization"); 
                                        }else{
                                            å("finish_edits","New Column inserted: " . $user_dt['username'],"completion"); 
                                        }
                                    }else{
                                        å("finish_edits","new selection submission fail: " . $user_dt['username'].";\n".hack_free($error),"organization"); 
                                        $error = preg_match("#".$regex_check['selection']."#",$prop) ? $error ."Selection must not be more than 25 characters long, or be left blank.\n" : $error;
                                        
                                        $error = is_array($check_4) ? $error . "This selection name is already in an option in this particular selection. \n" : $error;
                                    } 
                                    }
                                        }
                                    }
                                }
                                }
                            }
                            
                            }
                            
                            if(isset($pairz["dtc"])){    
                            å("finish_edits","has new selection/intlink on NEW column: " . $user_dt['username'],"organization"); 
                            
                            foreach($pairz["dtc"] as $id => $class){
                                if(isset($latest_id)){
                                    foreach($ids as $ideee => $propz){
                                        if(is_array($propz)){
                                        foreach($propz as $typeofdt => $propz2){
                                            switch($typeofdt){
                                                case "intlink":
                                                    å("finish_edits","has intlink on new column: " . $user_dt['username'],"organization"); 
                                                break;
                                                case "selection":
                                                    $check_3 = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbtid_ref='$_FILTERED[id]' AND dbcid='".$latest_id[$ids]."'");   
                                                    if(count($check_3) === 1){
                                                       
                                                        foreach($propz2 as $ke => $name){
                                                            $submit = $quick_commands->quick_dbq("INSERT INTO db_columns(dbc_name,type,maxlength,dbtid_ref) VALUES('$name','choice','".$latest_id[$ids]."','$_FILTERED[id]')"); 
                                                            if($submit !== "executed"){
                                                                $error .= $submit ."\n";
                                                                å("finish_edits","SQL error(".hack_free($submit)."): " . $user_dt['username'],"error"); 
                                                            }   
                                                        }
                                                    }
                                                break;
                                            }
                                        }
                                        }                                        
                                    }
                                }
                            }  
                            
                            }
                            
                            if(isset($pairz["table"])){  
                            
                            å("finish_edits","updating table settings: " . $user_dt['username'],"organization"); 
                            
                            foreach($pairz["table"] as $db_id => $db_propz){
                                if(count($pairz["table"]) === 1){
                                    foreach($pairz["table"] as $eid => $eky){
                                        $eid = intval($eid);
                                        if($extra_commands->perm_check("SCAN","manage",$eid)){
                                            foreach($eky as $keey => $val){
                                                switch($keey){
                                                    case "name":
                                                        if(strlen($val) < 30){
                                                            $xee = $quick_commands->quick_dbq("UPDATE dbform SET table_name = '$val' WHERE dbfid='$_FILTERED[id]'");
                                                            if($xee !== "executed"){
                                                                $error .= $xee;
                                                            }
                                                        }
                                                        else{
                                                            $error .= "Column Name too long. 30 characters maximum.\n";
                                                            å("finish_edits","Table Name Updated for table edit: " . $user_dt['username'],"completion"); 
                                                        }
                                                    break;
                                                    case "opt":
                                                        $new_perm = "";
                                                        ksort($val,SORT_DESC);
                                                        
                                                        foreach($val as $nums => $nuum){
                                                            if(intval($nuum) < 1 && intval($nuum) > 3){
                                                                $reset = true;
                                                            }
                                                            $new_perm .= $nuum;   
                                                        }
                                                        if(isset($reset)){
                                                            $new_perm = "3321";
                                                            unset($reset);
                                                        }
                                                        $xee = $quick_commands->quick_dbq("UPDATE dbform SET permission_val = '$new_perm' WHERE dbfid='$eid'");
                                                        if($xee !== "executed"){
                                                            $error .= $xee;
                                                        }else{
                                                            å("finish_edits","Permission Value updated for table edit: " . $user_dt['username'],"completion"); 
                                                        }
                                                    break;
                                                    case "def_open":
                                                        if($val === "false" || $val === "true"){
                                                            $xee = $quick_commands->quick_dbq("UPDATE dbform SET is_open = '$val' WHERE dbfid='$eid'");
                                                            if($xee !== "executed"){
                                                                $error .= $xee;
                                                            }
                                                        }
                                                    break;
                                                }
                                                
                                                if(isset($xee) && $xee !== "executed"){
                                                    å("finish_edits","SQL error(".hack_free($xee)."): " . $user_dt['username'],"error"); 
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            
                            }
                            
                            if(isset($pairz["dbc"])){
                            
                            å("finish_edits","modifying settings on current column: " . $user_dt['username'],"organization"); 
                                
                                            foreach($pairz["dbc"] as $idee => $valu){
                                                        
                                                        if(preg_match("#^[0-9]+$#",$idee) && is_array($valu)){ //echo "a";
                   
                                                            if(count($quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbcid='$idee' AND dbtid_ref='$_FILTERED[id]'")) > 0){
                                                            //echo "b";
                                                            foreach($valu as $properties => $valu2){//the properties themselves
                                               
                                                                switch($properties){
                                                                    case "selection":
                                                                    case "slct":
                                                                        if(is_array($valu2)){
                                                                            foreach($valu2 as $slct_id => $slct_val){ //selection name
                                                                                //echo "c";
                                                                                $table_check = ($user_dt["db_affinity"] === "0") ? true : is_array($quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbtid_ref='$slct_id' AND dbtid_ref='$user_dt[db_affinity]'"));
                                                                                
                                                                                if(preg_match("#^[0-9]+$#",$slct_id) && $table_check && !preg_match("#".$regex_check['selection']."#",$slct_val) && !is_array($quick_commands->quick_dbq("SELECT * FROM db_columns WHERE type='selection' AND dbc_name='$slct_val' AND dbcid != '$slct_id'"))){
                                                                                    //oecho "d";
                                                                                    $check_2 = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbcid='$slct_id' AND maxlength='$idee'");
                                                                                    $change_val = $quick_commands->quick_dbq("UPDATE db_columns SET dbc_name='".hack_free($slct_val)."' WHERE dbcid='$slct_id' AND maxlength='$idee'");
                                                                                    if($change_val !== "executed"){
                                                                                        $error .= $change_val . "\n";
                                                                                        å("finish_edits","SQL error on modifying column settings(".hack_free($change_val)."): " . $user_dt['username'],"error"); 
                                                                                    }else{
                                                                                        å("finish_edits","Column modified: " . $user_dt['username'],"completion");                                                                          
                                                                                    }
                                                                                }else{
                                                                                    if(!preg_match("#^[0-9]+$#",$slct_id)){
                                                                                        $error .= "Processing error.\n";
                                                                                    }else{
                                                                                        if(is_array($quick_commands->quick_dbq("SELECT * FROM db_columns WHERE type='selection' AND dbc_name='$slct_val' AND maxlength='$idee'"))){
                                                                                            $error .= "Selection Name already taken for this list of selects.\n";
                                                                                            å("finish_edits","Selection name already taken for this list of select ID $slct_id: " . $user_dt['username'],"error"); 
                                                                                        }
                                                                                        if(!$table_check){
                                                                                            $error .= "You cannot access this table.\n";
                                                                                            å("finish_edits","Table inaccessible to user: " . $user_dt['username'],"organization"); 
                                                                                        }
                                                                                    }   
                                                                                }
                                                                            } 
                                                                        }
                                                                    break;
                                                                    case "name":
                                                                        
                                                                        if(!preg_match("#".$regex_check['column_name']."#",$valu2) && !is_array($quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbc_name = '$valu2' and dbcid !=$idee"))){
                                                                            //echo "c";
                                                                            $xee = $quick_commands->quick_dbq("UPDATE db_columns SET dbc_name = '$valu2' WHERE dbcid ='$idee'");
                                                                            if($xee !== "executed"){
                                                                                $error .= $xee. "\n";
                                                                                å("finish_edits","SQL error on updating columns(".hack_free($xee)."): " . $user_dt['username'],"organization"); 
                                                                            }else{
                                                                                å("finish_edits","Finished updating column name: " . $user_dt['username'],"completion"); 
                                                                            }
                                                                        }
                                                                        else{   
                                                                            $error = preg_match("#".$regex_check['column_name']."#",$valu2) ? $error . "Column Name is too long. Must not be more than 50 characters, or left blank.\n" : $error;
                                                                            $error = (is_array($quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbc_name = '$valu2' and dbcid ='$idee'"))) ? $error . "Desired Column Name is already taken.\n" : $error;
                                                                            å("finish_edits","Column name is already taken at ID#$idee: " . $user_dt['username'],"error"); 
                                                                        }
                                                                    break;                 
                                                                    case "defval":
                                                                    if(!preg_match("#". $regex_check['defval_check'] ."#",$valu2)){
                                                                        $valu2 = "";
                                                                    }
                                                                        $xee = $quick_commands->quick_dbq("UPDATE db_columns SET defval = '$valu2' WHERE dbcid ='$idee'");
                                                                        if($xee !== "executed"){
                                                                            $error .= $xee. "\n";
                                                                            å("finish_edits","Has new selections on existing column: " . $user_dt['username'],"error"); 
                                                                        }else{
                                                                            å("finish_edits","Default Value updated(#$idee): " . $user_dt['username'],"completion"); 
                                                                        } 
                                                                        
                                                                    break;
                                                                }                 
                                                            }
                                                            }
                                                        }
                                                    }   
                                    
                            }   
                                        
                            if($error !== ""){
                                echo $error;
                            }else{
                                echo "Saved!";            
                                å("finish_edits","Editing finished on table: " . $user_dt['username'],"completion"); 
                            }            
                           
                            
                            /*foreach($pairz as $pairz_key => $typeof_edits){ //dbc,dts,dtc, etc on $pairz_key    
                                switch($pairz_key){
                                    
                                    case "dbcn":           //new columns
                                    case "dtc":
                                        if(is_array($class) && preg_match("#^[0-9]+$#",$id)){
                                            foreach($typeof_edits as $id => $class){
                                                if(is_array($class)){
                                                    foreach($class as $idee => $valu){
                                                        if(is_array($valu)){
                                                            foreach($valu as $properties => $valu2){
                                                                switch($properties){
                                                                    case "intlink":
                                                                    case "selection":
                                                                        
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    else{
                                                        switch($class){
                                                    
                                                        }
                                                    }
                                                }
                                                
                                            }
                                        }
                                    break;
                                    case "dbc":
                                    case "dts":
                                        $check_1 = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbcid='".hack_free($dbc['id'])."' AND dbtid_ref='$_FILTERED[id]'","all");
                                        if(count($check_1) === 1){
                                            foreach($typeof_edits as $id => $class){ //the IDs
                                                if(is_array($class) && preg_match("#^[0-9]+$#",$id)){                                        
                                                      /*
                                                      foreach($typeof_edits as $id => $properties){
                                                if(is_array($properties)){
                                                    foreach($properties as $idee => $valu){
                                                        
                                                    }         
                                                }
                                            }
                                                      
                                                      *\/
                                                    foreach($class as $idee => $valu){// the properties themselves(slct,defval,selection,etc)
                                                        if(is_array($valu)){
                                                            foreach($valu as $properties => $valu2){//the properties themselves
                                                                switch($properties){
                                                                    case "selection":
                                                                    case "slct":
                                                                        if(is_array($valu2)){
                                                                            foreach($valu2 as $slct_id => $slct_val){ //selection name
                                                                               
                                                                                if(preg_match("#^[0-9]+$#",$slct_id)){
                                                                                    $check_2 = $quick_commands->quick_dbq("SELECT * FROM db_columns WHERE dbcid='$slct_id' AND maxlength='$id'");
                                                                                    $change_val = $quick_commands->quick_dbq("UPDATE db_columns SET dbc_name='".hack_free($slct_val)."' WHERE dbcid='$slct_id' AND maxlength='$id'");
                                                                                    if($change_val !== "executed"){
                                                                                        $error .= $change_val . "\n";
                                                                                    }
                                                                                }
                                                                            } 
                                                                        }
                                                                    break;
                                                                    
                                                                }
                                                            }
                                                        }
                                                    }
                                                }else{ //intlink
                                                    
                                                }  
                                            }   
                                            var_dump($pairz); 
                                       }    
                                    break;  
                                 }      
                            } */       
                        }
                        
                    break; //end finish_edits             
                }
            }
        break;
        case "display_page":
            if(isset($_GET['pg_display'])){
                switch($_GET['pg_display']){
                    case "todolist":
                        ?>
                    <div id="todo_list">
                        <h3>To-Do List</h3>
                        <textarea class="flick full spec5" ref="note_maker regex_check" rel="note_maker">Type anything you need to do here...</textarea>  
                        <div class="option"><input class="spec2" type="checkbox" checked="checked"> Submit an entry by pressing [ENTER]</div>
                        <h3 class="separator">Current To-Do List</h3>
                        <div class="white_bg" id="notes">
                        <?php 
                            $notes = $quick_commands->quick_dbq("SELECT * FROM notes WHERE note_by='$user_dt[uid]' ORDER BY note_date DESC","all");
                            if(!is_array($notes)){
                                echo "No notes for now. Feel free to make one!";
                            }else{
                                foreach($notes as $nc => $note_dt){
                                $note_dt['note_date'] = $preset->dflt_dfmt($note_dt['note_date']);
                                
                        ?>
                        <div class='note' note_id='<?php echo $note_dt['note_id'] ?>'><div class='note_content'><?php echo $note_dt['note'] ?></div><span class='prop date'><?php echo $note_dt['note_date']; echo $filler_temp->sub("e_d_cell",$note_dt['note_id'],"true","notes"); ?> </span></div>
                        <?php }} ?>
                        </div>
                    </div>                                                                                                                         
                        <?php
                    break;
                    case "formsmanage":
                        unset($_SESSION['sel'],$_SESSION['table_mod_new_col'],$_SESSION['promptz']);
                        if($user_dt['db_affinity'] === "0"){
                            if(!isset($_SESSION['ra_db_selected'])){
                                $no_db = "<div class='notice'>". $text_i['no_db_selected']."</div>";
                            }else{
                                $actual_dbid = $_SESSION['ra_db_selected'];
                            }
                        }
                        else{
                            $actual_dbid = $user_dt['db_affinity'];
                        }
                ?>                
                    <div reff="actual_content" id="<?php echo $_FILTERED['pg_display']; ?>"<?php if(isset($actual_dbid)){ ?> ref="<?php echo $actual_dbid; ?>"<?php } ?>>
            <?php   
                        if(isset($no_db)){echo $no_db;}
                        if(isset($actual_dbid)){
                         
                        $get_db_dt = $extra_commands->db_schema($_MONITORED['ra_db_selected'],["db_info","table_info"]);
        
                        echo "<h3 class='db_stack'><strong>$text[viewing]</strong>".$get_db_dt[0]['name']."<br><small>$text[db_since] ".$get_db_dt[0]['date_created']."</small></h3>";
                        echo "<div class='white_bg' id='form_list'>";
                        if(!is_array($get_db_dt[1])){
                            echo "<div class='notice'>".$text['no_forms']."</div>";    
                        }else{
                            
                        }
                        
                        echo "<button class='submit_clone spec noselect' id='new_db_form'>Create New Form</button>";
                        echo"</div>";
                        echo "<h3>$text[column_list]</h3>";
                        if(is_array($get_db_dt[1])){
                            echo "<div class='white_bg norm'>";
                            $real_option = json_decode($user_dt['column_propz'],true);
                            $fillr = "";
                            $x = 0;
                            $nameadd = "";
                            //$quick_commands->var_dump($get_db_dt);
                            foreach($get_db_dt[1] as $ye => $la){
                                $y = count($get_db_dt[1]) - $x - 1;
                                $user_option = (isset($real_option[$la['dbfid']]['open_state']) && $real_option[$la['dbfid']]['open_state'] === "open");
                                $tab_is_open = ($quick_commands->truth_hrc("false priority",$user_option,($user_option || $la["is_open"] === "true")) || ($y === 0 && isset($_SESSION['new_dbf']))); //This took longer than I imagined it would take. I couldn't imagine the logical sequence in my head
                                if($tab_is_open){
                                    $fillr = " slctd";    
                                    $nameadd = " name='newdbf'";
                                }
                                echo "<a href='javascript:void(0)' ref='table_tab' class='fulltab spec2$fillr' name='dt$la[dbfid]' rel='$la[dbfid]'$nameadd>$la[table_name]</a>";
                                /*$la["is_open"] === "true" ||            
                                (isset($real_option[$la['dbfid']]['open_state']) && $real_option[$la['dbfid']]['open_state'] === "open" || ($y === 0 && isset($_SESSION['new_dbf']))) */
                                if($tab_is_open){
                                    $extra_commands->view_table($la['dbfid']);
                                }
                                
                                if($y === 0 && isset($_SESSION['new_dbf'])){
                                    unset($_SESSION['new_dbf']);
                                }
                                $fillr = "";
                                $x++;
                            }
                            echo "</div>";
                        }
                    }
                   
                    break;    
                }
                echo "</div>"; 
            }
        break; //end formsmanage
        case "table_tabs":  
            $get_dt = $extra_commands->db_schema($_FILTERED['id_name']);
            

            if(is_array($get_dt)){  
                $real_option = json_decode($user_dt['column_propz'],true);
                if(isset($_GET['misc_command']) && isset($_GET['key_2'])){
                    
                    switch($_GET['misc_command']){
                        case "default_check":
                            if(count($real_option) === 0){
                                echo false;
                            }
                            else{
                                echo ($real_option[$_FILTERED['id_name']]['default_option'] === $_GET['key_2']) ;
                            }
                        break;
                        case "update":  
                            if(preg_match("#^[0-9]+$#",$_GET['id_name'])){
                                $real_option[$_FILTERED['id_name']]['default_option'] = $_FILTERED['key_2'];
                                $wrapped_ro = mysqli_real_escape_string($db_main,json_encode($real_option)); //condoms are necessary
                                $check = $quick_commands->quick_dbq("UPDATE userz SET column_propz='$wrapped_ro' WHERE username='$_MONITORED[logged_q]'");                              
                            }
                        break;
                        case "tab_switch":
                            if(preg_match("#^[0-9]+$#",$_GET['id_name']) && preg_match("#^open|close+$#",$_GET['key_2'])){
                                $real_option[$_FILTERED['id_name']]['open_state'] = $_FILTERED['key_2'];
                                $wrapped_ro = mysqli_real_escape_string($db_main,json_encode($real_option));
                                var_dump($real_option);
                                $update = $quick_commands->quick_dbq("UPDATE userz SET column_propz='$wrapped_ro' WHERE username='$_MONITORED[logged_q]'");
                                var_dump($update);
                                
                            }
                        break;
                        
                    }
                }else{
                    $extra_commands->view_table($_FILTERED['id_name']);
                }
            } 
        break;
        case "filler_request":
            switch($_GET['filler']){
                case "intangible_link":
                    echo $filler_temp->sub("intangible_link",$_FILTERED['id_name']);
                break;
                case "signup":
                    echo "sdfsdfsd";
                break;
                case "add_selection":
                    
                    $label = "dt";
                    if(isset($_FILTERED["type"])){
                        switch($_FILTERED["type"]){       
                            case "modifying":
                                $label = 'dts';
                                $copy = $_FILTERED['id_name'];
                                $_FILTERED['id_name'] = preg_replace("#s#","",$_FILTERED['id_name']);
                                $_SESSION["sel"][$_FILTERED['id_name']] = isset($_SESSION["sel"][$copy]) ? $_SESSION["sel"][$copy]++ : 1 ;
                            break;
                        }
                        
                    }
                    $int = $_FILTERED['id_name'];
                    $_SESSION["sel"][$_FILTERED['id_name']] = intval($_SESSION["sel"][$_FILTERED['id_name']]) + 1;   
                    $sel_n = $_SESSION["sel"][$_FILTERED['id_name']];         
                    echo '<input type="text" value="Selection" class="flick" name="'. $label .$int.'_selection'.$sel_n.'">';
                break;
                case "new_selection":       
                   //hi im lazy and can't be bothered to use constructors
                   $_SESSION["sel"][$_FILTERED['id_name']] = 2;                                                  
                   echo $filler_temp->sub("new_selection",$_FILTERED['id_name']);
                break;
                case "new_column":
                    if(isset($_GET['id'])){
                        echo $filler_temp->sub("new_column",$quick_commands->increment($_SESSION['table_mod_new_col'][$_FILTERED['id']]),"true"); 
                    }else{
                        echo $filler_temp->sub("new_column",$quick_commands->increment($_SESSION['crnt_col_id'])); 
                    }
                break;                                                                                               
                case "personal_infoz":
                    //all default personal information will be under db_affinity *#0*
                    $def_pi = mysqli_query($db_main,"SELECT * FROM extra_person_info WHERE pi_affinity = '0'");
                    $int = 0;
                    while($defpi = mysqli_fetch_assoc($def_pi)){  
                        $defpe[$int] = $defpi;
                        switch($defpi['epdt_type']){
                            case "selection":                           
                                $selections = $quick_commands->quick_dbq("SELECT * FROM extra_person_info WHERE pi_affinity='b".hack_free($defpi['epi_id'])."'","all");
                                $defpe[$int]['slcts'] = $selections;
                            break;
                        }
                        $int++;
                    }

                ?>
                    <h3>Default Personal Information Columns</h3>   
                    <div class='plain_p'><?php echo $text['dpic_notice'];?></div> 
                    <div class='select_bx'>
                <?php  
                    for($i = 0;$i <= count($defpe) - 1;$i++){
                        $check_MARK = ["",""];
                        switch($defpe[$i]['epdt_type']){
                            case "int":
                                $defpe[$i]['epdt_type'] = "Integer";  
                            break;
                            case "string":
                                $defpe[$i]['epdt_type'] = "String"; 
                            break;
                            case "paragraph":
                                $defpe[$i]['epdt_type'] = "Paragraph"; 
                            break;
                            case "selection":
                                $defpe[$i]['epdt_type'] = "Selection"; 
                            break;   
                        }
                        switch($defpe[$i]['epdt_value']){
                            case "Email Address":
                            case "Occupation":
                            case "Date of Birth":
                            case "First Name":
                            case "Middle Name":
                            case "Last Name":
                            case "Phone Number":
                                $check_MARK = [" selectd"," checked='checked'"];
                            break;
                        }
                    ?>
                        <div class='noselect select<?php echo $check_MARK[0]; ?>'><?php echo $defpe[$i]['epdt_value']; ?> (<?php echo $defpe[$i]['epdt_type']; ?>) <input name="include_pii" type="checkbox" ref="<?php echo $defpe[$i]['epdt_value']; ?>" value="<?php echo $defpe[$i]['epi_id']; ?>"<?php echo $check_MARK[1]; ?>></div>
                    <?php
                    }
                    echo "</div>";
                break;
                case "new_form":
                    $_SESSION['crnt_col_id'] = 1;     
                    unset($_SESSION['sel']);
                    ?> 
                    <div id='new_frmm'> <div id='formsettings'>  <h3>Create New Form</h3>
                    <input type='hidden' name='db_affinity' value='<?php echo $_FILTERED['db_aff']; ?>'>
                    <div class='box1 max'><h4><input type='text' value='Form Name' class='flick largeform' name='form_name'></h4></div>
                    <div class='box1'><h4><?php echo $text['has_ppl_dt']?><button class='button_help spec2' ref='button_help' rel='has_ppl_dt'></button></h4><div class='box2'><input type='radio' name='has_ppl_dt' value='true'>Yes</div><div class='box2'><input type='radio' name='has_ppl_dt' value='false' checked='checked'>No</div></div>
                    
                    <div class='box1'><h4><?php echo $text['is_editable']; ?> <button class='button_help spec2' ref='button_help' rel='is_editable'></button></h4><div class='box2'><input type='radio' name='is_editable' value='true' checked='checked'>Yes</div><div class='box2'><input type='radio' name='is_editable' value='false'>No</div>
                    </div>
                    
                    <div class='box1'><h4><?php echo $text['is_intangible']; ?> <button class='button_help spec2' ref='button_help' rel='is_intangible'></button></h4><span id='intngopt'><div class='box2'><input type='radio' name='is_intangible' value='true'>Yes</div><div class='box2'><input type='radio' name='is_intangible' value='false' checked='checked'>No</div></span></div></div>
                    
                    
                    
                    <h3>Form Columns</h3>
                    
                    <div class="box1 max left"><h4><button class="submit_clone spec" id="addnewform">Add New Column...</button></h4></div>
                    
                    <?php echo $filler_temp->sub("new_column",1);?>
                    </div>
                    </div> 
                    <?php
                break;
            }
        break;
    }
}

if($extra_commands->login_status("root_admin") && isset($_GET["action"])){
  switch($_GET['action']){
  case "sql_q": // first, test to check if it has a delete or drop statement
                echo "<br>";
                 if (!preg_match("#^[;]?[ ]{0,}(DELETE|DROP|delete|drop) #", $_POST['sql_q'])) {
                    
                    if (preg_match("#^[ ]{0,}(SELECT|select)#", $_POST['sql_q'])) {
                        
                        $_POST['sql_q'] = (preg_match("#LIMIT[ ]+[0-9]+([,][ ]{0,}[0-9]+)?[ ]{0,}[;]?[ ]{0,}$#", $_POST['sql_q'])) ? $_POST['sql_q'] : preg_replace("#^(.+)([;]?)[ ]{0,}$#", "$1 LIMIT 0,100$2", $_POST['sql_q']) ;
                         $type = "row fetch";
                         } 
                    
                    @$test_query = mysqli_query($db_main, $_POST['sql_q']);
                     if ($test_query) { // successful SQL query?
                        if (!preg_match("#^[ ]{0,}(SELECT|select) #", $_POST['sql_q'])) {
                            echo "The query <span class='sql_q'>" . htmlspecialchars($_POST['sql_q']) . "</span> was successful.";
                             } else { // display rows!
                            // $get_rows = mysqli_fetch_assoc($test_query);
                            /**
                             * foreach($get_rows as $keys => $values){
                             * $rows = isset($rows) ? array_merge([$keys],$rows) : [$keys];
                             * }
                             */
                            
                            if (mysqli_num_rows($test_query) == 0) {
                                echo "No rows returned. Your SQL syntax is valid, but maybe your search criteria is wrong.";
                                 } 
                            
                            if (mysqli_num_rows($test_query) !== null && mysqli_num_rows($test_query) > 0) {
                                echo "<table id='results' class='no_breaks'>";
                                
                                 while ($get_data = mysqli_fetch_assoc($test_query)) {
                                    $get_rows[] = $get_data;
                                     foreach($get_data as $keys => $values) {
                                        $key_dump = (isset($key_dump) && array_search($keys, $key_dump) === false) ? array_merge($key_dump, [$keys]) : [$keys]; //all possible keys
                                         } 
                                    } 
                                echo "<tr>";
                                 echo "<th width='1%'>#</th>";
                                 foreach($key_dump as $field_names) {
                                    echo "<th>" . $field_names . "</th>";
                                     } 
                                echo "</tr>";
                                 foreach($get_rows as $get_rows2) {
                                    $x = isset($x) ? $x + 1 : 1;
                                     $y = (isset($y) && $y < 2) ? $y + 1 : 1;
                                     echo "<tr>";
                                     echo "<th width='1%'>" . $x . "</th>";
                                     foreach($get_rows2 as $keys => $values) {
                                        $z = (isset($z) && $z < 2) ? $z + 1 : 1; //let's get stylish
                                         $values = (strlen($values) > 250) ? preg_replace("#^(.+){247}(.+)$#", "$1...", $values) : $values;
                                         echo "<td class='a$y$z'>" . $values . "</td>";
                                         } 
                                    unset($z);
                                     echo "</tr>";
                                    
                                     } 
                                unset($y);
                                 echo "</table><br>";
                                 unset($x);
                                 unset($key_dump);
                                 unset($get_rows);
                                 mysqli_free_result($test_query);
                                 } 
                            } 
                        } else {
                        echo !preg_match("#^[ ]{0,}$#", $_POST['sql_q']) ? "<div class='admin_notice'><strong>SQL error:</strong> " . mysqli_error($db_main) . " </div>" : "You left the SQL query empty. Please input something.";
                         } 
                    
                    } 
                break;
            
             case "sg_url_avail":
                 $sg_url_search = mysqli_query($db_main, "SELECT * FROM snowglobes WHERE sg_url='$_FILTERED[test]'");
                 if (mysqli_num_rows($sg_url_search) > 0) {
                    echo "<div class='notice'>Unfortunately, this URL is not available.</div>";
                     } else {
                    echo "<div class='notice green'>This URL is available</div>";
                     } 
                break;
            
            
            
             case "css_edit":
                
              
                
                 if (preg_match("#[.]css$#", $_POST['file'])) { // nice try kids
                    
                     
                     $_POST['file'] = preg_replace("#^.{0,}(?:core[\/])(.+)$#", "$1", $_POST['file']);
                    
                     $file_opener = fopen($_POST['file'], "w+");
                     // convert the file strings back
                    // $_POST['data'] = preg_replace("#template[\057]#","",$_POST['data']);
                    // no longer a need for above as i've replaced the trim with the full url
                    
                    $_POST['data'] = str_replace("{url}",domain,$_POST['data']);
                    $z = fwrite($file_opener, $_POST['data']);
                    
                     if ($z) {
                        echo json_encode(array("notice" => "success", "bash" => "two"), JSON_UNESCAPED_SLASHES);
                         } 
                    
                    }   
                    
                 
                
                
                break;
            
             case "admin_notes":
                
                 $_POST["admin_notes"] = htmlspecialchars(mysqli_real_escape_string($db_main, $_POST["admin_notes"]));
                
                 $send_sql = mysqli_query($db_main, "UPDATE `internal_settings` SET `value` = '$_POST[admin_notes]' WHERE `internal_settings`.`int_s_id` = 1");
                
                 if ($send_sql) {
                    echo json_encode(array("notice" => "success"), JSON_UNESCAPED_SLASHES);
                     } 
                else {
                    echo mysqli_error();
                     } 
                
                break;
            
            
            
             // admin panel stuff
            case "drag_box":
                 if (isset($_GET['screen_max'])) {
                    if (isset($_SESSION['admin_panel']['xpos'])) {
                        if ((intval($_GET['screen_max'][0]) - $_GET['screen_max'][2] < $_SESSION['admin_panel']['xpos']) || (intval($_GET['screen_max'][1]) - $_GET['screen_max'][3] < $_SESSION['admin_panel']['ypos'])) {
                            unset($_SESSION['admin_panel']['xpos'], $_SESSION['admin_panel']['ypos']);
                             } 
                        } 
                    } 
                if (isset($_GET['offsets'])) {
                    $_SESSION['admin_panel']['xpos'] = intval($_GET['offsets'][0]);
                     $_SESSION['admin_panel']['ypos'] = intval($_GET['offsets'][1]);
                     } 
                
                
                break;
            
             case "change_panels":
                 unset($_SESSION['admin_panel']['current_view']);
                
                 if ($_GET['view'] !== "v_sess") {
                    $_SESSION['admin_panel']['current_view'] = $_GET['view'];
                     } 
                
                // no idea why I had so much code there previously
                break;
             case "generate_password":
                 /*		$nick = hash( 'sha512', $mn['salt'] . $_DATA['pwrdnorm'] ); //our hash mix shoulde be more complicated than this
																$gravy = substr( $nick, 0, 40 ); //make a nice meal */
                $salt = substr(sha1(microtime(true).microtime(true)),0,20);
                $password_plaintext = substr($salt,0,15);
                $salad = substr(hash("sha512", $salt . $password_plaintext),0,100);
                echo "<tr class='res_list'><th>Salt</th><td>$salt</td></tr>";
                echo "<tr class='res_list'><th>Password Plaintext</th><td>$password_plaintext</td></tr>";
                echo "<tr class='res_list'><th>Salad</th><td>$salad</td></tr>";
                break;
             case "admin_opts":
                 if (isset($_GET['req'])) {
                    switch ($_GET['req']) {
                    case "fadeout":
                        if (isset($_SESSION['admin_panel']['fadeout_opt'])) {
                            
                            unset($_SESSION['admin_panel']['fadeout_opt']);
                            
                        } else {
                            
                            $_SESSION['admin_panel']['fadeout_opt'] = "set";
                            
                        }
                    break;
                    case "minimize":
                         // unsetting and setting sessions with ternary operators... kinda tacky?
                        // lol doesn't work
                        if (isset($_SESSION['admin_panel']['minimized'])) {
                            
                            unset($_SESSION['admin_panel']['minimized']);
                            
                             } else {
                            
                            $_SESSION['admin_panel']['minimized'] = "set";
                            
                             } 
                        
                        break;
                         } 
                    } 
                break;
  }
}
    
?>