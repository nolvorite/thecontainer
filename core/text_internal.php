<?php 
    $text_i = [
        "default_title" => "TheContainer",
        "no_db_selected" => "You have not selected a database to manage.<br>",
        "rad_h3" => "Root Admin Database Query",
        "selection_error" => "Selection texts must be between 2 and 25 characters, and must not be exactly 'Selection'.",
        "string_error" => "String must be between 1 and 130 characters and not contain the term 'Default Value'",
        "paragraph_error" => "Paragraph too long.",
        "integer_error" => "Default value contains non-integer characters.",
        "datetime_error" => "Invalid datetime format."         ,
        "cname_error" => "Column Name must not exceed 25 characters, and must not be called 'Column Name' or left blank.",
        "new_table" => "New table created!",
        "sql_textbox_note" => "Type your SQL query here... Any SQL query that will drop a table, or delete a row will require a password.",
        "minimize" => "<strong>&#9736;</strong><span> Minimize Panel</span>",
        "maximize" => "<strong>&#10531;</strong><span> Maximize Panel</span>",
        "enable_drag" => "<strong>&hArr;</strong><span> Enable Drag</span>",
        "disable_drag" => "<strong>&hArr;</strong><span> Disable Drag</span>",
        "fadeout_opt2" => "<stromg>&#9728;</strong><span> Disable Fadeout</span>",
        "fadeout_opt" => "<stromg>&#9729;</strong><span> Enable Fadeout</span>",
    ];
    $regex_check = [
        "blank_check" => "^[ \t]+$",
        "form_name" => "^.{2,50}$",
        "selection2" => "^.{2,25}$",
        "tf_selects" => "^(true|false)$",
        "defval_check" => "^Default Value$",
        "column_name" => "^Column Name|^[ \t]$|.{50,}$",  //match against
        "column_name_mf" => "^(?!Column Name$|[ \t]+|.?$|.{50,})",
        "default_value" => "(^Default Value$)|(^.{131,}$)", //Match against
        "selection" => "^".$text['slctn']."$|^.?$|^.{26,}$",  //this would be matching *against*, not for said regular expression
        "data_types" => "^(int|string|selection|paragraph|intangible|datetime)$",
        "email_address" => "^(?:[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*|\"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*\")@(?:(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])+)\])$",   //RFC 5322 Standard. Could have made one myself but I need one as precise as possible lol
        "marital_status" => "^(Single|Married|Divorced|Widowed)$",
        "username" => "^[A-Za-z0-9_-]{3,25}$",
        "password" => "^.{10,60}$",
        "string" => "^(.{1,130})$",
        "paragraph" => "^Default Value|.{25001,}$", //Match against
        "integer" => "^([0-9]+,?)+$",   //08/20/2017 01:00 AM
        "datetime_format" => "^(0[1-9]|1[0-2])[/](0[1-9]|[1-2][0-9]|3[0-1])[/]([0-9]{4}) (0[1-9]|1[0-2])[:]([0-5][0-9]) (AM|PM)$"
    ];
?>