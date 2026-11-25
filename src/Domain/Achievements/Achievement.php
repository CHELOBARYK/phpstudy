<?php
namespace App\Domain\Achievements;

use App\Domain\Achievements\CatEvents;

class Achievement {
    public function __construct(
        private string $id,
        private CatEvents $event,
        private string $titleKey,
        private string $descKey, 
        private string $icon = "<3"
    ) {}

    //getters
    public function getId():string {return $this->id;}
    public function getEvent():CatEvents{return $this->event;}
    public function getTitleKey():string {return $this->titleKey;}
    public function getDecrpKey():string {return $this->descKey;}
    public function getIcon():string {return $this->icon;}

}