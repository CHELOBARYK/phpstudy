<?php
namespace App\Domain\states;

use App\Domain\Cat;

interface CatStateInterface {
    public function pet(Cat $cat):array;
    public function feed(Cat $cat):array;
    public function play(Cat $cat):array;
    public function applyOfflineTime(Cat $cat, int $hours):array;
    public function yearlyVetLab(Cat $cat):array;
    public function death(Cat $cat):array;
    public function ressurect(Cat $cat):array;
    public function unbox(Cat $cat):array;
}