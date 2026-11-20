<?php
namespace App\Domain;

use App\Domain\states\AliveState;
use App\domain\states\BoxState;
use App\Domain\states\DeadState;
use App\Domain\states\CatStateInterface;
use DateTimeImmutable;

class Cat {

    public int $age;
    public string $name;
    private bool $isDead;
    private bool $isInBox;
    private int $saturation; //min 0 - max 100
    private int $happiness; //min 0 - max 100
    private CatStateInterface $state;

    private const MAX_HAPPINESS = 100;
    private const MAX_SATURATION = 100;
    private const MIN_HAPPINESS = 0;
    private const MIN_SATURATION = 0;

    public const HUNGRY = 30;
    public const FEED = 80;

    public const SAD = 30;
    public const HAPPY = 80;

    public const SATURATION_PENALTY = 5;
    public const HAPPINESS_PENALTY = 5;
    
    private DateTimeImmutable $createdAt;
    public DateTimeImmutable $lastBday;
    

    public function __construct(
        public readonly string $color,
        string $name, 
        int $age
    ){
        $this->age = $age;
        $this->name = $name;
        $this->happiness = 50;
        $this->saturation = 50;
        $this->state = new AliveState;
        $this->createdAt = new DateTimeImmutable();
        $this->lastBday = new DateTimeImmutable();
    }
    // get
    
    final public function isDead():bool {return $this->state instanceof DeadState;}
    final public function isInBox():bool {return $this->state instanceof BoxState;}
    final public function getCreatedAt():DateTimeImmutable {return $this->createdAt;}
    final public function getHappiness():int {return $this->happiness;}
    final public function getSaturation():int {return $this->saturation;}
    final public function getState():CatStateInterface {return $this->state;}

    // set
    
    final public function setHappiness(int $happiness): string {
        $this->happiness = max(self::MIN_HAPPINESS, min(self::MAX_HAPPINESS,$happiness));
        return ":)";
    }
    final public function setSaturation(int $saturation):void {
       $this->saturation = max(self::MIN_SATURATION, min(self::MAX_SATURATION,$saturation));
    }

    final public function setState(CatStateInterface $state):void {$this->state = $state;}
    final public function setCreatedAt(DateTimeImmutable $date) {$this->createdAt = $date;}
    
    public function pet(): array {
        return $this->state->pet($this);  
    }

    public function feed(): array {
        return $this->state->feed($this);
    }

    public function play(): array {
        return $this->state->play($this);
    }

    public function yearlyVetLab(): array {
        return $this->state->yearlyVetLab($this);
    }

    public function applyOfflineTime(int $passedTime):array {
        return $this->state->applyOfflineTime($this, $passedTime);
    }

    public function death():array {
        $this->setState(new DeadState);
        return [[
            "key"=> "cat.death.joke",
            "params" => []
        ],[
            "key"=> "cat.death.msg",
            "params" => []
        ]];
    }

    public function box():array {
        $this->isInBox=true;
        $this->isDead=false;

        $this->setState(new BoxState);
        return [[
            "key"=> "cat.box.msg",
            "params" => []
        ]];
    }

    public function resurrect():array {
        $this->isInBox=false;
        $this->isDead=false;
        $this->state=new AliveState;
        $this->setHappiness(50);
        $this->setSaturation(50);
        return [[
            "key"=> "cat.ressurect",
            "params" => [
                "name"=>$this->name
            ]
        ]];
    }
    
    //helper
    public function borderStats() {
        $this->setHappiness($this->happiness);
        $this->setSaturation($this->saturation);
    }    

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

}

?>