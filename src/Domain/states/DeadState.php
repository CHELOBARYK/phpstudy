<?php
namespace App\domain\states;

use App\Domain\states\CatStateInterface;
use App\Domain\Cat;

class DeadState implements CatStateInterface {
    // idk why it doesnt accept infoType $cat as an argument but accepts just $cat

    public function pet(Cat $cat): array {
        return [[
            "key"=>"cat.dead.pet",
            "params" => [
                "name"=>$cat->name
            ]
        ]];
    }

    public function feed(Cat $cat): array {
        return [[
            "key"=>"cat.dead.feed",
            "params" => [
                "name"=>$cat->name
            ]
        ]];
    }
    public function play(Cat $cat): array {
        return [[
            "key"=>"cat.dead.play",
            "params" => [
                "name"=>$cat->name
            ]
        ]];
    }
    public function applyOfflineTime(Cat $cat, $hours): array {
        return [
            "key"=>"cat.dead.time",
            "params" => [
                "name"=>$cat->name,
                "hours" => $hours
            ]
        ];
    }

    public function yearlyVetLab(Cat $cat): array {
        $randNum = rand(0,20);
        if($randNum===2) {
            return [[
                "key"=>"cat.dead.enchBook",
                "params" => [
                    "name"=>$cat->name,
                    "secret_code"=>"1RSYhnIXR4"
                ]
            ]];
        }
        
        return [[
            "key"=>"cat.dead.yearly_vet_lab",
            "params" => [
                "name"=>$cat->name
            ]
        ]];
    }
}