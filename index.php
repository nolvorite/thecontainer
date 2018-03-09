<?php     
    require_once("core/prereq.php");    
    //session_unset();
?>               
<!DOCTYPE html>
<html>
<head>
    <link rel='stylesheet' href="core/jquery.datetimepicker.css">
    <link rel="stylesheet" href="core/template/style_main.css">
    <title><?php echo $internal_var->title(); ?></title>
    <link rel="icon" href="core/template/img/icon.png">
    <script type="text/javascript" src="core/extra.js"></script>
    <script type="text/javascript" src="core/jquery-.js"></script>
    <script type="text/javascript" src="core/jquery-ui.js"></script>
    <script type="text/javascript" src="core/js/jquery.datetimepicker.full.min.js"></script>
    <script type="text/javascript" src="core/js/timezonecomplete.js"></script>
    <script type="text/javascript" src="core/leet.js"></script>
    <?php if($extra_commands->login_status("root_admin")){ ?>
    <script type="text/javascript" src="core/admin_panel.js"></script>   
    <?php } ?>
</head>
<body>
    <div id="header">
        <a href="<?php echo domain; ?>index.php" class="logo">
            <img src="<?php echo $set_var->html("image","logo"); ?>" alt="<?php echo $set_var->html("image","logo","alt"); ?>" />
        </a>
        <?php if(!$extra_commands->login_status("_current_user")){ ?>    
        <button class="login rad spec" id="login_btn"><?php echo $text['login']; ?></button>
        
        <?php }else{ //user panel ?>
        
        <div id="login1" class="logged_in">
            <?php echo $extra_commands->status("dbref_display"); ?>
        </div><a href="<?php echo domain . "index.php?action=logout";?>" class="submit_clone" id="logout_btn">Log Out</a>
                                             
        <?php } ?>
    </div>
    <?php  if(!$extra_commands->login_status("_current_user")) {
              if(isset($_SESSION['submit_q'])){ //all submission queues for non-users
                   if(isset($_SESSION['submit_q']['ra_setup'])){
                          if($quick_commands->quick_dbq("SELECT * FROM userz WHERE uid=1","num_rows") === 0){
                          
              //create root admin account here ?>
    <div id="admin_setup">
        <form method="POST" action="<?php echo domain; ?>index.php?action=admin_confirm">
            <h4>Root Admin Setup</h4>
            <p>Do not close this page, or you will lose this setup form.</p>
            <input type="text" value="<?php echo $_SESSION['submit_q']['ra_setup']['log_username']; ?>" name="admin_username">
            <input type="password" value="<?php echo $_SESSION['submit_q']['ra_setup']['log_password']; ?>" name="admin_password">
            <input type="text" value="Email Address" name="admin_email" class="flick">
            <input type="text" value="Full Name" name="admin_name" class="flick">
            <input type="submit" value="Submit">
        </form>
    </div>          
    <?php
                          }
                          else{
    ?>
    <div class='notice'><?php echo $text['pw_notice']; ?></div>
    <?php                 }      
                           } //end ra_setup
     ?>
    <?php
            if(isset($_SESSION['submit_q']['admin_confirm'])){  
                       ?>
    <div id="admin_setup"><h4>Root Admin Setup</h4>
        <p>Congratulations, you have finished the admin confirmation process! Now just click the button below to finish the setup, and login to the admin panel. <br><br><a href="<?php echo domain . "index.php?action=true_ra_complete"; ?>" class="submit_clone">Finish Admin Setup</a>
        </p>
    </div>        
    <?php    
     }   //end admin_confirm       
    
    ?>
    
    
    
    <?php     } ?>
    <!-- begin actual content -->
    
    <div id="content_1">
    <div id="midspace"><table><tr><td class="img_cast"><img class="left" src="<?php echo $set_var->html("image","stock_1"); ?>" alt="<?php echo $set_var->html("image","stock_1","alt"); ?>"></td><td id="intro1"><h3><?php echo $text['intro1_header']; ?></h3>
    <p><?php echo $text['intro1_text']; ?>
    
    </p><p><a href="/in_detail/" id="learnmore1" class="opts1"><?php echo $text['learnmore1']; ?></a><button id="downloadapp1" class="opts1"><?php echo $text['downloadapp1']; ?></button></p>
    </td></tr></table></div>      
    </div>
    <?php }  else { ?> 
    <div id="panel">
        <div id="left_menu">
            <h3>Menu</h3>
            <div id="list_container">
                <?php for($i = 0; $i <= count($left_menu) - 1; $i++){   ?>
                <a href="javascript:void(0)" class="spec leftmnu" link="<?php echo $left_menu[$i][1]; ?>"<?php if(isset($left_menu[$i][3])){ ?> id="<?php 
                echo $left_menu[$i][3]; ?>"<?php } ?>><?php echo $left_menu[$i][0]; ?></a>
                <?php } ?>                      
            </div>
            <div id='floater'><span id='fp_slot'></span><span id='qbt_slot'></span>
            </div>
        </div>
        <div id="right_side">
             <?php if($user_dt['uid'] === "0" || $user_dt['uid'] === "1"){ //root admin database query ?>
             <h3 class='red'><?php echo $text_i['rad_h3']; if(isset($_SESSION['ra_db_selected'])){ 
             $db_name = $quick_commands->quick_dbq("SELECT name FROM dbz WHERE dbid=$_SESSION[ra_db_selected]","first_only","name");
             ?> (Currently <span>"<?php echo $db_name;?>"</span>)<?php } ?></h3>
             <div id="root_admin_panel">
                 <p><?php echo $text['rad_check']; ?></p><input type="text" id="rad_check">
             </div>
             <?php } ?>
             <div id="shoop">
             </div>
        </div> 
    </div>
    <?php } ?>
    
    <?php if(isset($_SESSION['message']['login'])){
        echo '<script type="text/javascript"> 
            aux2.alertz("'.$text['user_not_found'].'.");
        </script>';
    } ?> 
                        
 
    
    <div id="footer">
        <h4><?php echo $text['sitemap']; ?></h4>
        <a href="/tos/"><?php echo $text['toc']; ?></a>
        <a href="/about/"><?php echo $text['about1']; ?></a>
    </div>
    <?php if($extra_commands->login_status("root_admin")){ include("core/admin_panel.php"); } ?>
</body>
</html>
<?php 
    require_once("core/lolendofpage.php");
?>