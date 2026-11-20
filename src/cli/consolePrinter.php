<?php
namespace App\Cli;

use App\Domain\Cat;

//

Final class ConsolePrinter {
    final function clearScreen():void {
        echo "\e[H\e[J";
    }

    final function printText (string $text):void {
        echo $text, PHP_EOL;
    }

    final function printArr (array $arr):void {
        foreach($arr as $msg) {
            echo $msg.PHP_EOL;
        }
    }
}


?>