<?php
namespace App\Domain;

use DateTimeImmutable;

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

    static function createCat($name,$age,$color,$ownerId) {
        $name = trim($name);
        $age = trim($age);
        $color = trim($color);
        $ownerId = trim($ownerId);
        $cat = new Cat($color,$name, (int) $age, $ownerId);
        return $cat;
    }
    
    public static function createCatFromArray(array $data, string $timeformat) {
        $cat = Cat::fromArray($data);
        
        if (!isset($data["createdAt"])) {
            $data["createdAt"] = new DateTimeImmutable()->format($timeformat);
        }
        if (!isset($data["lastBday"])) {
            $data["lastBday"] = new DateTimeImmutable()->format($timeformat);
        }
        $createdAt = DateTimeImmutable::createFromFormat($timeformat, $data["createdAt"]);
        $lastBday = DateTimeImmutable::createFromFormat($timeformat, $data["lastBday"]);
        
        $cat->setCreatedAt($createdAt ?: new DateTimeImmutable());
        $cat->setLastBday($lastBday ?: new DateTimeImmutable());

        $isDead = $data["isDead"];
        $isInBox = $data["isInBox"];

        if ($isDead===true) {
            $cat->death();
        }
        if ($isInBox===true) {
            $cat->box();
        }
        

        return $cat;
    }

}


?>

