<?php session_start();
require_once("../template/text.php");
require_once("../text_internal.php");
header("Content-type:application/javascript");    ?> 


dbf_dt = {};
var aux = {

    d : tc.local(),
    d2 : new Date(),
    dflt_dfmt : "<?php echo dflt_fmt; ?>",
    prev_key : -1,
    scrolling : true,
    scroll_point : 0,
    regex_lib: {
        note_maker: "^[A-Ma-m]+$"
    },
    data_lol : {},
    last_note : false,
    last_value : 0,
    anchor: {
        anchor_jump : "",
        anchor_check : ""
    },
    
    reg_f: function(c,r,a,b){   console.log(c,r,a,b);
        if(typeof c !== "undefined" && typeof r !== "undefined"){
            flags = (typeof b !== "undefined") ? b : "gi";
            var c = new RegExp(c,flags);
            //c for regular expression, r for regex method select, and a for something that I havent thought of yet
            //a for string to match or value to evaluate/etc, b for flags
            
            var a = new String(a);
            switch(r){          
                case "match":   
                    return a.match(c);
                break;
                case "test":
                    return c.test(a);
                break;
                case "test_alt": 
                    return a.match(c) !== null;
                break;
            }
        }
          
    },
    date: function(request,offset){
        switch(request){
            case "offset":
                return (aux.d.hasDst()) ? aux.d2.getTimezoneOffset() : aux.d2.getTimezoneOffset() + 60;
            break;
            case "dlft_dfmt":
                
            break;
            case "declare_offset":
                $.get("<?php echo domain; ?>core/simcheck.php",{action:"declare_timestamp",offset:aux.date("offset")});
            break;
        }
    },
    close_alert : function(){
        $("#capsule").children().filter(function(){return $(this).parent().is("#capsule")}).unwrap();
        $(".overlay,#al3333rrtt").detach();
        
    },
    json_data : function(url,attributes,value){
        prop = {};
        if(/http\:\/\//.test(url) === false && typeof attributes === "string"){
            prop["action"] = url; 
            if(typeof attributes !== "undefined" && typeof value !== "undefined"){
                prop[attributes] = value;
            }
            url = "<?php echo domain; ?>core/simcheck.php";
        }
        else{
            if(!(typeof attributes === "array" || typeof attributes === "object")){
                attributes = {};
            }
        }
        $.ajax({
            url: url,
            data: prop,
            success: function(feedback){
                aux.set_data("crnt_json",feedback); 
            },
            dataType: "json",
            async: false
        });
        rval = aux.get_data("crnt_json");
        aux.set_data("crnt_json","");
        return rval;
    },
    user_data : function(){
        return aux.json_data("fetch","fetch","user_data");     
    },
    set_data : function(name,value){          
        if(/^[0-9a-zA-Z-_]+$/.test(name)){ 
            aux.data_lol[name] = value;
        } 
    },
    get_data : function(name){
        return aux['data_lol'][name];
    },

    scroll_height: function(){
        
        scrolltop = (/Chrome/g.test(navigator.userAgent)) ? $("html,body").scrollTop() : $(window).scrollTop();

        aux.last_value = typeof scrolltop === "number" ? scrolltop : aux.last_value;
        return scrolltop;  //damn it Chrome
    },
    tagstrip: function(str){
        return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    },
    check_str: function(str){
        return (typeof str === "undefined" || (typeof str !== "undefined" && !(str === false || str === ""))) ? true : false;
    }, 
    truth_priority: function(){     
        console.log(arguments);
        if(arguments[0] !== "false priority" && arguments[0] !== "true priority"){
            type = "false priority";
        }else{     
            type = (arguments[0] === "true priority" || arguments[0] === "false priority") ? arguments[0] : "true priority";
        } 
        if(arguments.length < 1){return false}
        else{
            startt = ((arguments.length > 1) && (arguments[0] === "true priority" || arguments[0] === "false priority")) ? 1 : 0;
            for(j = startt; j < arguments.length; j++){
                console.log(j,"start",startt,"boolean: ",arguments[j],"argument_length",arguments.length);
                switch(type){
                    case "true priority":
                        if(arguments[j] === true){return true;}
                    break;
                    case "false priority": 
                        if(arguments[j] !== true){return false;}
                    break;
                }
            }
            return (type === "false priority");
        }
       
    },    
    update_preview: function(cas3){
        switch(cas3){
            case "intangible":
                if(
                $("#form_preview").length 
                && (typeof iires2 !== "undefined" && iires2 === "true") 
                && $("input[name=intangible_ref]:checked").length
                ){
                    intz = $("input[name='intangible_ref']:checked").val();
                    col_name = [$("input[name='dt"+intz+"_name']").val(),$("select[name='dt"+intz+"_type1'] option:selected").text()];
                    html_content = "<strong>Intangible Column:</strong> "+col_name[0]+" ("+col_name[1]+")";    
                    if($("li#int_z").length){
                        $("#int_z").html(html_content);
                    }
                    else{
                        $("#propz").append("<li id='int_z'>"+html_content+"</li>");
                    }
                }
            break;
        }
    },
    get_ppl_dt: function(ret){
        dbf_dt[0] = {};   
        if($("input[name='has_ppl_dt']:checked").val() === "true"){
                           z = 0;
                           $("input[name='include_pii']:checked").each(function(){
                               dbf_dt[0][z] = [$(this).val(),$(this).attr("ref")];
                               z++;
                           });
            if(typeof ret !== "undefined" && ret === true){
                return dbf_dt[0];
            }               
        }
        
    }
    ,
    past_selection: {
        dt: {},
        update: function(c_id,valu3){
            Object.defineProperty(aux.past_selection.dt,c_id,{writable: true, value: valu3});  
        }
    } 
};   
aux.date("declare_offset");
(function ( $ ) {

jQuery.fn.changeTag = function (newTag) {
    var q = this;
    this.each(function (i, el) {
        var h = "<" + el.outerHTML.replace(/(^<\w+|\w+>$)/g, newTag) + ">";
        try {
            el.outerHTML = h;
        } catch (e) { //elem not in dom
            q[i] = jQuery(h)[0];
        }

    });
    return this;
};


jQuery.fn.loltooltip = function(s,m,a,c,k,e,d){ //can only be used within an event function scope!!
    //s = event that releases the tooltip
    //m = the message
    //a = event element, lol. Not sure how to get this without using it as a parameter but also not just getting "event"
    //c is if you want a special class for the tooltip, among other properties.
    s = (typeof s === "undefined") ? "default" : s;                  
    switch(typeof m){
        case "string":
            message = m;
        break;
        case "function":
            if(typeof m() === "string"){
                message = m();
            }else{
                message = "";
                console.log("Notice: loltooltip() has no actual message");
            }
        break;
    }                     
    current_event = a.type;
    if($("#t001t1p").length === 1){
        $("#t001t1p").empty().detach();
    }
   
    local_coords = [
        [$(this).offset().top,$(this).offset().left]
        ,
        [$(this).offset().top + $(this).outerHeight(),$(this).offset().left + $(this).outerWidth()]
    ];  //this would be bottom left
    
    pad = "";
    position = "top:"+local_coords[1][0]+"px;left:"+local_coords[0][1]+"px;";
    switch(typeof c){
        case "string":
            pad += ( aux.reg_f("([A-Za-z0-9-_]+[ ]?)+$","test",c) === true ) ? " class='"+c+"'" : "";
        break;
    }
    $("body").prepend(function(){ return ($("#tt_container").length === 0) ? "<div id='tt_container'></div>" : ""; });
    
    $("#tt_container").html(function(){return ($("#t001t1p").length === 0) ? "<div id='t001t1p' style='position:absolute;"+position+"z-index:100'"+pad+">"+message+"</div>" : ""});
    
    switch(current_event){
        case "keyup":
            ending_event = "change";
        break;
        case "click":
            ending_event = "click";
        break;
    }
    if(s !== "default"){
        ending_event = s;
    }
    //console.log(ending_event);
    $("body").on(ending_event,$(this),function(){
        sessionStorage.setItem("t001t1p_event",ending_event);
    })
    
};
  
})(jQuery);

