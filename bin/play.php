<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Cli\consolePrinter;
use App\Domain\catFabric;

// the start of the game and entry point
echo "===КОТЫ==\n";
echo "==создать кота?==\n";

$printer = new consolePrinter();
$catfabric = new catFabric();


while (True) {
    $agreement = trim(readline("1 - ДА 0 - НЕТ:\n"));

    if ($agreement === '0') {
        echo "Вы отказались!\n";
        exit(0);
    }

    if ($agreement !== '1') {
        echo "++Повторите попытку++\n";
        echo "Введите 0 или 1\n";
        continue;
    }

    $result = catFabric::EMPTY_INPUT;
    

    while ($result !== 0) {
        if ($result===catFabric::EMPTY_INPUT) {
            echo "Заполните все поля\n";
            $name = trim(readline("Имя: "));
            $age = trim(readline("Возраст: "));
            $color = trim(readline("Цвет: "));
        } elseif($result===catFabric::INVALID_AGE) {
            echo "Введите корректный возраст\n";
            $age = trim(readline("Возраст: "));
        } elseif($result===catFabric::INVALID_NAME) {
            echo "Введите корректное имя\n";
            $name = trim(readline("Имя: "));
        } elseif($result===catFabric::INVALID_COLOR) {
            echo "Введите корректный цвет\n";
            $color = trim(readline("Цвет: "));
        }
        $result = $catfabric -> validate($name, $age, $color);
    }

    exit(0);
}






?>