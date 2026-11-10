<?php
namespace App\Domain;

class catFabric {

    public const EMPTY_INPUT = 1;
    public const INVALID_AGE = 2;
    public const INVALID_NAME = 3;
    public const INVALID_COLOR = 4;

    function validate ($name,$age,$color) {
        if($name===""||$age===""||$color==="") {
            return self::EMPTY_INPUT;
        }

        
        if (!preg_match('/^[1-9][0-9]*$[\D]*/',$age)) {
            return self::INVALID_AGE;
        }

        if(!preg_match("/^[[:alpha:]]+/u",$name)) {
            return self::INVALID_NAME;
        }

        if(!preg_match("/^[[:alpha:]]+/u",$color)) {
            return self::INVALID_COLOR;
        }

        echo "meow";
        return 0;
    }


}


?>

