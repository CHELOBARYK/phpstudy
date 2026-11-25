<?php
namespace App\domain\states;

use App\Domain\Achievements\CatEvents;
use App\Domain\states\CatStateInterface;
use App\Domain\Cat;
use App\Infrastructure\Env;
use Override;

class DeadState implements CatStateInterface {
    // idk why it doesnt accept infoType $cat as an argument but accepts just $cat
    
    public string $secretCode;

    public function __construct() {
        $this->secretCode = Env::get("SECRET_CODE") ?? "default_code";
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

    public function death(Cat $cat):array {
        return [];
    }
    
    public function ressurect(Cat $cat): array{
        $cat->setState(new AliveState);
        $cat->notifyObservers(CatEvents::Ressurected);
        $cat->setHappiness(50);
        $cat->setSaturation(50);
        return [[
            "key"=> "cat.ressurect",
            "params" => [
                "name"=>$cat->getName()
            ]]];
    }

    public function unbox(Cat $cat): array{
        return [];
    }
}