var aux2 = {
    new_note : function(note){
        $.ajax({
            url: "<?php echo domain; ?>core/simcheck.php?action=submit_new_note",
            type: 'post',
            data: {note: note},
            dataType: 'html',
            async: false,
            success: function(res){
                if(res === "<?php echo $text['submitted']; ?>"){
                    aux2.alertz("Submitted!");
                    return_val = true;
                    $("#todo_list textarea.full").val("");
                }else{
                    return_val = false;
                    aux2.alertz(res);
                }
            }
        });
        aux.last_note = return_val;
        return return_val;
    },
    future_trigger: function(b,r,a,n){
        //b is the type of function, r is the specific function you want to call, a is a relative reference, and n is a miscellaneous setting
        //all are required.
        if(typeof b === "string" && typeof r === "string" && (typeof a === "string" || typeof a === "number") && typeof n === "string"){
            switch(b){
                case "click_on":
                    switch(r){
                        case "data_table":
                            if($(".tabz[rel='"+a+"']").length === 0){
                                //$("")
                            }
                        break;
                    }
                break;
            }
        }
        
    },
    alertz: function(s,c,r,a,p,e,d){ 
        //an alert function that is declared. This only pops up wherever the code is executed unless you set e to true
        //then it'll be delegated in the future 
        
        /*
        s = the message
        c = coordinates. either in array or jQuery object form
        if it's an object the placement of the alert widget will be wherever the object was
        if it's an array it'll be in the middle of the screen   
        r = if c is a jquery object, decide where you want to place the array relative to the object, otherwise false.
        a if there's a class you want to add to the widget (false for none)
        p if you want a specific time on the fadeout
        e if you want a specific time on the duration of the delay before fadeout
        d if you want to modify whether the alert box will filter out HTML tags(set to true if you do)
        */
        
        if(typeof s !== "undefined"){  
            s = (typeof s === "string" || typeof s === "boolean" || typeof s === "number") ? s : "(Alert!)";
            c = (typeof c === "object" || typeof c === "array") ? c : 0;
            r = (typeof r === "string" && typeof c === "object") ? r : false;
            a = (typeof a === "undefined") ? "" : a;            
            p = (typeof p === "undefined") ? 1000 : p;
            e = (typeof e === "undefined") ? 2500 : e;
            d = (typeof d === "undefined") ? false : d;
            
        
            if(c === 0){
                //get screen coordinates
                dimensions = [$(document).width(),window.innerHeight];   
                scrolltop = $("html,body").scrollTop();
            }else{
                if(c instanceof jQuery){ 
                
                    coordinates = [$(c).offset().left,$(c).offset().top];
                    dom_dim = [$(c).outerWidth(),$(c).outerHeight()];
                    console.log(coordinates,dom_dim);
                    placements = { // i'll do the -1 coordinates in the future lol, not like im ever gonna need them anyway
                        "x0y0" : [coordinates], //directly on top of the object
                        "x0y1" : [coordinates[0],coordinates[1]+dom_dim[1]+5], //directly below                  
                        "x1y0" : [coordinates[0]+dom_dim[0]+5,coordinates[1]], //directly right
                        "xm1y0" : [coordinates[0]-5,coordinates[1]], //directly left
                        "x1y1" : [coordinates[0]+dom_dim[0]+5,coordinates[1]+dom_dim[1]+5]  //pushed below and to the right  
                    };
                }
                if(c instanceof jQuery === false && typeof c[0] === "number" && typeof c[1] === "number"){
                    placements = c;
                    
                } 
            }
            if(c instanceof jQuery){
                if(typeof r !== "string" || r === false){
                    pos = [];
                    if(r === false){
                        pos[1] = "direct right";
                    }
                    
                }
                else if(typeof r === "string"){
                    //format = <on top/direct right/direct left/right bottom/direct bottom>(< ><center/right/bottom>)(< ><5>)(< ><6>) == parentheses is optional,inside tags are required
                    //on top/direct right/direct left/right bottom/direct bottom = positioning relative to the coordinate direction of the jquery element
                    //center/right/bottom = positioning relative to the edges of the jquery element
                    //5 - extra x-offset. can be negative
                    //6 - extra y-offset. can be negative
                    
                    var decodee = /^(on top|direct right|direct left|right bottom|direct bottom)([ ](center|top|bottom))?( -?[0-9]{0,2})?( -?[0-9]{0,2})?$/gi;
                    var pos = decodee.exec(r);
                    
                    if(typeof pos[4] === "string" || typeof pos[5] === "string"){
                       pos[4] = (typeof pos[4] !== "undefined") ? parseInt(pos[4]) : 0;
                       pos[5] = (typeof pos[5] !== "undefined") ? parseInt(pos[5]) : 0;    
                    }
                    if(typeof pos[1] === "undefined"){
                        pos[1] = "direct right";
                    }
                    }
                    switch(pos[1]){
                        case "on top":
                            placements = placements.x0y0; 
                        break;
                        case "direct bottom":
                            placements = placements.x0y1; 
                        break;
                        case "direct right":
                            placements = placements.x1y0; 
                        break;
                        case "direct left":
                            placements = placements.xm1y0
                        break;
                        case "right bottom":
                           placements = placements.x1y1; 
                        break;
                    
                    }
                
            }
            if(typeof a === "string"){     
                class_adds = (/^([A-Za-z0-9-_]+[ ]?)+$/.test(a)) ? a : "";
            }else{
                
                if(typeof a === "array" || typeof a === "object"){
                    if(typeof a.overlay === "string"){
                        current_position = aux.scroll_height();
                        a.prompt = (typeof a.prompt !== "undefined") ? a.prompt : true;
                        aux.scroll_point = $("html,body").scrollTop();
                            add = ($(document).height() > document.documentElement.clientHeight) ? true : false;
                        
                        switch(a.overlay){
                            case "black":
                                $("body").wrapInner("<div id='capsule'></div>").prepend("<div class='overlay' id='black_overlay' style='top:"+aux.scroll_height()+"px;left:0;' />");                  
                                
                            break;
                            
                        }
                        if(add === false){  
                            $("#capsule").addClass("no_scrollbar");
                        }
                        $("#capsule").scrollTop(current_position);         
                    }
                    class_adds = (typeof a.classes === "string" && (/^([A-Za-z0-9-_]+[ ]?)+$/.test(a.classes))) ? a.classes : ""; 
                }
                else{
                    class_adds = "";
                }
            }
            if(typeof p !== "number" || typeof e !== "number"){
                p = 1000;
                e = 400;                                                                   
            }
            message = (d === true) ? s : (aux.tagstrip(s)); //for security sake 
            message = (d === true) ? message : message.replace("\n","<br>");
            
            if(typeof c === "number" && typeof a !== "object" && typeof a !== "array"){
                class_adds = (/^$/.test(class_adds)) ? " default_alert" : " default_alert " + class_adds;
            }else{
                class_adds = " " + class_adds;
            }          
            var date = new Date();
            stamp = "a" + date.getTime();
            console.log("attempt!");
            id= (typeof a === "object" && a.prompt === true) ? "al3333rrtt" : stamp;  
            $(".alert_dflt:not('#al3333rrtt')").detach();
            $("body").prepend("<div id='"+id+"' class='alert_dflt"+class_adds+"' style='position:absolute;display:inline-block;'><div class='msg_box'>"+message+"</div></div>");    
            box_width = [$("#"+id).outerWidth(),$("#"+id).outerHeight()];  
            
            console.log(typeof dimensions);                 
            if(typeof dimensions === "array" || typeof dimensions === "object"){ //ergo not referencing a jquery object as its coordinates, or even any coordinate
                xcoord = (dimensions[0] / 2) - (box_width[0] / 2);
                ycoord = scrolltop + (dimensions[1] / 2) - (box_width[1] / 2);
                placements = [xcoord,ycoord];
            }
            else{  
                
                switch(pos[1]){
                    case "direct left":
                        placements[0] -= box_width[0];
                        if(typeof pos[3] !== "undefined"){
                            switch(pos[3]){ //the relative point of alignment from the linked jquery object
                                case "center":
                                    placements[1] = placements[1] + ( (dom_dim[1] / 2) - (box_width[1] / 2) );
                                break;
                            }
                        }
                    break;
                    case "direct right": 
                        console.log(placements[0] + $("#"+id).width(),$(document).width());
                        if(placements[0] + $("#"+id).width() > $(document).width()){
                            placements[0] = $("#"+id).position().left;
                        }
                    break;
                    case "direct bottom":
                        placements[1] -= box_width[1];
                    break;
                }
            }
            z_index = (typeof a === "object" && typeof a.prompt === "boolean") ? 150 : 200;
            z_index = (typeof a === "object" && typeof a.z_index === "number") ? a.z_index : z_index;
            $("#"+id).css({"z-index":z_index,"left":placements[0] + "px","top":placements[1] + "px"});
            
            if(typeof a === "object" && typeof a.prompt === "boolean" && a.prompt === true){
                btn_text = (typeof a.btn_text !== "undefined") ? a.btn_text : "Cancel"; 
                $("#"+id).append("<button class='submit_clone spec' id='prompt_close'>"+btn_text+"</button>");    
            }else{
                $("#"+id).fadeIn(400).delay(e).fadeOut(p); 
                if(typeof a === "object" && typeof a.overlay === "string"){
                    $(".overlay").detach();
                }
            }      
            delete dimensions,class_adds,placements; //2lazy2oopforthis    
        }                                                  
    },
    table_tabbing : {
        dt : {},
        exec : function(id){
            if(typeof aux2.table_tabbing.dt[id] === "undefined"){  
                aux2.table_tabbing.dt[id] = {
                    is_open : ($(".tabz[rel='"+parseInt(id)+"']").length === 0 || $(".tabz[rel='"+parseInt(id)+"']").hasClass("clear")) ? false : true
                }
            }
            
        },
        toggle : function(id){
            if(typeof id === "number" && typeof aux2.table_tabbing.dt[id] === "object"){
                $("a.fulltab[rel='"+id+"']").toggleClass("slctd");
                if($(".fulltab[rel='"+id+"']").next().is(".fulltab") === false)
                {
                    $(".fulltab[rel='"+id+"']").next().toggleClass("clear");
                }
                aux2.table_tabbing.exec(id);
                if($(".fulltab[rel='"+id+"']").next().is(".tabz") === false){
                $.get("<?php echo domain; ?>core/simcheck.php",{action:"table_tabs",id_name:id},function(res){ 
                    $("a.fulltab[rel='"+id+"']").after(function(){
                        return ($(".tabz[rel='"+id+"']").length) ? "" : res;
                    });        
                }); 
                }    
            }
        }
    }, // end table tabbing
    scroll_floater: function(adjustment){
        adjustment = parseInt(adjustment) + 15;
        //get scroll height and bottom of #list_container, a reference point
        orig = parseInt($("#list_container").position().top) + parseInt($("#list_container").outerHeight());
        orig2 = parseInt($("#list_container").height()) + parseInt($("#left_menu h3").height());
            
        if(adjustment + orig2 + $("#floater").outerHeight() + 60 < ($("#right_side").outerHeight())){
            $("#floater").css("margin-top",adjustment  + "px");
        }else{
            $("#floater").css("margin-top",($("#right_side").outerHeight() - (orig2 + $("#floater").outerHeight() + 60)) + "px");
        }
        //change #floater margin to 10 pixels + scroll height - . But don't let the margin make it exceed the height of #panel and leave it alone if it's less than 0.
            
          
    }
}

                      
setInterval(function(){
    if($("#t001t1p").length === 1 && typeof tt_declare === "undefined"){  
        tt_declare = true;
    }
    else{
        if($("#t001t1p").length === 0){
            delete tt_declare;
        }
    }
    if(aux.anchor.anchor_jump !== ""){
        console.log(aux.anchor.anchor_check);
        set = [aux.anchor.anchor_jump,aux.anchor.anchor_check];
        aux.anchor.anchor_jump = "";
        aux.anchor.anchor_check = "";
        if(set[1] !== ""){
            switch(set[1]){
                case "new_tab":
                    $(".leftmnu[link='formsmanage']").trigger("click");
                    //get a separate handler for the anchor
                    anchor_2 = "new_tab";               
                break;
            }   
        }              
    } 
},200);  


