<?php
namespace App\Domain;

use App\Domain\states\AliveState;
use App\domain\states\BoxState;
use App\Domain\states\DeadState;
use App\Domain\states\CatStateInterface;
use DateTimeImmutable;

class Cat {

    private int $age;
    private string $name;
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
    private DateTimeImmutable $lastBday;
    

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
    final public function getLastBday():DateTimeImmutable {return $this->createdAt;}
    final public function getHappiness():int {return $this->happiness;}
    final public function getSaturation():int {return $this->saturation;}
    final public function getName():string {return $this->name;}
    final public function getAge():int {return $this->age;}
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
    final public function setLastBday(DateTimeImmutable $date) {$this->createdAt = $date;}
    final public function setAge(int $age) {$this->age = max(0, $age);}
    final public function increaseAge(int $increaseBy=1) {$this->age+=$increaseBy;}
    final public function setName(string $newName) {$this->name=$newName;}

    public function pet(): array {return $this->state->pet($this);}
    public function feed(): array {return $this->state->feed($this);}
    public function play(): array {return $this->state->play($this);}
    public function yearlyVetLab(): array {return $this->state->yearlyVetLab($this);}
    public function applyOfflineTime(int $passedTime):array {return $this->state->applyOfflineTime($this, $passedTime);}

    public function unbox():array {
        if($this->state instanceof BoxState){return $this->state->unbox($this);}

        return [["key"=>"unbox.fail","params"=>[]]];
    }

    public function death():array {
        $this->setState(new DeadState);
        return [[
            "key"=> "cat.death.joke",
            "params" => []
        ],[
            "key"=> "cat.death.msg",
            "params" => []]];
    }

    public function box():array {
        if ($this->state instanceof BoxState) {
            $this->state->addCat();
            return [[
                "key"=>"cat.multi",
                "params"=>["cat_amount" => $this->state->countCats()]
            ]];
        }

        $this->setState(new BoxState);
        $text = [[
            "key"=> "cat.box.msg",
            "params" => []]];

        return $text;
    }

    public function resurrect():array {
        $this->setState(new AliveState);
        $this->setHappiness(50);
        $this->setSaturation(50);
        return [[
            "key"=> "cat.ressurect",
            "params" => [
                "name"=>$this->name
            ]]];
    }
    
    //helper  
    public function tickAndReport(): array {
        $status = [
            "key" => "cat.status",
            "params" => [
                "happiness" => $this->happiness,
                "saturation" => $this->saturation
            ]
        ];
        $reaction = [];
        
        if ($this->saturation<=self::MIN_SATURATION) {
            return [
                "status" => [
                    "key"=> "cat.hungry",
                    "params" => []
                ],
                "reaction" => $this->death()
            ];
        }

        $params = ["name" => $this->name]; // [0, []]
        $saturationKey = '';
        $happinessKey = '';
        
        if($this->saturation<=self::HUNGRY){$saturationKey = "cat.low.hunger";}
        elseif ($this->saturation>=self::FEED) {$saturationKey = "cat.not.hunger";} 

        if ($this->happiness<=self::SAD) {$happinessKey = "cat.low.happiness";}
        elseif ($this->happiness>=self::HAPPY) {$happinessKey = "cat.high.happiness";}

        if ($saturationKey) {$reaction[] = ["key"=>$saturationKey,"params"=>$params];}
        if ($happinessKey) {$reaction[] = ["key"=>$happinessKey,"params"=>$params];}
        return [
            "status"=> $status,
            "reaction" => $reaction
        ];
    }

}

?>