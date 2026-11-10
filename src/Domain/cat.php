<?php
namespace App\Domain;

class Cat 
{
    public int $age;
    public string $name;
    public bool $isAlive;
    public int $saturation; //min 0 - max 100
    public int $happiness; //min 0 - max 100

    public const MAX_HAPPINESS=100;
    public const MAX_SATURATION=100;
    public const MIN_HAPPINESS = 0;
    public const MIN_SATURATION = 0;

    public const HUNGRY = 30;
    public const FEED = 80;

    public const SAD = 30;
    public const HAPPY = 80;


    

    public function __construct(
        public readonly string $color,
        string $name, 
        int $age
    ){
        $this->age = $age;
        $this->name = $name;
        $this->isAlive = true;
        $this->happiness = 50;
        $this->saturation = 50;

    }

    public function pet(): string {
        $this->happiness+=5;
        $check = $this->checkCat();
        $response = "MEOW".$check["status"];
        if ($check["reaction"]!=="") {
            $response.=PHP_EOL.$check["reaction"];
        }
        return
        "MEOW".PHP_EOL
        .$response;
        // добавить чек кат и тд +счастье
    }

    public function feed(): string {
        $this->saturation+=10;
        $this->happiness+=10;
        $check = $this->checkCat();
        $response = $check["status"];
        if ($check["reaction"]!=="") {
            $response.=PHP_EOL.$check["reaction"];
        }
        return
        "Вы покормили котика".PHP_EOL
        .$response;
        // поменять все на константы короче
    }

    public function play(): string {
        $behaviours = [
            "meow",
            "*aura farms*",
            "mystic",
            "silly",
            "smarty",
            "*void*",
            "*leaves you for food*",
            "*MEOW*",
            "0w0",
        ];
        $this->happiness += 10;
        $this->saturation -= 10;
        $behaivour = $behaviours[array_rand($behaviours)];
        $check = $this->checkCat();
        $response = $behaivour.PHP_EOL
        .$check["status"];

        if ($check["reaction"]!=="") {
            $response.=PHP_EOL.$check["reaction"];
        }

        return $response;
        // поменять все на константы короче
    }

    public function checkCat(): array {
        /*Защита*/
        if ($this->saturation<=self::MIN_SATURATION) {
            return [
                "status" => "Голод: 0/100",
                "reaction" => $this->death()
            ];
        }
        if ($this->saturation>self::MAX_SATURATION) {$this ->saturation = self::MAX_SATURATION;}
        if ($this->happiness<self::MIN_HAPPINESS) {$this->happiness = self::MIN_HAPPINESS;}
        if ($this->happiness>self::MAX_HAPPINESS) {$this -> happiness = self::MAX_HAPPINESS;}


        $status = "Счастье:$this->happiness/100\nГолод:$this->saturation/100";
        $reaction = "";

        if($this->saturation<=self::HUNGRY){
            $this->happiness-=10;
            $reaction .= "$this->name сильно проголодался.\nОн ОЧЕНЬ громко мяучет :<\n";
        } elseif ($this->saturation>=self::FEED) {
            $reaction .= "$this->name не голоден, рад вас видеть!\n";
        } 

        if ($this->happiness<=self::SAD) {
            $reaction .= "$this->name очень грустит, попробуйте поиграть с ним\n";
        } elseif ($this->happiness>=self::HAPPY) {
            $reaction .= "$this->name самый счастливый котик в мире, можете гордиться собой!\n";
        }

        return [
            "status"=> $status,
            "reaction" => $reaction
        ];

    }

    public function yearlyVetLab(): string {
        $check = $this->checkCat();
        $status = $check["status"];

        if ($check["reaction"]!=="") {
            $diagnose = $check["reaction"];
        } else {
            $diagnose = "Здоров";
        }
        $info = <<<END
        Имя: $this->name
        Цвет: $this->color
        Возраст: $this->age
        Состояние: $diagnose
        $status
        END;
        return $info;
    }

    public function death() {
        $this->isAlive=false;
        return
        "YOUR CAT IS DEAD\nby lenin was a Zombie".PHP_EOL
        ."Вы проиграли :<";
    }

} 

?>