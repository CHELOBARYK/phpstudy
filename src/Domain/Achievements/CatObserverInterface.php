<?php
namespace App\Domain\Achievements;

use App\Domain\Cat;
use App\Domain\Achievements\CatEvents;

interface CatObserverInterface {
    public function update(CatEvents $event, Cat $cat);
}