setInterval(function(){
    $(".table_itself").each(function(){
        var rel = $(this).parent().attr("rel");
        if(typeof aux.data_lol[rel] === "undefined"){
            aux.data_lol[rel] = false;
        }
        height = $(this).css("max-height");
        height = parseInt(height.replace("px",""));      
        if($(this).scrollLeft() > 0 || $(this).scrollLeft() !== $(".hidex[rel='"+rel+"']").scrollLeft()){
            left_scr = $(this).scrollLeft();
            $(".hidex[rel='"+rel+"']").scrollLeft(left_scr);
        }                                   
        if($(this).find("table").outerHeight() > height){
            if($(this).scrollTop() > 0 && $(this).prev().is(".hidex") === false){
                strict_width = $(this).prop("clientWidth");
                current_width = $(this).find("table").width();   
                positions = [$(this).prevAll("h4").position().left,$(this).prevAll("h4").position().top + $(this).prevAll("h4").outerHeight()];
                 
                $(this).before("<div id='xcvm' class='hidex' rel='"+rel+"' style='width:"+strict_width+"px;position:absolute;left:"+positions[0]+"px;top:"+positions[1]+"px'><div class='clone' style='width:"+current_width+"px;'></div></div>");          
                $(this).find("tr:first").contents().clone(true,true).appendTo("#xcvm .clone");      
                
                actual_width = {};
                counter = 0; 
                $(this).find("tr:first td,th").each(function(){
                    actual_width[counter] = $(this).width();
                    counter++;
                });
                
                counter = 0;
                
                $(this).prev(".hidex").find("td,th").each(function(){
                        $(this).removeAttr("width").addClass("fakecell").css("width",actual_width[counter]+"px").changeTag("div");
                    counter++; 
                });
                $("#xcvm").removeAttr("id"); 
            }else{
                if($(this).prev().is(".clone")){
                    var height_check = $(this).prev(".clone").outerHeight();
                    console.log(height_check,$(this).scrollTop(),height_check,aux.data_lol[rel]);
                    if($(this).scrollTop() < height_check && aux.data_lol[rel] === false){
                        aux.set_data(rel,true);                 
                        $(this).prev(".clone").addClass("clear");
                        var height_check = $(this).prev(".clone").outerHeight();
                    } 
                    if($(this).scrollTop() > height_check && aux.data_lol[rel] === true){
                        aux.set_data(rel,false);                      
                        $(this).prev(".clone").removeClass('clear');
                        var height_check = $(this).prev(".clone").outerHeight();
                    }
                }
            }
        }
    });
    
    if(typeof anchor_2 !== "undefined"){  //I can't believe this shit
        switch(anchor_2){
            case "new_tab":
                zz = $("a[name='newdbf']").position();
                $("html,body").scrollTop(zz.top);  
            break;     
        }
        delete anchor_2;
    }    
},500);



