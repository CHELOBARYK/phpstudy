<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Cli\consolePrinter;
use App\cli\Menu;
use App\cli\MenuChoice;
use App\Domain\catFabric;
use App\Infrastructure\jsonCat;
use App\Infrastructure\Translator;

// the start of the game and entry point
$printer = new consolePrinter();
$catFabric = new catFabric();
$saveFile = new jsonCat(__DIR__."/cat.json");
$cat=$saveFile->load();
$lang = new Translator('ru_RU');

while (True) {

    if ($cat===null) {
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

        $cat = catFabric::createCat($name,$age,$color);
        $saveFile->save($cat);
        $printer->printVetInfo($cat);
    }

    // menu creation

    $mainMenu = new Menu($lang);
    $langMenu = new Menu($lang);

    $mainMenu
    ->addChoice(MenuChoice::Exit, "menu.exit", 0)
    ->addChoice(MenuChoice::Feed, "menu.feed", 1)
    ->addChoice(MenuChoice::Pet, "menu.pet", 2)
    ->addChoice(MenuChoice::Play, "menu.play", 3)
    ->addChoice(MenuChoice::View, "menu.view", 4)
    ->addChoice(MenuChoice::Language, "menu.lang", 5)
    ->addChoice(MenuChoice::DeleteCat, "menu.delete", 6);

    $langMenu
    ->addChoice(MenuChoice::ToMainMenu, "menu.back", 0)
    ->addChoice(MenuChoice::Russian, "menu.lang.russian", 1)
    ->addChoice(MenuChoice::English, "menu.lang.english", 2);

    $currentMenu = $mainMenu;

    // game cycle

    $game = true;
    while($game&&$cat->isAlive){

        $printer->clearScreen();
        $saveFile->save($cat);
        $currentMenu->render();

        $user = readline("> ");
        $printer->clearScreen();
        $choice = $currentMenu->getChoice($user);

        if($choice === MenuChoice::Language) {
            $currentMenu=$langMenu;
            continue;
        }
        if($choice === MenuChoice::ToMainMenu) {
            $currentMenu=$mainMenu;
            continue;
        }

        match ($choice) {
             MenuChoice::Exit => exit(0),
             MenuChoice::Feed => $printer->printText($cat->feed()),
             MenuChoice::Pet => $printer->printText($cat->pet()),
             MenuChoice::Play => $printer->printText($cat->play()),
             MenuChoice::View => $printer->printVetInfo($cat),
             MenuChoice::DeleteCat => $printer->printVetInfo($cat),
             MenuChoice::Russian => $lang->setLanguage("ru_ru"),
             MenuChoice::English => $lang->setLanguage("en_us"),
             null => $printer->printText($lang->t("menu.choice.invalid"))
        };

        echo PHP_EOL;
        $saveFile->save($cat);
        readline($lang->t("menu.choice.continiue"));
        
    }

    exit(0);
}

?>