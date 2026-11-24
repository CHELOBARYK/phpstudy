<?php

namespace App\Infrastructure;

use App\Domain\Cat;
use App\Domain\catFabric;
use DateTimeImmutable;

/*
    отвечать + выполненные
*/


class JsonCat {

    private string $filepath;
    private const DATA_FORMAT = "Y-m-d h:i:s";
    
    function __construct(string $filepath){
        $this -> filepath = $filepath;

    }

    function exist(): bool {
        return file_exists($this->filepath);
    }

    function load () {
        if(!$this->exist()) { return null;}
        
        $data = json_decode(file_get_contents($this->filepath),1) ?? null;
        
        if ($data===null) {return $cat = null;}

        $cat = catFabric::createCat($data["name"],$data["age"],$data["color"]);
        
        $isDead = $data["isDead"];
        $isInBox = $data["isInBox"];

        if ($isDead===true) {
            $cat->death();
        }
        if ($isInBox===true) {
            $cat->box();
        }
        
         if (!isset($data["createdAt"])) {
            $data["createdAt"] = new DateTimeImmutable()->format(self::DATA_FORMAT);
        }
        if (!isset($data["lastBday"])) {
            $data["lastBday"] = new DateTimeImmutable()->format(self::DATA_FORMAT);
        }

        $cat->setHappiness($data["happiness"]);
        $cat->setSaturation($data["saturation"]);
        $cat->setCreatedAt(DateTimeImmutable::createFromFormat(self::DATA_FORMAT, $data["createdAt"]));
        $cat->setLastBday(DateTimeImmutable::createFromFormat(self::DATA_FORMAT, $data["lastBday"]));

        return $cat;
    }

    function save (Cat $cat) {
        $data = [
            "name" => $cat->getName(),
            "age" => $cat->getAge(),
            "color" => $cat->color,
            "happiness" => $cat->getHappiness(),
            "saturation" => $cat->getSaturation(),
            "isDead" => $cat->isDead(),
            "isInBox" => $cat->isInBox(),
            "lastUpdated" => time(),
            "createdAt" => $cat->getCreatedAt()->format(self::DATA_FORMAT),
            "lastBday" => $cat->getLastBday()->format(self::DATA_FORMAT)
        ];

        file_put_contents(
            $this->filepath,
            json_encode($data,JSON_PRETTY_PRINT)
        );
        
    }

    function delete() {
        if(!$this->exist()) { return null;}

        unlink($this->filepath);
    }

    public function getLastSaved() {
        if(!$this->exist()) {return null;}
        $data = json_decode(file_get_contents($this->filepath),1) ?? 0;
        $lastUpdated = $data["lastUpdated"] ?? 0;
        return $lastUpdated;
    }

}

?>