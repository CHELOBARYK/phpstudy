<?php
namespace App\Cli;

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

    public function printArrAndWaitEnter(array $arr, string $inputPrompt="> ") {
        $this->printArr($arr);
        readline($inputPrompt);
    }

    public function printTextAndWaitEnter(string $text, string $inputPrompt="> ") {
        $this->printText($text);
        readline($inputPrompt);
    }
}


?>