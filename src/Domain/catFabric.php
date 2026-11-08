<?php
namespace App\Domain;

class createCat {

}

echo "===КОТЫ==\n";
echo "==создать кота?==\n";
$agreement = (int) trim(readline("1 - ДА\n0 - НЕТ: "));

    switch ($agreement){
        case 1:
            $name = (string) readline("Имя: ") ?: "Kitty";
            $color = (string) readline("Цвет: ") ?: "Orange";
            $age = (int) readline("Возраст (цифрами): ");
            if($age<0) {
                $age=0;
                echo "Введенный возраст неверен\nКотик будет младенцем";
            } 
            
            echo "====Ваш кот===\n";
            echo $name.PHP_EOL.$color.PHP_EOL.$age.PHP_EOL;
        break;

        case 0:
        case "":
            echo "Вы отказались создать кота";
        break;

        default:
            echo "Напишите число";
        break;
    }


?>