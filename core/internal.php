<?php         
    $db_main = mysqli_connect("localhost","root","");
    require_once("core/directory_check.php");
    require_once("core/auxiliary.php");
    
     if(!isset($_GET['verify'])){
        $_SESSION['t_hash'] = $internal_var->random_hash();
    }
    
    require_once("core/template/text.php");
    require_once("text_internal.php");
    if(mysqli_query($db_main, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'thecontainer'")){
        mysqli_query($db_main,"CREATE DATABASE thecontainer");
    }
    
    //maintenance
    //call a process for submission processes to be finished
    if(isset($_SESSION['clear_process']) && !isset($_GET['action'])){
        foreach($_SESSION['clear_process'] as $key => $var){
            unset($_SESSION['submit_q'][$key],$_SESSION['clear_process'][$key]);
        }
    }
    if(isset($_SESSION['submit_q'])){
        foreach($_SESSION['submit_q'] as $key => $var){
            $_SESSION['clear_process'][$key] = true;    
        }
    }
   
    
    
    
  ////////////////////////////////////////////////////////////////////////////////////  
                ////////////////////////////////////////////////////////////////////////////////////    
  ////////////////////////////////////////////////////////////////////////////////////    
    
    //all the site processes heeeah
    if(isset($_GET['action'])){ 
        if(isset($_GET['verify']) && $_SESSION['t_hash'] === $_GET['verify']){ //all site commands requiring a hash. CSRF prevention
        
            switch($_GET['action']){
                case "login":
                    $user_check = $quick_commands->quick_dbq("SELECT * FROM userz WHERE username = '$_DATA[log_username]'");
                    if(is_array($user_check)){      
                    //$pw_hash = substr(hash("sha512",$salt.$password.substr(md5($password.strlen($password),0,50))),0,150);                                                  
                        
                        
                        $confirmation = $quick_commands->check_pw($_POST['log_password'],$user_check['salt'],$user_check['pw_hash']); 
                        if($confirmation === true){
                            $_SESSION['logged_q'] = $_DATA['log_username'];
                            $_SESSION['salt_q'] = $user_check['salt'];
                            $extra_commands->submission_process(domain ."index.php","log_complete","no_data");
                        }
                    }
                    else{ 
                        if($quick_commands->quick_dbq("SELECT * FROM userz WHERE uid=0 OR uid=1","num_rows") !== 0){
                            $extra_commands->submission_process( domain ."index.php","login","user_not_found");    
                        }else{
                            $extra_commands->submission_process( domain ."index.php","ra_setup");
                        }
                    }
                break;
                
            }
        }
        switch($_GET['action']){//for those that dont need the token
            case "admin_confirm":   
                if($quick_commands->quick_dbq("SELECT * FROM userz WHERE uid=0 OR uid=1","boolean") !== true && isset($_POST['admin_username'],$_POST['admin_password'],$_POST['admin_email'],$_POST['admin_name'])){
                    //password = inputted password, salt = the salt logged in data, and hash = hash logged in data
                    //hash_equals(substr($salt.$password.substr(md5($password.strlen($password)),0,50),0,150),$hash); 
                    $password = $_POST['admin_password'];
                    $quick_commands->generate_hash($password,$salt,$pw_hash);
                    $admin_user_insert = mysqli_query($db_main,"INSERT INTO userz(username,pw_hash,db_affinity,salt,email,fullname,db_position) 
                    VALUES('$_DATA[admin_username]','$pw_hash','0','$salt','$_DATA[admin_email]','$_DATA[admin_name]','')");
                    if($admin_user_insert){            //who did this shit? lmfaoooooo
                    
                    
                        $_SESSION['admin_user_log'] = $_DATA['admin_username'];
                        $_SESSION['admin_user_salt'] = $salt;
                        $extra_commands->submission_process( domain ."index.php","admin_confirm","no_data");
                    }else{
                        echo mysqli_error($db_main);
                        unset($_SESSION['root_admin_q']);
                    }
                }else{
                    header("Location: " . domain);
                }
            break; //end "admin_confirm" conditional
            case "true_ra_complete":
                if(isset($_SESSION['admin_user_log'])){
                    $_SESSION['logged_q'] = $_SESSION['admin_user_log'];
                    $_SESSION['salt_q'] = $_SESSION['admin_user_salt'];
                    unset($_SESSION['admin_user_log'],$_SESSION['admin_user_salt']);
                    header( domain ."index.php" );
                }
            break; 
        }
    }
                     
    if(isset($_SESSION['root_admin_q'])){
        unset($_SESSION['root_admin_q']);
    }
    
    
?>