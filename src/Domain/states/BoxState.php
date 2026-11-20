<?php
namespace App\domain\states;

use App\Domain\states\CatStateInterface;
use App\Domain\Cat;

class BoxState implements CatStateInterface {
    private int $counter=0;

    public function __construct(int $counter = 1) {
        $this->counter = $counter;
    }
    public function countCats():int {
        return $this->counter;
    } 
    public function addCat():void {
        $this->counter++;
    } 
    
    public function pet(Cat $cat): array {
        return $this->addJoke([
            "key"=>"cat.box.pet",
            "params" => [
                "name"=>$cat->getName()
            ]
        ]);
    }

    public function feed(Cat $cat): array {
        return $this->addJoke([
            "key"=>"cat.box.feed",
            "params" => [
                "name"=>$cat->getName()
            ]
        ]);
    }
    public function play(Cat $cat): array {
        return $this->addJoke([
            "key"=>"cat.box.play",
            "params" => [
                "name"=>$cat->getName()
            ]
        ]);
    }

    public function applyOfflineTime(Cat $cat, $hours): array {
        return $this->addJoke([
            "key"=>"cat.box.time",
            "params" => []
        ]);
    }
    
    public function yearlyVetLab(Cat $cat): array {
    
        return $this->addJoke([
            "key"=>"cat.box.yearly_vet_lab",
            "params" => ["name"=>$cat->getName()]]);
    }

    public function addJoke(array $message):array {
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