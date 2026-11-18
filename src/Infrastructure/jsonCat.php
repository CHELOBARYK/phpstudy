<?php

namespace App\Infrastructure;

use App\Domain\Cat;
use App\Domain\catFabric;

/*
    отвечать + выполненные
*/


class JsonCat {

    public string $filepath;
    
    function __construct(string $filepath){
        $this -> filepath = $filepath;
    }

    function exist(): bool {
        return file_exists($this->filepath);
    }

    function load () {
        if(!$this->exist()) { return null;}

        // default variant in case of an error
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

        $cat->setHappiness($data["happiness"]);
        $cat->setSaturation($data["saturation"]);

        return $cat;
    }

    function save (Cat $cat) {
        $data = [
            "name" => $cat->name,
            "age" => $cat->age,
            "color" => $cat->color,
            "happiness" => $cat->getHappiness(),
            "saturation" => $cat->getSaturation(),
            "isDead" => $cat->isDead(),
            "isInBox" => $cat->isInBox(),
            "lastUpdated" => time()

        ];

        file_put_contents(
            $this->filepath,
            json_encode($data,JSON_PRETTY_PRINT)
        );

        // echo "Saving...\n";
        
    }

    function delete() {
        if(!$this->exist()) { return null;}

        unlink($this->filepath);
        // return "Котик удален\n";
    }

    public function getLastSaved() {
        if(!$this->exist()) {return null;}
        $data = json_decode(file_get_contents($this->filepath),1) ?? 0;
        $lastUpdated = $data["lastUpdated"] ?? 0;
        return $lastUpdated;
    }

}

?>