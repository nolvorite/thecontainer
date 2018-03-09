<?php
if(!class_exists('internal_var')){
    class internal_var {
        public function random_hash(){
            return md5(microtime(true) . microtime());
        }
        public function title(){
            global $text_i;
            $title = $text_i['default_title'];
            return $title;
        }
    }
}
if(!defined('domain') && !defined('test')){
    define("current_url","http://" . $_SERVER['SERVER_NAME'] .$_SERVER['PHP_SELF']);
    define("domain",preg_replace("#(index[.]php|core[/](.+)[.]php)[/]?#","",current_url)) ;
}
if(!defined('dflt_fmt')){
    define('dflt_fmt',"F j, Y, g:i A");
}
if(!isset($internal_var)){
    $internal_var = new internal_var();
}         

$text = [         
    "login" => "Log In",
    "intro1_header" => "Online database storage and management has never been easier.",
    "intro1_text" => "Set up an online database in just minutes! TheContainer is your tool to efficiently and securely store information.",
    "learnmore1" => "Learn More!",
    "downloadapp1" => "Download App",
    "toc"=> "Terms and Conditions", 
    "sitemap"=> "Sitemap",
    "about1" => "About TheContainer",
    "pw_notice" => "Incorrect username/password combination",
    "u_r_logd_msg" => "You are logged in",
    "root_admin" => "Root Admin View",
    "rad_check" => "Look up any existing database and make any modifications by looking up the database here.",
    "date_created" => "Date Created:",
    "no_forms" => "There are no tables/database forms in this database.",
    "db_since" => "A database since",
    "viewing" => "Viewing: ",
    "has_ppl_dt" => "Is this table/form a listing of people's personal information?",
    "is_editable" => "This table's contents can be edited.",
    "is_intangible" => "This table's individual data sets is a unique item.",
    "form_preview" => "Form Preview",
    "t6" => "Is not a listing of people's personal information",
    "t5" => "Contents are NOT editable",
    "t4" => "Each data set isn't an identity",
    "t3" => "Each individual data set is unique",
    "t2" => "Is editable",
    "t1" => "Is a listing of people's personal information",
    "isedtbl1" => "Editable",
    "isedtbl2" => "Uneditable",
    "intngblz" => "Intangible Link",
    "prgrph" => "Paragraph/Long Text",
    "int" => "Number/Integer",
    "string" => "String",
    "lolnone" => "<em>None</em>",
    "create_form" => "Create Form",
    "slctn" => "Selection",
    "multislct" => "Multi-Selectable",
    "singleslct" => "Single-Selection",
    "intngbl_ref" => "This is the intangible reference. <input type='radio' name='intangible_ref' value='\"+ cid +\"'>",
    "intangible_slct" => '<option value="string">String</option><option value="int">Number/Integer</option>',
    "full_slct" => '<option value="string">String</option><option value="selection">Selection</option><option value="int">Number/Integer</option><option value="paragraph">Paragraph/Long Text</option><option value="intangible">Intangible Link</option><option value="datetime">Date/Timestamp</option>',
    "dpic_notice" => 'Select what data is needed in a form requiring one\'s personal information. If you do not find it here, feel free to make your customized columns under <strong>"Form Columns"</strong>.',
    "intng_ppl" => "Disabled, Personal Information Forms are Intangible by default. <button class='button_help' ref='intng_ppl'></button>",
    "datetime" => "Timestamp",
    "def_ts" => "Default Timestamp",
    "no_results" => "<span class='notice3'>No results found. Please change your search criteria</span>",
    "select_table" => "Select the Table with Intangible Data...",
    "column_list" => "List of Tables",
    "no_tables" => "There are currently no tables in this database. Feel free to create one below!",
    "add_new_data" => "Add New Data",
    "manage_data" => "View Data",
    "dbf_settings" => "Table Settings",
    "schema" => "View Schema",
    "search_x" => "Search for data...",
    "view_opts" => "Open by default",
    "view_opts2" => "Default Tab open",
    "current_data" => "Current Data",
    "no_data" => "There is currently no data available",
    "delet" => "Delete",
    "prompt" => 'Are You Sure?',
    'deltd' => "Deleted",
    "delet_2" => "Delete Column",
    "redir_opt" => "Redirect after Data Submission",
    "sort_1" => "Sort by Descending Order.",
    "sort_2" => "Sort by Ascending Order.",
    "sort_3" => "Remove all sorting.",
    "n0_results" => "No results found.",
    "current_tables" => "Current Tables",
    "finish_edits" => "Finish Editing",
    "submitted" => "Submitted!"     ,
    "too_long" => "Note is too long.",
    "user_not_found" => "User not found, or incorrect username/password combination.",
    "sign_up" => "Sign Up",
    "delete_prompt" => "Are you sure that you want to delete this? For reference, this is ",
    "c0nfirm" => "Confirm Deletion"
];  
/*

echo "<a href='javascript:void(0)' ref='new_data' class='spec2'>$text[add_new_data]</a>";
                echo "<a href='javascript:void(0)' ref='manage_data' class='spec2'>$text[manage_data]</a>";
                echo "<a href='javascript:void(0)' ref='dbf_settings' class='spec2'>$text[dbf_settings]</a>";
                echo "<a href='javascript:void(0)' ref='schema' class='spec2'>$text[schema]</a>";

*/   
                  
