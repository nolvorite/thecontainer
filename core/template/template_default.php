<?php
    $img_prefix = "core/template/img/";         
    $images = [
        "logo" => [$img_prefix . "logo.png","Site Logo"],
        "stock_1" => [$img_prefix . "lolstock1.png","lolStock 1 - Site Index"]
    ];
    class set_var extends preset {
        public function html($type,$requested,$misc = ""){ global $images;
            switch($type){
                case "image":
                    if(!isset($images[$requested])){ 
                        return $this->error_notice("image not found"); 
                    }
                    else{
                        return ($misc === "alt") ? $images[$requested][1] : $images[$requested][0]; 
                    }
                break;
            } 
        }
    }
    $set_var = new set_var();
?>