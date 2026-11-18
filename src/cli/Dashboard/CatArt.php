<?php
namespace App\Cli\Dashboard;

use App\Domain\Cat;

class CatArt {

/*

- takes Cat $cat
- returns an array that contains Cat img depending on Cats stats

*/

    public static function get(Cat $cat): array {

        $happiness = $cat->getHappiness();
        $saturation = $cat->getSaturation();

        // compares, calls a static method

        if ($cat->isDead()) {
            return self::dead();
        }

        if ($cat->isInBox()) {
            return self::box();
        }

        if ($happiness>Cat::HAPPY || $saturation>=Cat::FEED) {
            return self::happy();
        }

        if ($happiness<=Cat::SAD || $saturation<=Cat::HUNGRY) {
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
    public static function box():array {
        return [
            "  |-------------|  ",
            "  |  _________  |  ",
            "  |  [ 0 w 0 ]  |  ",
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