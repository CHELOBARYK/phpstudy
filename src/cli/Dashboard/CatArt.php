<?php
namespace App\Cli\Dashboard;

use App\Domain\Cat;
use App\domain\states\BoxState;

class CatArt {

/*

- takes Cat $cat
- returns an array that contains Cat img depending on Cats stats

*/

    public static function get(Cat $cat): array {

        $happiness = $cat->getHappiness();
        $saturation = $cat->getSaturation();
        
        $catAmount = 1;
        $state = $cat->getState();
        if ($state instanceof BoxState) {
            $catAmount = $state->countCats();
        }

        // compares, calls a static method

        if ($cat->isDead()) {
            return self::dead();
        }

        if ($cat->isInBox()) {
            return self::box($catAmount);
        }

        if ($happiness>Cat::HAPPY and !($saturation<=Cat::HUNGRY)) {
            return self::happy();
        }

        if ($happiness<=Cat::SAD) {
            return self::sad();
        }

        return self::neutral();

    }

    public static function dead():array {
        return [
            "      /\\___/\\     ",
            "     / x _ x \     ",
            "     \   ^   /     ",
            "     /       \     ",
            "     |        |     ",
            "     /  | | | |     ",
        ];
    }
    public static function box(int $catAmount):array {
        $eyes = "  0 w 0  ";
        if ($catAmount>1) {$eyes = "owo} {owo";}
        return [
            "  ┌-------------┐  ",
            "  |  _________  |  ",
            "  | [$eyes] |  ",
            "  |             |  ",
            "  |             |  ",
            "  |_____________|  ",
        ];
    }
    public static function happy():array {
        return [
            "      /\\___/\\     ",
            "  <3 / _   _ \     ",
            "     \   w   /     ",
            "     /       \     ",
            "     |        |     ",
            "     /  | | | |     ",
        ];
    }
    public static function sad():array {
        return [
            "      /\\___/\\     ",
            "     / 0 . 0 \     ",
            "     \   ^ ] /     ",
            "     /       \     ",
            "     |        |     ",
            "     /  | | | |     ",
        ];
    }
    public static function neutral():array {
        return [
            "      /\\___/\\     ",
            "     / O _ O \     ",
            "     \   ^   /     ",
            "     /       \     ",
            "     |        |     ",
            "     /  | | | |     ",
        ];
    }
}