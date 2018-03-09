<?php
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

define("current_url","http://" . $_SERVER['SERVER_NAME'] .$_SERVER['PHP_SELF']);
define("domain",preg_replace("#(index[.]php|core[/](.+)[.]php)[/]?#","",current_url)) ;
$internal_var = new internal_var();

?>