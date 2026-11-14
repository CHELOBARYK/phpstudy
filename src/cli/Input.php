<?php
namespace App\cli;

class Input {

    public function inputText(string $text) {
        return trim(readline($text));
    }

}