<?php session_start();
require_once("../template/text.php");
require_once("../text_internal.php");
header("Content-type:application/javascript");
                              
 ?>   
$(document).ready(function(){    
    $("body").on("click",".prompt",function(event){
    event.preventDefault();
    if(/^opts[:]/.test($(this).attr("href"))){
        opt = $(this).attr("href").replace(/^opts:(.+)$/,"$1");   
        $(this).attr("id",opt);
        switch(opt){
            case "minimize":
                if(typeof $("#admin_panel").attr("minimized") === "string"){    
                    $("#admin_panel").removeAttr("minimized");
                    $("#"+opt).html("<?php echo $text_i["minimize"]; ?>");
                    $(".open").hide();
                    true_width = $("#panels").outerWidth() -10;
                    $(".open").attr('style',"width:"+true_width+"px");
                }
                else {
                    $("#admin_panel").attr("minimized","a");
                    $("#"+opt).html("<?php echo $text_i["maximize"]; ?>");
                }
                $.get("<?php echo domain; ?>core/simcheck.php",{"action":"admin_opts","req":opt});
            break;
            case "fadeout":
                if(typeof $("#admin_panel").attr("nofadeout") === "string"){    
                    $("#admin_panel").removeAttr("nofadeout");
                    $("#"+opt).html("<?php echo $text_i["fadeout_opt2"]; ?>");
                    $(this).removeClass("selected_plink");
                }
                else {
                    $("#admin_panel").attr("nofadeout","a");
                    $("#"+opt).html("<?php echo $text_i["fadeout_opt"]; ?>");
                    $(this).addClass("selected_plink");
                }
                $.get("<?php echo domain; ?>core/simcheck.php",{"action":"admin_opts","req":opt});
            break;
            case "drag":
                toggle = (typeof toggle === "undefined" || toggle == "1") ?  0 : 1;
                if(toggle == 0){
                    $("#admin_panel").draggable("enable"); $("#admin_panel").addClass("draggy");
                    $(this).addClass("selected_plink");
                }
                else{$("#admin_panel").draggable("disable");$("#admin_panel").removeClass("draggy"); 
                    $(this).removeClass("selected_plink");
                }
                z = $(this).html();
                $(this).html(function(){  
                    return_value = ($("#admin_panel").hasClass("draggy")) ? "<?php echo $text_i["disable_drag"]; ?>" : "<?php echo $text_i["enable_drag"]; ?>";
                    return return_value;
                });
            break;
        }                               
    }
    if(/^edit[:]/g.test($(this).attr("href"))){    //murder murder murder murrdurrrrrrrrrrrrrrrrr murrrrrrdurrrrrrrrr
        //FINALLY. holy shit mother of god tweaking batman
        $zen_0 = $(this).attr('href');     
        $zen_1 = $(this).attr('href');                
        $zen_1 = $zen_1.replace(/^edit[:](.+)$/,"$1");
        $.get($zen_1).done(function(css){
            $("link[href='" +$zen_1+"']").remove();
            //modify directory file at url('')'s
            $snick = css;
            $snick2 = $snick;
            $snick6 = new RegExp("<?php echo domain; ?>",'g');
            $snick2 = $snick2.replace($snick6,"{url}");
            $snick2 = $snick2.replace(/[<>]/,"");
            if($("head style[title='"+$zen_1+"']").length === 0){     
                $("head").append("<style title='"+$zen_1+"'>"+ $snick +"</style>");  
            }
            if($("#jones").length === 0){  
            $("a[href='"+$zen_0+"']").addClass("selected2")
            .after("<a href='submit-edit' class='link_view link_view2 rad prompt finish_button' submit-type='css'>Finish editing</a><textarea id='jones' title='"+$zen_1+"' style='height:150px;margin-top:5px'></textarea>");
            //hmm...     
            $("#jones").val($snick2);              
            }
        }); 
    }
switch($(this).attr("href")){
    case "submit-edit":
        switch($(this).attr("submit-type")){
             case "css":
                  console.log($("#jones").val());
                  $.post("<?php echo domain; ?>core/simcheck.php?action=css_edit",
                  {"data":$("#jones").val(),"file":$("#jones").attr("title")},
                  function(data){
                      if(data.notice == "success"){aux2.alertz("Successfully edited file!",0,false,"on_top");}                                                                    
                  },
                  "json"
                  );  
             break; 
             case "admin_notes":
                  $.post("<?php echo domain; ?>core/simcheck.php?action=admin_notes",
                  {"admin_notes":$("textarea[name=admin_notepad]").val()},
                  function(data){
                      if(data.notice == "success"){aux2.alertz("Successfully saved notes!",0,false,"on_top");}                                                                       
                  },
                  "json"); 
             break;
        }  
    break;
    case "submit_sql":  
        $.post("<?php echo domain; ?>core/simcheck.php?action=sql_q",
        {"sql_q":$("#" + $(this).attr('refer')).val()},
        function(message){
            $("#t_wrap").html(message);
        });
    break;
    case "generate_password":
        $.get("<?php echo domain; ?>core/simcheck.php",{action:"generate_password"},function(res){
            switch(($("#chax .res_list").length > 0)){ 
                case true:
                    $("#chax tr.res_list").empty().detach();
                break;
                case false:
                    //lol nothing it turns out, although I might need something here later 
                break;   
            }
            $("#chax").append(res);
        });
    break;    
}

if(/^v[\137]/g.test($(this).attr("href"))){
if($(".selected_link").attr("href") != $(this).attr("href")){

$.get("<?php echo domain; ?>core/simcheck.php",{"action":"change_panels","view":$(this).attr("href")});


$(".selected_link").removeClass("selected_link");  

$(".open").removeClass("open");

$(this).addClass("selected_link");

$("#admin_panel").attr("currently_open",$(this).attr("href")); 
//adjust the width for each different panel
//the pains of making a draggable admin panel: Captivate(TM)
//yay math calculations that can't be inline because intended concatenation will be confused for a math operator
//I mean...operand

$("div#" + $(this).attr("href")).addClass("open not_yet");
diff = $("#panels").outerWidth() - 10;  
$(".open").attr("style","width:"+ diff +"px").removeClass("not_yet");

}
}

});

});