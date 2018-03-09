<?php
    if(!$extra_commands->login_status("_current_user")) {   
        if(isset($_SESSION['submit_q'])){ //all submission queues for non-users
            foreach($_SESSION['submit_q'] as $key => $val){
                switch($key){
                    case "login": 
                        unset($_SESSION['submit_q']["login"]);
                    break;
                    case "true_ra_complete":
                        unset($_SESSION['submit_q']);  
                    break;
                }
            }
            unset($_SESSION['redir_location']);
        }
    }
    if(isset($_SESSION['message']) && !isset($_GET['action'])){
        foreach($_SESSION['message'] as $key => $val){
            switch($key){
                case "login":
                    unset($_SESSION['message']['login']);
                break;
            }
        }
    }
    unset($_SESSION['sel'],$_SESSION['table_mod_new_col'],$_SESSION['promptz']);
 $quick_commands->var_dump($_SESSION);
?>