@$filler = [
    "login_opt" => "<form id='login1' method='POST' action='". domain ."index.php?action=login&verify=". $_SESSION['t_hash'] ."'><input type='text' name='log_username' value='Username...' class='flick'><input type='password' name='log_password' value='Password' class='flick'><input type='submit' value='Log In' class='submit_btn'><a href='' class=\\\"submit_clone spec\\\" id=\\\"signupp\\\">".$text['sign_up']."</button></a>",
    
];

$left_menu = [
["To-Do List","todolist"],
["Manage Forms","formsmanage"],
["Manage Users","usersmanage"],
["General Settings","generalsettins"],
["Personal Settings","personalsettins"],
["Root Admin Settings","ra_settins","admin","admin_btn"]
];

class filler_templates {
    public function sub($temp_name,$int = "0",$modifying = "false",$type = ""){  global $text;  
        switch($temp_name){            
            case "e_d_cell":
                $mod = $modifying === "true" || $modifying === true ? "" : " clear";
                $type = ($type === "") ? "" : " type='".htmlspecialchars($type)."'";
                echo "<div class='options2$mod' rel='$int'><button class='small_btn spec2' ref='edit_cell' rel='$int'$type>Edit</button><button class='small_btn spec2' ref='delete_cell' rel='$int'$type>Delete</button></div>";
            break;
            case "optz":
                return "<input type='text' value='$int' name='view_optz$int2' ref='$int2'>";
            break;
            case "table_tabs":
                return "<div class='tabz'></div>";        
            break;
            case "new_selection":
            case "intangible_link":
                $header = '';
                switch($temp_name){
                    case "new_selection":
                        $inputs = '<input type="text" value="Selection" class="flick" name="dt'.$int.'_selection1">
                        <input type="text" value="Selection" class="flick" name="dt'.$int.'_selection2">
                        <button class="submit_clone spec2" ref="add_selection">Add Possible Selection</button>';
                    break;
                    case "intangible_link":
                        $header = '<h5>Intangible Link</h5>';
                        $inputs = '<input type="text" value="'.$text['select_table'].'" class="flick spec3" name="dt'.$int.'_intlink" ref="intangible_link">';
                    break;
                }
                return '
                    <div class="spec_box" id="'.$int.'">'.$header.'
                        <div class="spec_note"><span>Multiple values can be selected.</span><input type="checkbox" class="cleared" name="dt'.$int.'_is_multi"></div>
                    '.$inputs.'  
                    </div>
                ';
            break; //end new selection
            case "new_column":
                return ($modifying === "true")
                    ? 
                    "
                        <div class='form_box new_creation'>   
                        <div class='div3'>
                        <strong>Column Name</strong><br>
                        <input type='text' value='Column Name' name='dbcn_".$int."_name' value='Column Name' class='flick'>    
                        </div>             
                        
                        <div class='div3'>
                        <strong>Default Value:</strong><br>
                        <input type='text' name='dbcn_".$int."_defval' class='' value='Default Value'>
                        </div>
                        <div class='div3'>
                        <strong>Type</strong><br>
                        <select name='dbcn_".$int."_type' class='spec2' ref='selection' rel='c$int'>
                        $text[full_slct];
                        </select>
                        </div>
                        
                        </div>"                 
                    :
                    '
                    <h4 class="id_designation">Column '.$int.'</h4>
                    <div class="oneform" column_id="'.$int.'">
                    <div class="box1"><h4 class="hasform">Column Name</h4><div class="box2"><input type="text" class="flick" name="dt'.$int.'_name" value="Column Name"></h4>
                    
                    </div></div>
                    
                    <div class="box1"><h4>Data Type</h4><div class=\'box2\'><select name="dt'.$int.'_type1" class="spec2" ref="selection">
                     '.$text["full_slct"].'
                    </select>   </div>
                    </div>
                    <div class="box1"><h4 class="hasform">Default Value</h4><div class="box2"><input type="text" class="flick" value="Default Value" name="dt'.$int.'_defval"></h4>
                    
                    </div></div>
                    <div class=\'box1\'><h4>This column\'s contents can be edited. <button class=\'button_help\' ref=\'is_editable2\'></button></h4><div class=\'box2\'><input type=\'radio\' name=\'dt'.$int.'_is_editable2\' value=\'true\' checked="checked">Yes</div><div class=\'box2\'><input type=\'radio\' name=\'dt'.$int.'_is_editable2\' value=\'false\'>No</div>
                    </div>
                    
                    <div class=\'box1\'><h4>This column\'s is required to be filled out. <button class=\'button_help\' ref=\'is_required2\'></button></h4><div class=\'box2\'><input type=\'radio\' name=\'dt'.$int.'_is_required2\' value=\'true\' checked="checked">Yes</div><div class=\'box2\'><input type=\'radio\' name=\'dt'.$int.'_is_required2\' value=\'false\'>No</div>
                    </div>
                    
                    </div>
                    ';
            break;
        }
    }
}
$filler_temp = new filler_templates;
?>