actions = {
    display_fp: function(){
        console.log("#form_preview about to be created");   
        $("#fp_slot").append(function(){
            return ($("#form_preview").length) ? "" : "<div id='form_preview'><h3><?php echo $text['form_preview']; ?></h3><div class='white_bg'><ul id='char_x'></ul></div><div class='right_align'><button class='submit_clone spec' id='createform' ref='new_dbform'><?php echo $text['create_form']; ?></button></div></div>";
        });
        int_counter = 0;
        
        $("#char_x").html("").prepend(function(){
            return ($("#form_props").length) ? "" : "<li id='form_props'><strong>Table Properties</strong><ul id='propz'></ul><br></li>";
        });
        db_deets = {
             tbl_name: $("input[name='form_name']").val(),
             ppl_dt: $("input[name='has_ppl_dt']:checked").val(),
             is_editable: $("input[name='is_editable']:checked").val(),
             intngbl: $("input[name='is_intangible']:checked").val()
        };
        console.log(db_deets);
        dbdisp = [
             (db_deets.tbl_name === "Form Name" || /^[ \t]{0,}$/.test(db_deets.tbl_name)) ? "<em>[Unnamed]</em>" : db_deets.tbl_name,
             db_deets.ppl_dt === "true" ? "<?php echo $text['t1']; ?>" : "<?php echo $text['t6']?>",
             db_deets.is_editable === "true" ? "<?php echo $text['t2']; ?>" : "<?php echo $text['t5']?>",
             db_deets.intngbl === "true" ? "<?php echo $text['t3']; ?>" : "<?php echo $text['t4']?>"
        ];
        dbdisp3 = (db_deets.ppl_dt === "true") ? "" : "<li>"+dbdisp[3]+"</li>";
        $("#propz").html("<li>Table Name: "+dbdisp[0]+"</li><li>"+dbdisp[1]+"</li><li>"+dbdisp[2]+"</li>" + dbdisp3);
        aux.get_ppl_dt();
        console.log(dbf_dt);
        if($("input[name='has_ppl_dt']:checked").val() === "true"){
            for(i = 0; i <= Object.keys(dbf_dt[0]).length - 1; i++){   
                htmlo = "<li>"+dbf_dt[0][i][1]+"</li>";
                l1st = typeof l1st === "undefined" ? htmlo : l1st + htmlo;
            }
            l1st = "<ul>"+l1st+"</ul>";
            $("#propz").after("<ul id='default_pi'><li><strong>Selected Personal Information Columns:</strong>"+l1st+"</li></ol>");
            delete l1st; //ew I know I know
        } 
        $(".oneform[column_id]").each(function(){  
            cid = $(this).attr("column_id");
            function hay(val){
                return "input[name='dt"+cid+"_"+val+"']";
            }
            var deets = {
                col_name: $(this).find(hay("name")).val() === "Column Name" ? "[Unnamed]" : aux.tagstrip($(this).find(hay("name")).val()),
                dt_type: aux.tagstrip($(this).find("select[name='dt"+cid+"_type1']").val()),
                defval: $(this).find(hay("defval")).val() === "Default Value" ? "<?php echo $text['lolnone']; ?>" : aux.tagstrip($(this).find(hay("defval")).val()),
                is_editable: $(this).find("input[name='dt"+cid+"_is_editable2']:checked").length ? aux.tagstrip($(this).find("input[name='dt"+cid+"_is_editable2']:checked").val()) : "" 
            };            
            if(deets.is_editable !== ""){                                        
                isedtbl_txt = (deets.is_editable !== "true") ? "<?php echo $text['isedtbl1']; ?>" : "<?php echo $text['isedtbl2']; ?>";
            }else{
                isedtbl_txt = "<?php echo $text['isedtbl2']; ?>";
            }
            switch(deets.dt_type){
                case "int":
                    deets.dt_disp = "<?php echo $text['int']; ?>";    
                break;
                case "string":
                    deets.dt_disp = "<?php echo $text['string']; ?>";    
                break;
                case "paragraph":
                    deets.dt_disp = "<?php echo $text['prgrph']; ?>";    
                break;
                case "intangible":
                    deets.dt_disp = "<?php echo $text['intngblz']; ?>";    
                break;
                case "datetime":
                    deets.dt_disp = "<?php echo $text['datetime']; ?>"
                break;
                case "selection":
                    deets.dt_disp = "<?php echo $text['slctn']; ?>";
                    $("input[name^='dt"+cid+"_selection']").each(function(){ console.log($(this).val());
                        slct_list = (typeof slct_list === "undefined") ? "<li>"+$(this).val()+"</li>" : slct_list + "<li>"+$(this).val()+"</li>";
                    });
            
                break;
            }
            slct_list = typeof slct_list === "undefined" ? "" : slct_list;
            slct_type = $("input[name='dt"+cid+"_is_multi']:checked").length ? "(<?php echo $text['multislct']; ?>)" : "(<?php echo $text['singleslct']; ?>)";
            slct_check = (deets.dt_type === "selection") ? "<li><strong>Selections:</strong> "+slct_type+"<ol>"+slct_list+"</ol></li>" : "";
            console.log((deets.col_name === "[Unnamed]" || deets.col_name === ""));
            $("#char_x")
            .append(function(){ return ((deets.col_name === "[Unnamed]" || deets.col_name === "") && $(".oneform[column_id]").length === 1) ? "" : "<li><strong>"+deets.col_name+"</strong><ul><li>Data Type: "+deets.dt_disp+"</li><li>Default Value: "+deets.defval+"</li>"+slct_check+"<li>Is it editable: <strong>"+deets.is_editable+"</strong></li></ul></li>"});
            aux.update_preview("intangible");
            delete slct_list;
            delete slct_check;
        });
        
    } 
};

setInterval(function(){         //form preview
    //check to see if the first column's name is unfilled and there's no other columns(essentially an unfilled form), or if the personal information radio is set to false(
    //if both are true, delete #form_preview)
    //if either are false, display the graph
        ts0 = typeof creating_form !== "undefined";
        ts1 = (($("input[name=dt1_name]").val() === "" || ($("input[name=dt1_name]").val() === $("input[name=dt1_name]").prop("defaultValue"))) && $(".oneform[column_id]").length === 1);
        ts2 = $("input[name='has_ppl_dt']:checked").val() === "false";  
        console.log(ts1,ts2);
        if(ts1 && ts2){
            $("#form_preview").empty().detach();
        }else{
            console.log(ts1,ts2);
            if((!ts1||!ts2) && ts0){
                actions.display_fp();
            }
        }
},15000);

