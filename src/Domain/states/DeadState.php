<?php
namespace App\domain\states;

use App\Domain\states\CatStateInterface;
use App\Domain\Cat;
use App\Infrastructure\Env;

class DeadState implements CatStateInterface {
    // idk why it doesnt accept infoType $cat as an argument but accepts just $cat
    
    public string $secretCode;

    public function __construct() {
        $this->secretCode = Env::get("SECRET_CODE");
    }

    public function pet(Cat $cat): array {
        return [[
            "key"=>"cat.dead.pet",
            "params" => [
                "name"=>$cat->getName()
            ]
        ]];
    }

    public function feed(Cat $cat): array {
        return [[
            "key"=>"cat.dead.feed",
            "params" => [
                "name"=>$cat->getName()
            ]
        ]];
    }
    public function play(Cat $cat): array {
        return [[
            "key"=>"cat.dead.play",
            "params" => [
                "name"=>$cat->getName()
            ]
        ]];
    }
    public function applyOfflineTime(Cat $cat, $hours): array {
        return [[
            "key"=>"cat.dead.time",
            "params" => [
                "name"=>$cat->getName(),
                "hours" => $hours
            ]
        ]];
    }

    public function yearlyVetLab(Cat $cat): array {
        $randNum = rand(0,20);
        if($randNum===2) {
            return [[
                "key"=>"cat.dead.enchBook",
                "params" => [
                    "name"=>$cat->getName(),
                    "secret_code"=> $this->secretCode
                ]
            ]];
        }
        
        return [[
            "key"=>"cat.dead.yearly_vet_lab",
            "params" => [
                "name"=>$cat->getName()
            ]
        ]];
    }
}