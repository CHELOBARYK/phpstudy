<?php
namespace App\Domain;

class CatFabric {

    public const EMPTY_INPUT = 1;
    public const INVALID_AGE = 2;
    public const INVALID_NAME = 3;
    public const INVALID_COLOR = 4;

    function validate ($name,$age,$color) {
        if($name===""||$age===""||$color==="") {
            return self::EMPTY_INPUT;
        }
        
        if (!$this->validateAge($age)){return self::INVALID_AGE;}
        if (!$this->validateName($name)){return self::INVALID_NAME;}
        if (!$this->validateColor($color)){return self::INVALID_COLOR;}

        return 0;
    }

    public function validateAge($age): bool {return !ctype_digit((int)$age);}
    public function validateName($name): bool {return preg_match("/^[[:alpha:]]+/u",$name);}
    public function validateColor($color): bool {return preg_match("/^[[:alpha:]]+/u",$color);}

    static function createCat($name,$age,$color) {
        $name = trim($name);
        $age = trim($age);
        $color = trim($color);
        $cat = new Cat($color,$name, (int) $age);
        return $cat;
    }

}


?>

