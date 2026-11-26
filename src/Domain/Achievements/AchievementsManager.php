<?php
namespace App\Domain\Achievements;

use App\Domain\Cat;
use App\Domain\User;
use App\Domain\Achievements\CatObserverInterface;
use App\Domain\Achievements\Achievement;

/*
    - gots notif from $cat 
    - makes an array with ach
*/

class AchievementsManager implements CatObserverInterface{
    private const ALL_ACHIEVEMMENTS = [
        "boxed" => ["id"=> "boxed", "event"=> CatEvents::Boxed, "titleKey"=> "cat.achievement.title.boxed", "descKey"=> "cat.achievement.desc.boxed", "icon"=>"<3",],
        "died" => ["id"=> "died", "event"=> CatEvents::Died, "titleKey"=> "cat.achievement.title.died", "descKey"=> "cat.achievement.desc.died", "icon"=>"<3",],
        "unboxed" => ["id"=> "unboxed", "event"=> CatEvents::Unboxed, "titleKey"=> "cat.achievement.title.unboxed", "descKey"=> "cat.achievement.desc.unboxed", "icon"=>"<3",],
        "ressurected" => ["id"=> "ressurected", "event"=> CatEvents::Ressurected, "titleKey"=> "cat.achievement.title.ressurected", "descKey"=> "cat.achievement.desc.ressurected", "icon"=>"<3",],
        "manyCats" => ["id"=> "manyCats", "event"=> CatEvents::ManyCats, "titleKey"=> "cat.achievement.title.manyCats", "descKey"=> "cat.achievement.desc.manyCats", "icon"=>"<3",],
    ];
    private array $achievements = [];

    public function __construct(
        private User $user
    ){
        $this->achievements;
    }

    public function update(CatEvents $event, Cat $cat){
        match ($event) {
            CatEvents::Boxed => $this->addAchievement(self::ALL_ACHIEVEMMENTS["boxed"]),
            CatEvents::Died => $this->addAchievement(self::ALL_ACHIEVEMMENTS["died"]),
            CatEvents::Unboxed => $this->addAchievement(self::ALL_ACHIEVEMMENTS["unboxed"]),
            CatEvents::Ressurected => $this->addAchievement(self::ALL_ACHIEVEMMENTS["ressurected"]),
            CatEvents::ManyCats => $this->addAchievement(self::ALL_ACHIEVEMMENTS["manyCats"]),
        };
    }

    public function isNewAchievement(Achievement $achievement):bool {
        return !$this->user->hasAchievement($achievement->getId());
    }

    public function addAchievement(array $params):void {
        $achievement = new Achievement(
            id: $params["id"],
            event: $params["event"],
            titleKey: $params["titleKey"],
            descKey: $params["descKey"],
            icon: $params["icon"],
        );
        if ($this->isNewAchievement($achievement)) {
            $this->achievements[] = $achievement;
            $this->user->unlockAchievement($achievement->getId());
        }
        else {return;}
    }

    public function showNewAchievement():array {
        $new = $this->achievements;
        $this->achievements = [];
        return $new;
    }

    public function getAchievement(string $id):array {
        return self::ALL_ACHIEVEMMENTS[$id];
    }
}