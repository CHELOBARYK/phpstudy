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
        
        if (!ctype_digit($age)) {
            return self::INVALID_AGE;
        }

        if(!preg_match("/^[[:alpha:]]+/u",$name)) {
            return self::INVALID_NAME;
        }

        if(!preg_match("/^[[:alpha:]]+/u",$color)) {
            return self::INVALID_COLOR;
        }

        // echo "meow";
        return 0;
    }

    function createCat($name,$age,$color) {
        $name = trim($name);
        $age = trim($age);
        $color = trim($color);
        $cat = new Cat($color,$name, (int) $age);
        return $cat;
    }

}


?>

