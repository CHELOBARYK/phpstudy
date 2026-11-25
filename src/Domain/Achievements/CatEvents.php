<?php
namespace App\Domain\Achievements;

enum CatEvents {
    case Died;
    case Boxed;
    case Unboxed;
    case Ressurected;
    case ManyCats;
}