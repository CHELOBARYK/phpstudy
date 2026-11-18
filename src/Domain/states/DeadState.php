<?php
namespace App\domain\states;

use App\Domain\states\CatStateInterface;

class DeadState implements CatStateInterface {
    // idk why it doesnt accept infoType $cat as an argument but accepts just $cat

    public function pet($cat): array {
        return [[
            "key"=>"cat.dead.pet",
            "params" => [
                "name"=>$cat->name
            ]
        ]];
    }

    public function feed($cat): array {
        return [[
            "key"=>"cat.dead.feed",
            "params" => [
                "name"=>$cat->name
            ]
        ]];
    }
    public function play($cat): array {
        return [[
            "key"=>"cat.dead.play",
            "params" => [
                "name"=>$cat->name
            ]
        ]];
    }
    public function applyOfflineTime($cat, $hours): array {
        return [
            "key"=>"cat.dead.time",
            "params" => [
                "name"=>$cat->name,
                "hours" => $hours
            ]
        ];
    }

    public function yearlyVetLab($cat): array {
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