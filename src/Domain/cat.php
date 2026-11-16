<?php
namespace App\Domain;

class Cat 
{
    public int $age;
    public string $name;
    private bool $isAlive;
    private int $saturation; //min 0 - max 100
    private int $happiness; //min 0 - max 100

    private const MAX_HAPPINESS = 100;
    private const MAX_SATURATION = 100;
    private const MIN_HAPPINESS = 0;
    private const MIN_SATURATION = 0;

    private const HUNGRY = 30;
    private const FEED = 80;

    private const SAD = 30;
    private const HAPPY = 80;


    

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
    // get
    
    final public function IsAlive():bool {
        return $this->isAlive;
    }
    final public function getHappiness():int {
        return $this->happiness;
    }
    final public function getSaturation():int {
        return $this->saturation;
    }

    // set
    
    final public function setHappiness(int $happiness):void {
        $this->happiness = max(self::MIN_HAPPINESS, min(self::MAX_HAPPINESS,$happiness));
    }
    final public function setSaturation(int $saturation):void {
       $this->saturation = max(self::MIN_SATURATION, min(self::MAX_SATURATION,$saturation));
    }
    
    public function pet(): array {
        $this->happiness+=5;
        $response = ["key"=>"cat.pet","params"=>["name"=>$this->name]];
        return $this->buildResponse($response);
        
    }

    public function feed(): array {
        // поменять все на константы короче
        $this->saturation+=10;
        $this->happiness+=10;
        $response = ["key"=>"cat.feed","params"=>["name"=>$this->name]];
        return $this->buildResponse($response);
    }

    public function play(): array {
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
        $this->happiness += 10;
        $this->saturation -= 10;
        $behaivourKey = $behaviours[array_rand($behaviours)];
        $response = ["key"=>$behaivourKey, "params"=>[]];
        
        return $this->buildResponse($response);
        // поменять все на константы короче
    }

    public function borderStats() {
        $this->setHappiness($this->happiness);
        $this->setSaturation($this->saturation);
    }

    // refactor later
    public function checkCat(): array {
        /*Защита*/
        $this->borderStats();

        if ($this->saturation<=self::MIN_SATURATION) {
            return [
                "status" => [
                    "key"=> "cat.hungry",
                    "params" => []
                ],
                "reaction" => $this->death()
            ];
        }

        $status = [
            "key" => "cat.status",
            "params" => [
                "happiness" => $this->happiness,
                "saturation" => $this->saturation
            ]
        ];
        $reaction = [];

        if($this->saturation<=self::HUNGRY){
            if ($this->happiness<self::MIN_HAPPINESS) {$this->happiness = self::MIN_HAPPINESS;}
            $reaction[] = [
                "key" => "cat.low.hunger",
                "params" => ["name" => $this->name]
            ];
        } elseif ($this->saturation>=self::FEED) {
            $reaction[] = [
                "key" => "cat.not.hungry",
                "params" => ["name" => $this->name]
            ];
        } 

        if ($this->happiness<=self::SAD) {
            $reaction[] = [
                "key" => "cat.low.happiness",
                "params" => ["name" => $this->name]
            ];
        } elseif ($this->happiness>=self::HAPPY) {
            $reaction[] = [
                "key" => "cat.high.happiness",
                "params" => ["name" => $this->name]
            ];
        }

        return [
            "status"=> $status,
            "reaction" => $reaction
        ];

    }

    public function buildResponse(array $message):array {
        $check = $this->checkCat();
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

    public function yearlyVetLab(): array {
        $this->age++;
        $info[] = [
            "key"=> "cat.yearly.vet.lab.header",
            "params" => [
                "name" =>$this->name,
                "age" =>$this->age,
                "color" =>$this->color
            ]
        ];

        $check = $this->checkCat();
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

    public function death():array {
        $this->isAlive=false;
        return [[
            "key"=> "cat.death.joke",
            "params" => []
        ],[
            "key"=> "cat.death.lose",
            "params" => []
        ]];
    }

} 

?>