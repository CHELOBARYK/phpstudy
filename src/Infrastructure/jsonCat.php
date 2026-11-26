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
    
    function __construct(string $filepath, string $ownerId){
        $this -> filepath = $filepath."/cat_".$ownerId.".json";

    }

    function exist(): bool {
        return file_exists($this->filepath);
    }

    function load () {
        if(!$this->exist()) { return null;}
        
        $data = json_decode(file_get_contents($this->filepath),1) ?? null;
        
        if ($data===null) {return $cat = null;}

        $cat = catFabric::createCatFromArray($data, self::DATA_FORMAT);

        return $cat;
    }

    function save (Cat $cat) {
        $data = $cat->toArray(self::DATA_FORMAT);

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