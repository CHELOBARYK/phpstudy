<?php
namespace App\Cli;

use App\Domain\Cat;

//

class consolePrinter {
    function clearScreen(){
        echo "\e[H\e[J";
    }

    function printVetInfo(Cat $cat) {
        echo "Name: ".$cat->name.PHP_EOL;
        echo "Age: ".$cat->age.PHP_EOL;
        echo "Color: ".$cat->color.PHP_EOL;
    }

    function printText (string $text) {
        echo $text, PHP_EOL;
    }
}


?>