<?php 
namespace App\cli;

use App\Cli\ConsolePrinter;
use App\Domain\CatFabric;
use App\Infrastructure\Translator;

class UserInteraction {

    public function __construct(
        private ConsolePrinter $printer,
        private Translator $lang,
        private CatFabric $catFabric,
        private Input $input
    ) {}
    
    public function askYesNo (string $question) {
        $yes = ["yes","y",1,"да","д"];
        $no = ["no","n",0,"нет","н"];

        
        while (true) {
            $input = strtolower($this->input->inputText($question));

            if (in_array($input, $yes)) {return true;}
            if (in_array($input, $no)) {return false;}
            
            $this->printer->printText($this->lang->t("menu.choice.invalid"));
        }    
        
    }

    public function collectCatData () {
        $result = catFabric::EMPTY_INPUT;
        while ($result !== 0) {
            $this->printer->clearScreen();
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
            $result = $this->catFabric -> validate($name, $age, $color);
        }

        return catFabric::createCat($name,$age,$color);
    }

}



?>