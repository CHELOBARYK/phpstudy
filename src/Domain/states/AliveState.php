<?php
namespace App\Domain\states;

use App\Domain\states\CatStateInterface;
use App\Domain\Cat;
use DateTimeImmutable;

class AliveState implements CatStateInterface {

    public function pet($cat): array {
        $cat->setHappiness($cat->getHappiness()+5);
        $response = ["key"=>"cat.pet","params"=>["name"=>$cat->name]];
        return $this->buildResponse($cat, $response);
    }

    public function feed($cat): array {
        // поменять на константы
        $cat->setSaturation($cat->getSaturation()+10);
        $cat->setHappiness($cat->getHappiness()+10);

        $response = ["key"=>"cat.feed","params"=>["name"=>$cat->name]];
        return $this->buildResponse($cat, $response);
    }

    public function play($cat): array {

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
        $newHappy = $cat->getHappiness()+10;
        $saturation = $cat->getSaturation();
        $saturation-=10;
        $cat->setHappiness($newHappy);
        $cat->setSaturation($saturation);

        $behaivourKey = $behaviours[array_rand($behaviours)];
        $response = ["key"=>$behaivourKey, "params"=>[]];
        
        return $this->buildResponse($cat, $response);
    }

    public function applyOfflineTime(Cat $cat, int $hours): array {
        $responseKey = "cat.passed_time";
        $now = new DateTimeImmutable();

        $isBday = $now->format("d") === $cat->getCreatedAt()->format("d");
        $lastBday = $cat->lastBday->format("Y-m-d") === $now->format("Y-m-d");

        if ($isBday and !$lastBday) {
            $responseKey = "cat.bday";
            $cat->lastBday = $now;
            $cat->age++;
        }
        elseif ($hours === 0) {
            return []; // if 0 time passed then nothing
        }

        if ($hours === -25) {
            return $cat->box();
        }

        $cat->setSaturation($cat->getSaturation()-(Cat::SATURATION_PENALTY*$hours));
        $cat->setHappiness($cat->getHappiness()-(Cat::HAPPINESS_PENALTY*$hours));

        if ($cat->getSaturation()<=0) {
            return $cat->death();
        }

        return $this->buildResponse($cat, [
            "key"=>$responseKey,
            "params"=>["passed_time"=>$hours,"name"=>$cat->name, "age"=>$cat->age]
        ]); 
    }
    
    public function yearlyVetLab(Cat $cat): array {

        $cat->age++;
        $info[] = [
            "key"=> "cat.yearly.vet.lab.header",
            "params" => [
                "name" =>$cat->name,
                "age" =>$cat->age,
                "color" =>$cat->color
            ]
        ];

        $check = $cat->checkCat();
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

    public function buildResponse(Cat $cat, array $message):array {
        $check = $cat->checkCat();
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

}