<?php
namespace App\Domain\states;

use App\Domain\states\CatStateInterface;
use App\Domain\Cat;
use DateTimeImmutable;

class AliveState implements CatStateInterface {

    public function pet(Cat $cat): array {
        $cat->setHappiness($cat->getHappiness()+5);
        $response = ["key"=>"cat.pet","params"=>["name"=>$cat->getName()]];
        return $this->buildResponse($cat, $response);
    }

    public function feed(Cat $cat): array {
        // поменять на константы
        $cat->setSaturation($cat->getSaturation()+10);
        $cat->setHappiness($cat->getHappiness()+10);

        $response = ["key"=>"cat.feed","params"=>["name"=>$cat->getName()]];
        return $this->buildResponse($cat, $response);
    }

    public function play(Cat $cat): array {

        $behaviours = [
            "cat.behaivour.meow",
            "cat.behaivour.aura_farm",
            "cat.behaivour.smart",
            "cat.behaivour.silly",
            "cat.behaivour.void",
            "cat.behaivour.loaf",
            "cat.behaivour.leaves_for_food",
            "cat.behaivour.mega_meow",
            "cat.behaivour.owo",
        ];
        $cat->setHappiness($cat->getHappiness()+10);
        $cat->setSaturation($cat->getSaturation()-10);

        $behaivourKey = $behaviours[array_rand($behaviours)];
        $response = ["key"=>$behaivourKey, "params"=>[]];
        
        return $this->buildResponse($cat, $response);
    }

    public function applyOfflineTime(Cat $cat, int $hours): array {
        $responseKey = "cat.passed_time";
        $now = new DateTimeImmutable();

        $isBday = $now->format("d") === $cat->getCreatedAt()->format("d");
        $lastBday = $cat->getLastBday()->format("Y-m-d") === $now->format("Y-m-d");

        if ($isBday and !$lastBday) {
            $responseKey = "cat.bday";
            $cat->setLastBday($now);
            $cat->increaseAge();
        }
        elseif ($hours === 0) {
            return [];
        }

        if ($hours <= -25) {
            return $cat->box();
        }

        $cat->setSaturation($cat->getSaturation()-(Cat::SATURATION_PENALTY*$hours));
        $cat->setHappiness($cat->getHappiness()-(Cat::HAPPINESS_PENALTY*$hours));

        if ($cat->getSaturation()<=0) {
            return $cat->death();
        }

        return $this->buildResponse($cat, [
            "key"=>$responseKey,
            "params"=>["passed_time"=>$hours,"name"=>$cat->getName(), "age"=>$cat->getAge()]
        ]); 
    }
    
    public function yearlyVetLab(Cat $cat): array {

        $cat->increaseAge();
        $info = [];

        $info[] = [
            "key"=> "cat.yearly.vet.lab.header",
            "params" => [
                "name" =>$cat->getName(),
                "age" =>$cat->getAge(),
                "color" =>$cat->color
            ]
        ];

        $check = $this->tickAndReport($cat);
        $info[] = $check["status"];
        if (!empty($check["reaction"])) {
            foreach ($check["reaction"] as $reaction) {
                $info[] = $reaction;
            }
        } else {
            $info[] = ["key"=>"cat.diagnose.healthy","params"=>[]];
        }

        /*
            тут не используем buildResponse т.к.
            тут есть второй вариант $check["reaction"]
        */ 

        return $info;
    }

    public function unbox(Cat $cat): array {return [];}

    public function death(Cat $cat): array{
        $cat->setState(new DeadState);
        return [[
            "key"=> "cat.death.joke",
            "params" => []
        ],[
            "key"=> "cat.death.msg",
            "params" => []]];
    }

    public function ressurect(Cat $cat): array{
        return [];
    }

    private function buildResponse(Cat $cat, array $message):array {
        $check = $this->tickAndReport($cat);
        $response = []; // []
        $response[] = $message; //[0]
        $response[] = $check["status"]; // [1]
        if (!empty($check["reaction"])) {
            foreach ($check["reaction"] as $reaction) {
                $response[] = $reaction;
            }
        }
        return $response; // крутой arr
    }

    private function tickAndReport(Cat $cat): array {
        $saturation = $cat->getSaturation();
        $happiness = $cat->getHappiness();
        $status = [
            "key" => "cat.status",
            "params" => [
                "happiness" => $happiness,
                "saturation" => $saturation
            ]
        ];
        $reaction = [];
        
        if ($saturation<=Cat::MIN_SATURATION) {
            return [
                "status" => [
                    "key"=> "cat.hungry",
                    "params" => []
                ],
                "reaction" => $cat->death()
            ];
        }

        $params = ["name" => $cat->getName()]; // [0, []]
        $saturationKey = '';
        $happinessKey = '';
        
        if($saturation<=Cat::HUNGRY){$saturationKey = "cat.low.hunger";}
        elseif ($saturation>=Cat::FEED) {$saturationKey = "cat.not.hunger";} 

        if ($happiness<=Cat::SAD) {$happinessKey = "cat.low.happiness";}
        elseif ($happiness>=Cat::HAPPY) {$happinessKey = "cat.high.happiness";}

        if ($saturationKey) {$reaction[] = ["key"=>$saturationKey,"params"=>$params];}
        if ($happinessKey) {$reaction[] = ["key"=>$happinessKey,"params"=>$params];}
        return [
            "status"=> $status,
            "reaction" => $reaction
        ];
    }

}