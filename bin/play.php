<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Cli\consolePrinter;
use App\Domain\catFabric;

// the start of the game and entry point
echo "===КОТЫ==\n";
echo "==создать кота?==\n";

$printer = new consolePrinter();
$catFabric = new catFabric();


while (True) {
    $agreement = trim(readline("1 - ДА | 0 - НЕТ:\n"));

    if ($agreement === '0') {
        echo "Вы отказались!\n";
        exit(0);
    }

    if ($agreement !== '1') {
        echo "Введите 0 или 1\n";
        continue;
    }

    $result = catFabric::EMPTY_INPUT;
    

    while ($result !== 0) {
        $printer->clearScreen();
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
        $result = $catFabric -> validate($name, $age, $color);
    }

    $cat = $catFabric->createCat($name,$age,$color);
    $printer->printVetInfo($cat);

    $menu = <<<END
            0. Выйти
            1. Покормить
            2. Поиграть
            3. Погладить
            4. Посмотреть на кота
    END;
    $game = true;

    while($game&&$cat->isAlive){
        $printer->clearScreen();
        echo $menu.PHP_EOL;
        $user = readline("Ваш выбор: ");
        $printer->clearScreen();
        switch ($user) {
            case 0:
                exit(0);
            break;
            case 1:
                $printer->printText($cat->feed());
                // $printer->printText("Вы покормили котика");
            break;
            case 2:
                $printer->printText($cat->play());
                // $printer->printText("Aww");
            break;
            case 3:
                $printer->printText($cat->pet());
            break;
            case 4:
                $printer->printVetInfo($cat);
            break;

            case "kill":
                $printer->printText($cat->death());
            break;
            case "test":
                $cat->happiness = 100;
                $printer->printText($cat->checkCat()["reaction"]);
                $cat->happiness = 30;
                $printer->printText($cat->checkCat()["reaction"]);
                $cat->saturation = 20;
                $printer->printText($cat->checkCat()["reaction"]);
            break;            
            case "Drdonut":
                echo "donut :P";
            break;

            default:
                $printer->printText("Нет такой комманды");
            break;
        }
        echo PHP_EOL;
        readline("нажмите Enter, чтобы продолжить");
    }

    exit(0);
}






?>