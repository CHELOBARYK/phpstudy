<?php
namespace App\domain\states;

use App\Domain\states\CatStateInterface;

class BoxState implements CatStateInterface {
    // idk why it doesnt accept infoType $cat as an argument but accepts just $cat
    public function pet($cat): array {
        return $this->buildResponse([
            "key"=>"cat.box.pet",
            "params" => [
                "name"=>$cat->name
            ]
        ]);
    }

    public function feed($cat): array {
        return $this->buildResponse([
            "key"=>"cat.box.feed",
            "params" => [
                "name"=>$cat->name
            ]
        ]);
    }
    public function play($cat): array {
        return $this->buildResponse([
            "key"=>"cat.box.play",
            "params" => [
                "name"=>$cat->name
            ]
        ]);
    }

    public function applyOfflineTime($cat, $hours): array {
        return $this->buildResponse([
            "key"=>"cat.box.time",
            "params" => []
        ]);
    }
    
    public function yearlyVetLab($cat): array {
    
        return $this->buildResponse([
            "key"=>"cat.box.yearly_vet_lab",
            "params" => ["name"=>$cat->name]]);
    }

    public function buildResponse(array $message):array {
        $jokesArr = [
            "cat.box.joke_0",
            "cat.box.joke_1",
            "cat.box.joke_2",
            "cat.box.joke_3",
            "cat.box.joke_4",
            "cat.box.joke_5",
            "cat.in_box"
        ];
        $response = []; // []
        $response[] = $message; //[0]
        $response[] = ["key"=>$jokesArr[array_rand($jokesArr)],"params"=>[]]; // [1]

        return $response; // крутой arr

        
    }

}