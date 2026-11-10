<?php
namespace App\Cli;

use App\Domain\Cat;

//

class consolePrinter {
    function clearScreen(){
        echo "\e[H\e[J";
    }

    function printVetInfo(Cat $cat) {
        $this->clearScreen();
        echo "Имя: ".$cat->name.PHP_EOL;
        echo "Возраст: ".$cat->age.PHP_EOL;
        echo "Цвет: ".$cat->color.PHP_EOL;
        echo $cat->checkCat()["status"];
    }

    function printText (string $text) {
        echo $text, PHP_EOL;
    }
}


?>