$(document).ready(function(){
    
    $(window).on("scroll",function(event){
        if($("#floater").html().length > 0){
            aux2.scroll_floater(aux.scroll_height());
        }
    });     
    $("body").on("mouseover",".spec4",function(event){
        switch($(this).attr("ref")){
            case "toggle_sort":
                message = typeof $(this).attr("message") === "undefined" ? "<?php echo $text['sort_2']; ?>" : $(this).attr("message");
                aux2.alertz(message,$(this),"direct left center","sort_tips",500,800);
            break;
            case "table_cell":   
                $(".table_view .options2").addClass('clear');                                                           
                reference = $(this).parents(".table_view").attr("ref");
                idee = $(this).attr("ad_id");
                
                $(".table_view .options2[rel='"+idee+"']").removeClass('clear');       
                positions = [
                    $(this).position().top + 4,
                    $(this).position().left + $(this).outerWidth() - $(".table_view .options2[rel='"+idee+"']").outerWidth() - 10
                ];
                $(".table_view .options2[rel='"+idee+"']").css({top: positions[0]+"px",left:positions[1]+"px"});
            break;
        }
    });
    $("body").on("keyup keydown",".spec5",function(event){
        if(typeof $(this).attr("ref") !== "undefined"){
            //parse the ref value into different strings separated by a space
            //then loop for each parsed value 
            parsed = $(this).attr("ref").split(" ");
            for(i = 0;i < parsed.length; i++){
                switch(parsed[i]){
                    case "regex_check":   
                        if(typeof $(this).attr("rel") === "string"){
                            reg_exx = $(this).attr("rel");
                            reg_exx = /^\/.+\/$/.test(reg_exx) ? reg_exx.replace(/^\/(.+)\/$/,"$1") : aux.regex_lib[reg_exx];
                            test = typeof $(this).attr("r_test") === "string" ? $(this).attr("r_test") : "test" ;
                            msg = (aux.reg_f(reg_exx,test,$(this).val()) === true) ? "valid" : "not valid";
                            if(aux.reg_f(reg_exx,test,$(this).val()) === true){           
                                $(this).addClass("valid").removeClass("not_valid");
                                classes = "formcheck valid";
                            }else{
                                $(this).addClass("not_valid").removeClass("valid");
                                classes = "formcheck not_valid";
                            }
                            if(aux.truth_priority("false priority",$(this).val() === "",($(this).prop("defaultValue") === "" || $(this).hasClass("flick")))){$(this).removeClass("not_valid valid");}
                            aux2.alertz(msg,$(this),"direct left center",classes,0,5000);
                        }
                    break;   
                    case "note_maker":
                        switch(event.type){
                            case "keyup":
                                switch(event.keyCode){ 
                                    case 13:    
                                        console.log(event.shiftKey);
                                        if(aux.prev_key >= 0 && (aux.prev_key !== 16 && event.shiftKey === false)){
                                            msg = $(this).val();
                                            aux2.new_note(msg);
                                            if(aux.last_note === true){
                                                if($(".note").length === 0){$("#notes").empty();}
                                                $.get("<?php echo domain; ?>core/simcheck.php",{action:"get_latest_note"},function(dt){
                                                    //note_date = new Date(dt.note_date);
                                                    $("#notes").prepend("<div class='note' note_id='"+dt.note_id+"'><div class='note_content'>"+dt.note+"</div><span class='prop date'>"+dt.note_date+"<div class='options2' rel='$int'><button class='small_btn spec2' ref='edit_cell' rel='$int'>Edit</button><button class='small_btn spec2' ref='delete_cell' type='notes' rel='$int'>Delete</button></div></span></div>");
                                                },"json");
                                            }
                                        }
            
                                    break;
                                }
                                console.log(aux.prev_key);
                                aux.prev_key = event.keyCode;
                            break;
                            case "keydown":
                                if(aux.prev_key >= 0 && (aux.prev_key !== 16 && event.shiftKey === false) && event.keyCode === 13){
                                    event.preventDefault();
                                }
                            break;
                        }
                }    
            }
            
        }
        
    });
    $("html").on("click","body *:not('.spec')",function(xe2){
        if($("#t001t1p").length && $(this).parents("#tt_container").length === 0 && $(this).attr("id") !== "tt_container"){
            //ill have to find a way to execute in other tooltip_exit event delegations later, not just clicking
            $("#t001t1p").empty().detach();
        }
    });
    setInterval(function(){
        if($(".dated").length){
            if(typeof y === "undefined" || y !== $(".dated").length){
                var y = $(".dated").length;
                $(".dated").each(function(){
                    $(this).datetimepicker({format: 'm/d/Y h:i A',formatTime: "h:i A"});
                });
            }
        }
    },250);
    $("input[name='form_name']").datetimepicker();
    $("html").on("click",".spec",function(xex){
        if($("#t001t1p").length && $(this).parents("#tt_container").length === 0 && $(this).attr("id") !== "tt_container"){
            $("#t001t1p").empty().detach();
        }
        xex.preventDefault();           
        switch($(this).attr("id")){ //buttons goooooo
            case "login_btn":
                $(this).hide().after("<?php echo $filler['login_opt']; ?>");
            break;
            case "signupp":
                $.get("<?php echo domain; ?>core/simcheck.php",{action:"filler_request",filler:"signup"},function(res){
                    aux2.alertz(res,0,false,{overlay:"black",z_index:150,classes:"register",btn_text:"Close"},0,0,true);
                });
                
            break;
            case "new_db_form":
                creating_form = true;
                $("#form_list").after(function(){
                return ($("#new_form").length) ? "" : "<div id='new_form' class='silver_bg'></div>";
                }); 
                if($("#new_form").html().length === 0){
                    $.get("<?php echo domain; ?>core/simcheck.php",{action: "filler_request", filler: "new_form", db_aff: $("div[reff=actual_content]").attr("ref")},function(res){ 
                        $("#new_form").html(res);
                    });
                }
            break;
            case "prompt_close":
                aux.close_alert();
                $("html,body").scrollTop(aux.scroll_point);
            break;
            case "addnewform":    
                $.get("<?php echo domain; ?>core/simcheck.php",{action: "filler_request", filler: "new_column"},function(res){
                    $(".oneform:last").after(res);     
                    if(typeof iires2 !== "undefined" && iires2 === "true"){        
                        cid = $(".oneform:last").attr("column_id");           
                        $(".oneform:last").find(".box1:first .box2:last").append("<span class='is_intangible'><?php echo $text['intngbl_ref']; ?></span>");                   
                        }
                });
            break;
            case "createform": //AJAX post requests
                switch($(this).attr("ref")){
                    case "new_dbform":
                        var dbf_dt = {};
                        dbf_dt.settings = {
                            form_name : $("input[name='form_name']").val(),
                            has_ppl_dt : $("input[name='has_ppl_dt']:checked").val(),
                            is_editable : $("input[name='is_editable']:checked").val(),
                            is_intangible : $("input[name='is_intangible']:checked").val()
                        }
                        dbf_dt[0] = aux.get_ppl_dt(true);
                        $(".oneform[column_id]").each(function(){
                            var cid = $(this).attr("column_id");
                            dbf_dt[cid] = {};
                            $(this).find("input").filter(function(){
                                return ($(this).parents(".clear").length === 0)
                            }).each(function(){
                                if(!($(this).attr("type") === "radio" && $(this).is(":checked") === false)){                                   
                                    inp_n = $(this).attr("name");
                                    val = $(this).val();
                                    dbf_dt[cid][inp_n] = val;
                                }  
                            });
                            dt_type = "dt"+cid+"_type1";
                            dbf_dt[cid][dt_type] = $("select[name^='dt"+cid+"_type1']").val();
                        });
                        console.log(dbf_dt);
                        
                        $.ajax({
                            url: "<?php echo domain; ?>core/simcheck.php?action=quick_submit&req=new_dbform",
                            type: 'post',
                            data: dbf_dt,
                            dataType: 'html',
                            async: false,
                            success: function(result) { console.log(result);  
                                if(result === "<?php echo $text_i['new_table']; ?>"){
                                    aux2.alertz("New database form created!");
                                    
                                    //get scrolltop
                                    aux.anchor.anchor_jump = "#newdbf";
                                    aux.anchor.anchor_check = "new_tab";
                                    $("#form_preview").empty().detach();
                                  
                                } 
                            } 
                        });
                    break;
                }
            break;
        }
        if($(this).hasClass("leftmnu")){  var link = $(this).attr("link");
            $.get("<?php echo domain; ?>core/simcheck.php",{action: "display_page", pg_display: $(this).attr("link")},function(res){  
                $("#shoop").html(res);
                $("#floater *").empty();
                
                switch(link){        
                    case "formsmanage":
                        if($("#quick_tbtab").length === 0 && aux.user_data().has_selected_db){    
                            $("<div id='quick_tbtab' />").prepend("<h3><?php echo $text['current_tables'];?></h3>").appendTo("#qbt_slot");        
                        }
                        
                        $(".fulltab.spec2[rel]").each(function(){      
                            rel = $(this).attr("rel");
                            aux2.table_tabbing.exec(rel);
                            $("#quick_tbtab").append("<a href='#"+$(this).attr('name')+"'>"+$(this).html()+"</a>");
                        });                        
                        console.log(aux2.table_tabbing.dt);
                        
                    break;
               }
                    
                
            });
        } 
        if($(this).hasClass("ra_db_slct")){
            $.get("<?php echo domain; ?>core/simcheck.php",{action: "admin_db_save",db_id: $(this).attr("ref")},function(res){  
                $("a.leftmnu[link=formsmanage]").trigger("click");
            });
        }
    });
    //special event on forms
    $("body").on('focus','.flick',function(event){
        if($(this).val()==$(this).prop("defaultValue")){
        $(this).attr("extra",$(this).val()).val("");       
    }})
    .on('blur','.flick',function(event){if($(this).val().length === 0){
        $(this).val($(this).prop("defaultValue"));
    } })
    .on('change','.flick',function(event){  
        if($(this).val().length === 0 || $(this).val() === $(this).prop("defaultValue")){$(this).removeClass("named")}else{$(this).addClass("named")}
        if($(this).val().length == 0){
            $(this).val($(this).prop("defaultValue")); //had to change it to this for textareas
    }});
    $("body").on("change",".spec2",function(event){ //event delegations on more than one document object
        if($(this).attr("ref").length){
            rel = $(this).attr("rel");
            switch($(this).attr("ref")){

                case "order":  
                    if($(this).val() === "excludes"){    
                        if(typeof $(".selector[rel='"+rel+"']").data("original_val") === "undefined"){
                            $(".selector[rel='"+rel+"']").data("original_val", $(".selector[rel='"+rel+"']").find("option[value='all']").html());
                        }
                        $(".selector[rel='"+rel+"']").find("option").filter(function(){return ($(this).val() === "all") }).html("Exclude by query on column...");
                    }else{
                        $(".selector[rel='"+rel+"']").find("option").filter(function(){return ($(this).val() === "all") }).html($(".selector[rel='"+rel+"']").data("original_val"));
                    }
                break;
                case "selection":    
                    if(/^dbc[n]?_[0-9]+/.test($(this).attr("name"))){
                        c_id =  $(this).attr("rel");
                        kval = $(this).attr("name") + "_2";
                    }
                    else{
                        c_id = $(this).parents(".oneform").attr("column_id");
                        kval = $(this).attr("name");
                        
                    }
                    kv2 = kval+ "_c";
                    console.log(aux.past_selection.dt);
                    if(typeof aux.past_selection.dt[c_id] === "string"){ //cleanups
                       console.log(aux.past_selection.dt[c_id]);
                       switch(aux.past_selection.dt[c_id]){
                           case "datetime":
                               if(/^dbc[n]?_[0-9]+/.test($(this).attr("name"))){
                                   $("#"+kval+" .div3").insertAfter("#"+kval);
                               }
                               else{
                                   $("#"+kval+" .box1").insertAfter("#"+kval);
                                   
                               }
                               $("#"+kv2).appendTo("#"+kval);
                           break;                             
                           case "selection":
                           case "intangible":
                               $(".spec_box[id='"+c_id+"']").empty().detach();
                           break;
                       }
                   }
                   switch($(this).val()){ //executionnn   
                       case "selection":                    
                       case "intangible":
                           switch($(this).val()){
                                case "selection":
                                    filler_req = "new_selection";
                                break;
                                case "intangible":
                                    filler_req = "intangible_link";
                                break;
                           }
                           var id = (/^dbc[n]?_[0-9]+/.test($(this).attr("name"))) ? 
                               [
                               $(this).attr("rel"),
                               $(this).attr("name")
                               ]
                            :  [
                               $(this).parents(".oneform").attr("column_id"),
                               $(this).attr("name")
                           ];
                           if(/^dbc[n]?_[0-9]+/.test($(this).attr("name"))){
                               $.get("<?php echo domain; ?>core/simcheck.php",{action:"filler_request",filler:filler_req,id_name:id[0]},function(res){                                   $("select[name='"+id[1]+"']").parent().parent().append(
                                        function(){return $(this).find(".spec_box").length ? "" : res}
                                    );
                           });
                           }else{
                               $.get("<?php echo domain; ?>core/simcheck.php",{action:"filler_request",filler:filler_req,id_name:id[0]},function(res){                                       $("select[name='"+id[1]+"']").parent().parent().nextAll(".box1:first").find(".box2").append(
                                   function(){return $(this).find(".spec_box").length ? "" : res}
                                   );
                               });
                           }
                       break; //end selection
                       case "datetime":
                           //datepair 2sexy
                           //change Default Value on left side to "Default Timestamp"
                           if(!$("#"+kval).length){
                               if(/^dbc[n]?_[0-9]+/.test($(this).attr("name"))){
                                   
                                   $(this).parent().prev(".div3").wrap("<span class='clear' id='"+kval+"' />").clone().insertAfter("#"+kval);                         
                                   defcol_row = $("#"+kval).next();
                                   
                                   $(defcol_row).attr("id",kv2).find("strong").html("<?php echo $text['def_ts']; ?>").end().find("input").addClass("flick dated");                  
                               }else{
                               
                                   $(this).parents(".oneform").find(".box1").eq(2).wrap("<span class='clear' id='"+kval+"' />").clone().insertAfter("#"+kval);                         
                                   defcol_row = $("#"+kval).next();
                                   $(defcol_row).attr("id",kv2).find("h4").html("<?php echo $text['def_ts']; ?>").end().find(".box2 input").addClass("dated");                 
                               }
                               
                           }else{
                               if(/^dbc[n]?_[0-9]+/.test($(this).attr("name"))){
                                   ke = $("#"+kval+" .div3").prop("outerHTML");
                               }
                               else{
                                   ke = $("#"+kval+" .box1").prop("outerHTML");
                                   
                               }
                               $("#"+kval).empty().after(ke);
                                   $("#"+kv2).next().appendTo("#"+kval); //eugh
                           }
                       break;                             
                   }     
                   aux.past_selection.update(c_id,$(this).val());
                break;
                
                default:
                    
                break;
            }
        }
    }).on("click",".spec2",function(xe){  
        if($(this).prop("tagName") === "A"){
            xe.preventDefault();
        }  
        rel = (typeof $(this).attr("rel") !== "undefined") ? $(this).attr("rel") : "";
        switch($(this).attr("ref")){
            case "confirm":
                edit = $("textarea[cell_id='"+$(this).attr('rel')+"']").val();
                $.ajax({
                    url: "<?php echo domain; ?>core/simcheck.php?action=confirm",
                    type: 'post',
                    data: {msg:edit},
                    success: function(feedback){
                        console.log(feedback);                        
                    },
                    dataType: "json",
                    async: false
                });
            break;
            case "button_help":
                $.get("<?php echo domain;?>core/help_pages.php",{regarding:$(this).attr("rel")},function(valu){  
                    aux2.alertz(valu,0,false,{overlay:"black",z_index:150,classes:"help_case",btn_text:"Close"},0,0,true);
                });
            break;
            case "edit_cell":
            case "delete_cell":
                ref= $(this).attr("ref");
                event = xe;
                n_type = typeof $(this).attr("type") !== "undefined" ? $(this).attr("type") : "none";
                $.get("<?php echo domain;?>core/simcheck.php",{action:"cmd",cmd:"modify_cell",task:ref,type:n_type,cell:rel},function(valu){  
                    aux2.alertz(valu,0,false,{overlay:"black",z_index:150,classes:"prompt_1"},0,0,true);
                });
            break;
            case "confirm_delete":
                $.get("<?php echo domain;?>core/simcheck.php",{action:"confirm_delete"},function(a){
                    aux2.alertz(valu,0,false,{overlay:"black",z_index:150,classes:"prompt_1"},0,0,true);
                });
            break;
            case "content_search":         
                    /*
                        GET search_parsing = type of search
                        GET search_query = statement to match or match against
                        ... search_by_column(optional)
                        ... order_by_column
                        ... sort_by descending or ascending order
                        then ofc $_GET["id"] and $_GET['column']
                    */
                    
                
                    
                var id = $(this).attr("rel");
                var searchh = {
                    "parsing" : $(".parameters[rel='"+id+"']").val(),
                    "query": $(".query[rel='"+id+"']").val(),
                    "slctr" : $(".selector[rel='"+id+"']").val(),
                    "order_by" : $(".ordering[rel='"+id+"']").val(),
                    "sort" : $(".typeof_order[rel='"+id+"']").val(),
                };
                var orig = $(".query[rel='"+id+"']").prop("defaultValue");
                    
                if(orig !== searchh.query){    
                $.get("<?php echo domain; ?>core/simcheck.php",
                {search_parsing:searchh.parsing,
                search_query:searchh.query,
                search_by_column:searchh.slctr,
                order_by_column:searchh.order_by,
                id:id,
                sort:searchh.sort,
                action:"fetch",
                fetch:"advanced_adfetch"},
                function(res){          
                    if(res === "<?php echo $text['n0_results']; ?>"){
                        aux2.alertz(res);
                    }else{
                        if($("#orig_"+id).length === 0){
                            $("<div class='hide' id='orig_"+id+"' />").insertAfter("#manage_data"+id);
                            $("#manage_data"+id).contents().appendTo("#orig_"+id);   

                        }
                        $("#manage_data"+id).html(res);
                        if($(".submit_clone[rel='"+id+"'][ref='reset']").length === 0){
                            $("button[ref='content_search'][rel='"+id+"']").after("<button class='spec2 submit_clone' rel='"+id+"' ref='reset'>Reset</button>");
                        }
                    } 
                }
                );  
                }
            break;    
            case "reset":
                var id = $(this).attr("rel");
                $(this).empty().detach();
                orig_data = $("#orig_"+id).html();
                $("#manage_data"+id).html(orig_data);
                
            break;
            case "toggle_sort":
                id = $(this).attr("rel2");
                eq = parseInt($(this).attr("rel"));
                obj = ($(this).parents(".clone").length) ? ".table_view[rel='"+id+"'] table" : ".clone";       
                if($(this).attr("current_toggle")){
                    switch($(this).attr("current_toggle")){
                        case "up": //toggle to descending order
                            $(".clone,.table_view[rel='"+id+"'] table").find("button[rel='"+eq+"']").attr({"current_toggle":"down","class":"spec2 spec4","message":"<?php echo $text['sort_3']; ?>"}).addClass("toggle_back");
                            toggle_props = ["down",$(this).parent().attr("type")];
                        break;
                        case "down": //toggle to default sorting
                            $(".clone,.table_view[rel='"+id+"'] table").find("button[rel='"+eq+"']").attr({"class":"spec2 spec4","message":"<?php echo $text['sort_2']; ?>"}).removeAttr("current_toggle");  
                            toggle_props = ["back",$(this).parent().attr("type")];
                        break;
                    }    
                }else{  //toggle to ascending order
                    $(".clone,.table_view[rel='"+id+"'] table").find("button[rel='"+eq+"']").attr({"current_toggle":"up","class":"spec2 spec4","message":"<?php echo $text['sort_1']; ?>"}).addClass("toggle_down");
                    toggle_props = ["up",$(this).parent().attr("type")];
                }
                $(".table_view[rel='"+id+"'] div table tr td").removeClass("ab ba aa bb");   
                                                                       
                  
               
                $(this).parent().siblings().find(".spec2[ref]").removeClass("toggle_back toggle_down").removeAttr("current_toggle");
                
                $(obj).find("button[rel='"+eq+"']").parent().siblings().find(".spec2[ref]").removeClass("toggle_back toggle_down").removeAttr("current_toggle");
                
                
                switch(toggle_props[0]){
                    case "up": //ascending order
                        switch(toggle_props[1]){
                            case "integer": 
                               $(".table_view[rel='"+id+"'] div table tr").slice(1)
                               .sort(
                                   function(a, b){
                                       a_numeric = !isNaN(parseInt($(a).find("td").eq(eq).text())) ? parseInt($(a).find("td").eq(eq).text()) : 0;
                                       b_numeric = !isNaN(parseInt($(b).find("td").eq(eq).text())) ? parseInt($(b).find("td").eq(eq).text()) : 0;
                                       return (b_numeric < a_numeric) ? 1 : -1;  
                                   }
                               ).appendTo(".table_view[rel='"+id+"'] div table");  
                            break;
                            default:        
                               $(".table_view[rel='"+id+"'] div table tr").slice(1)
                               .sort(
                                   function(a, b){
                                       return ($(b).find("td").eq(eq).text()) < ($(a).find("td").eq(eq).text()) ? 1 : -1;  
                                   }
                               )
                               .appendTo(".table_view[rel='"+id+"'] div table");                    
                            break;
                        }
                    break;
                    case "down":
                        switch(toggle_props[1]){
                            case "integer": 
                               $(".table_view[rel='"+id+"'] div table tr").slice(1)
                               .sort(
                                   function(a, b){
                                       a_numeric = !isNaN(parseInt($(a).find("td").eq(eq).text())) ? parseInt($(a).find("td").eq(eq).text()) : 0;
                                       b_numeric = !isNaN(parseInt($(b).find("td").eq(eq).text())) ? parseInt($(b).find("td").eq(eq).text()) : 0;
                                       return (b_numeric > a_numeric) ? 1 : -1;  
                                   }
                               ).appendTo(".table_view[rel='"+id+"'] div table");  
                            break;
                            default:        
                               $(".table_view[rel='"+id+"'] div table tr").slice(1)
                               .sort(
                                   function(a, b){
                                       return ($(b).find("td").eq(eq).text()) > ($(a).find("td").eq(eq).text()) ? 1 : -1;  
                                   }
                               )
                               .appendTo(".table_view[rel='"+id+"'] div table");                    
                            break;
                        }
                    break;
                    case "back":
                        $(".table_view[rel='"+id+"'] div table tr").slice(1).sort(
                            function(a, b){  
                                return parseInt($(b).find("th").text()) < parseInt($(a).find("th").text()) ? 1 : -1;  
                            }
                        ).appendTo(".table_view[rel='"+id+"'] div table");
                    break;
                }
                
                for(i = 1; i < $(".table_view[rel='"+id+"'] div table tr").length;i++){
                    row_add = (typeof row_add === "undefined" || row_add === "b") ? "a" : "b";
                    for(j = 0;j < $(".table_view[rel='"+id+"'] div table tr").eq(i).find("td").length;j++){
                        column_add = (typeof column_add === "undefined" || column_add === "b") ? "a" : "b";
                        $(".table_view[rel='"+id+"'] div table tr").eq(i).find("td").eq(j).addClass(row_add+""+column_add);    
                    }
                    delete column_add;
                }
                
              
                
            break;
            case "submit_new_dt":
                submit_dtee = {};
                var rel = $(this).attr("rel");
                var cb_check = $(".tabz").find(".option input[name='redir_opt']").val();
                $(this).parent().find(".form_box input,.form_box select").each(function(){
                    name = $(this).attr("name");
                    submit_dtee[name] = $(this).val();     
                    
                });
                console.log(submit_dtee);
                $.post("<?php echo domain; ?>core/simcheck.php?action=quick_submit&req=submit_dtee&id=" + rel,submit_dtee,function(res){
                    if(res !== ""){
                        aux2.alertz(res);
                    }else{
                    aux2.alertz("Submitted!");
                    switch(cb_check){
                        case "on":
                            $(".leftmnu[link='formsmanage']").trigger("click");
                            window.location.href = "#"+rel;
                        break;
                        case "off":
                        break;
                    }
                    }
                });
            break;
            case "finish_edits":
                /*    get all the forms of the following:
                      - right side panel
                      - current columns
                      - selections on current columns
                      - new columns
                */
                var rel = $(this).attr("rel");
                mod_cnt = {};
                $(".tabz[rel='"+rel+"'] .dbf_settins input,.tabz[rel='"+rel+"'] .form_box input,.tabz[rel='"+rel+"'] select").filter(function(){ return ((($(this).attr("type") === "radio" || $(this).attr("type") === "checkbox") && $(this).is(":checked") === false) || ($(this).parent().parent().is(".clear"))) ? false : true }).each(function(){        
                     name = $(this).attr("name");
                     if($(this).attr("type") === "checkbox"){
                         mod_cnt[name] = typeof mod_cnt[name] === "undefined" ? [] : mod_cnt[name];
                         len = typeof mod_cnt[name] === "undefined" ? 0 : Object.keys(mod_cnt[name]).length;
                         mod_cnt[name][len] = $(this).val(); 
                     }
                     else{
                         mod_cnt[name] = $(this).val(); 
                     }
                });
                $.post("<?php echo domain; ?>core/simcheck.php?action=quick_submit&req=finish_edits&id=" + rel,mod_cnt,function(res){
                    aux2.alertz(res,$(".finish_edits[rel='"+rel+"']"),false,"f_eee");
                });
            break;
            case "delete_something":
                    if($(this).attr("type").length){
                        typee = $(this).attr("type");
                        txt = $(this).text();
                        num = $(this).attr("num");      
                        $.get("<?php echo domain; ?>core/simcheck.php",{action:"cmd",cmd:"delete_something",type: typee,num: num},
                            function(res){  console.log("a");  console.log(res);
                                if(txt === "<?php echo $text['delet']; ?>" || txt === "<?php echo $text['delet_2']; ?>"){   console.log("b");
                                    $("a[ref='delete_something'][type='"+typee+"'][num='"+num+"']").html("<?php echo $text['prompt']; ?>");
                                }
                                if(txt === "<?php echo $text['prompt']; ?>" && res === "<?php echo $text['deltd']; ?>"){
                                     console.log("c");
                                    switch(typee){
                                        case "column":
                                            $("a[ref='delete_something'][type='"+typee+"'][num='"+num+"']").html("<?php echo $text['deltd']; ?>").parent().parent().fadeOut(1000, function(){
                                                $(this).empty().detach();
                                            });
                                        break;
                                        case "selection":
                                            $("a[ref='delete_something'][type='"+typee+"'][num='"+num+"']").html("<?php echo $text['deltd']; ?>").parent().fadeOut(1000, function(){
                                                $(this).empty().detach();
                                            });
                                        break;                                    
                                    }     
                                }
                                
                            }
                        );
                    }
            break;
            case "add_new_column":
                rel = $(this).attr("rel");
                $.get("<?php echo domain; ?>core/simcheck.php",{action: "filler_request", filler: "new_column", id: $(this).attr("rel")},function(res){
                    $(".speci .spec2[rel='"+rel+"']").parent().parent().after(res);
                    if(typeof iires3[rel] !== "undefined" && iires3[rel] === "true"){                  
                        $(".speci .spec2[rel='"+rel+"']").parent().parent().next().find(".form_box .div3:first").append("<span class='is_intangible'><?php echo $text['intngbl_ref']; ?></span>");                   
                    }
                });                                                                
            break;
            case "add_selection":   console.log("abc");                  
                var ref_id = $(this).parent(".spec_box").attr("id");
                if($(this).attr("type") !== undefined){
                    switch($(this).attr("type")){
                        case "modifying":
                            $.get("<?php echo domain; ?>core/simcheck.php",{action:"filler_request",filler:"add_selection",id_name:ref_id},function(res){
                              $(".spec_box#"+ref_id+" button[ref='add_selection']").before(res); 
                            }); 
                        break;  
                    }
                }
                else{ 
                    $.get("<?php echo domain; ?>core/simcheck.php",{action:"filler_request",filler:"add_selection",id_name:ref_id},function(res){
                        $(".spec_box[id='" +ref_id+"'] button[ref='add_selection']").before(res); 
                    });       
                }
            break;                     
            case "table_tab":
                rel = parseInt($(this).attr("rel"));    
                aux2.scroll_floater(aux.scroll_height());
                aux2.table_tabbing.toggle(rel);
            break;     
            case "tabz":
                rel = $(this).attr("rel");
                
                $(this).parent().find(".selected_tab").removeClass("selected_tab");           
                $(this)
                .toggleClass("selected_tab")
                .parents(".tabz").find(".tabby").filter(function(){ return ($(this).hasClass("clear") === false);}).addClass("clear");
                $(this)
                .parents(".tabz").find(".tabby.clear[rel='"+rel+"']").removeClass("clear");
            break;                
        }
    });   
    $("body").on("click","input.tab_switcher",function(event){
        rel = $(this).attr("rel");
        key_2 = $(this).val();
        $.get("<?php echo domain; ?>core/simcheck.php",{action:"table_tabs",misc_command:'update',id_name:rel,key_2:key_2});  
    });
    
    $("body").on("click",".select",function(xe){
        $(this).toggleClass("selectd");
        $(this).find("input[type='checkbox'],input[type='radio']").each(function(){
            if($(this).is(":checked")){ 
                $(this).prop("checked", false);
            }else{
                $(this).prop("checked", true);
            }
        });
    });
    $("body").on("click",".select input[type='checkbox'],.select input[type='radio']",function(xe){
        xe.stopPropagation();
        name = $(this).attr("name");
        switch($(this).attr("type")){
            case "checkbox":
                $(this).parent(".select").toggleClass("selectd");
            break;
            case "radio":
                $("input[name='"+name+"']").toggleClass("selectd");
            break;
        }
    }); 
    $("body").on("click","input[name='def_open']",function(event){   
        rel = $(this).val();
        action = $(this).is(":checked") ? "open" : "close"; 
        console.log(action);
        $.get("<?php echo domain; ?>core/simcheck.php",{action:"table_tabs",misc_command:"tab_switch",id_name:rel,key_2:action},function(res){
            //test code here    
        });
    });
    //personal data additional inputs   
    $("body").on("click","input[name='intangible_ref']",function(event){
        if($(this).is(":checked")){
        $(".ntmarked").html('<?php echo $text['full_slct']; ?>').removeClass("ntmarked");
        cid3 = $(this).val();
        $("select[name='dt"+cid3+"_type1']").addClass("ntmarked").html('<?php echo $text['intangible_slct'];?>'); 
        $(".spec_box[id='"+cid+"']").empty().detach();
        }
    });               
    $("body").on("click","input[name='is_intangible']",function(event){
        var iires = $(this).val();
        iires2 = $(this).val(); //lol
        $(".oneform[column_id]").each(function(){ //append an option for each column if it is the intangible 
            cid = $(this).attr("column_id");
            if(iires === "true"){
                if(!$(this).find(".is_intangible").length){ 
                    $(this).find(".box1:first .box2:last").append("<span class='is_intangible'><?php echo $text['intngbl_ref']; ?></span>");
                }               
            }else{
                $(".is_intangible").empty().detach();
                $(".ntmarked").html('<?php echo $text['full_slct']; ?>').removeClass("ntmarked");
                
            }
        });
        aux.update_preview("intangible");
    }); 
    $("body").on("focus","input[name='has_ppl_dt']",function(event){
        if($(this).val() === "true"){ console.log("a");
            if(!$("#personal_infoz").length){
                $.get("<?php echo domain; ?>core/simcheck.php",{"action":"filler_request","filler":"personal_infoz"},function(res){
                    $("#formsettings").after(function(){                                                                                
                        return ($("#personal_infoz").length) ? "" : "<div id='personal_infoz'>"+res+"</div>";       })
                    .after(function(){
                        return ($("#side_piz").length) ? "" : "<span id='side_piz' class='clear'></span>";
                    });              
                });  
            }else{
                $("#personal_infoz").insertAfter("#formsettings");
            }
            $("#intngopt").find("input").each(function(){$(this).attr("disabled","")}).end().attr("style","opacity:.5").after(function(){return ($(this).next().is(".box2.inf0")) ? "" : "<div class='box2 inf0'><?php echo $text['intng_ppl']; ?></div>"; });      
        }
        else{
            $("#personal_infoz").appendTo("#side_piz");
            $("#intngopt").find("input").removeAttr("disabled").parent().parent().removeAttr("style").next().empty().detach();
        }                                               
    });     
    $("body").on("keyup",".spec3",function(event){ //events on keyup delegated on multiple inputs
        switch($(this).attr("ref")){
            case "intangible_link":
                var id = $(this).parents(".oneform").attr("column_id");
                var value = $(this).val();
                $("input[name='dt"+id+"_intlink']").loltooltip("click",  
                    function(){
                        $.ajax({
                            url: "<?php echo domain; ?>core/simcheck.php",
                            type: 'get',
                            data: {action:"json_req",dt:"table_search",table_name:value},
                            dataType: 'json',
                            async: false,
                            success: function(result) {
                                 switch(typeof result.results){
                                    case "string":
                                        html = "<?php echo $text['no_results']; ?>";
                                    break;
                                    case "array":
                                    case "object":
                                        
                                    break;
                                }
                            } 
                        });  
                        return html;
                    },event
                ); 
            break;
        }
    });
    $("#rad_check").on("keyup",function(event){
        var search_q = $(this).val();
        $(this).loltooltip("click",
            function(){
                
            $.ajax({
        url: "<?php echo domain; ?>core/simcheck.php?action=db_search_ad&search_q=" + search_q,
        type: 'get',
        dataType: 'html',
        async: true,
        success: function(data) {
            resulto = data;
        } 
     });     
           return typeof resulto === "undefined" ? "" : resulto;
            },event
        );
    });
    $("body").on("click","a.ra_db_slct",function(xex){
        xex.preventDefault();   
    });            
    $("a.leftmnu[link=formsmanage]").trigger("click");
   
});