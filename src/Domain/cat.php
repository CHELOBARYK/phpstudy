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

    public const MAX_HAPPINESS = 100;
    public const MAX_SATURATION = 100;
    public const MIN_HAPPINESS = 0;
    public const MIN_SATURATION = 0;

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
    final public function getLastBday():DateTimeImmutable {return $this->lastBday;}
    final public function getHappiness():int {return $this->happiness;}
    final public function getSaturation():int {return $this->saturation;}
    final public function getName():string {return $this->name;}
    final public function getAge():int {return $this->age;}
    final public function getState():CatStateInterface {return $this->state;}

    // set
    
    final public function setHappiness(int $happiness):void {
        $this->happiness = max(self::MIN_HAPPINESS, min(self::MAX_HAPPINESS,$happiness));
    }
    final public function setSaturation(int $saturation):void {$this->saturation = max(self::MIN_SATURATION, min(self::MAX_SATURATION,$saturation));}
    final public function setState(CatStateInterface $state):void {$this->state = $state;}
    final public function setCreatedAt(DateTimeImmutable $date):void {$this->createdAt = $date;}
    final public function setLastBday(DateTimeImmutable $date):void {$this->lastBday = $date;}
    final public function setAge(int $age):void {$this->age = max(0, $age);}
    final public function increaseAge(int $increaseBy=1):void {$this->age+=$increaseBy;}
    final public function setName(string $newName):void {$this->name=$newName;}

    public function pet(): array {return $this->state->pet($this);}
    public function feed(): array {return $this->state->feed($this);}
    public function play(): array {return $this->state->play($this);}
    public function yearlyVetLab(): array {return $this->state->yearlyVetLab($this);}
    public function applyOfflineTime(int $passedTime):array {return $this->state->applyOfflineTime($this, $passedTime);}
    public function death():array {return $this->state->death($this);}
    public function unbox():array { return $this->state->unbox($this);}
    public function resurrect():array {return $this->state->ressurect($this);}


    // any cat can be boxed
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

}

?>