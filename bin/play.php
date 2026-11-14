<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Cli\consolePrinter;
use App\cli\Input;
use App\cli\Menu;
use App\cli\MenuChoice;
use App\cli\UserInteraction;
use App\Domain\catFabric;
use App\Infrastructure\jsonCat;
use App\Infrastructure\Translator;

// the start of the game and entry point
$printer = new consolePrinter();
$catFabric = new catFabric();
$saveFile = new jsonCat(__DIR__."/cat.json");
$cat=$saveFile->load();
$lang = new Translator('ru_RU');
$input = new Input;

$user = new UserInteraction($printer, $lang, $catFabric, $input);

while (True) {

    if ($cat===null) {
        $printer->printText($lang->t("game.title"));
        $agreement = $user->askYesNo($lang->t("create.perm.question"));
        if($agreement===false) {exit(0);}
        $cat = $user->collectCatData();
    }

    // menu creation

    $mainMenu = new Menu($lang);
    $langMenu = new Menu($lang);

    $mainMenu
    ->addChoice(MenuChoice::Exit, "menu.exit", 0, needPause:false)
    ->addChoice(MenuChoice::Language, "menu.lang", 2, needPause:false)
    ->addChoice(MenuChoice::DeleteCat, "menu.delete", 3, needPause:false)
    ->addChoice(MenuChoice::Feed, "menu.feed", 4)
    ->addChoice(MenuChoice::Pet, "menu.pet", 5)
    ->addChoice(MenuChoice::Play, "menu.play", 6)
    ->addChoice(MenuChoice::View, "menu.view", 7)
    ;

    $langMenu
    ->addChoice(MenuChoice::ToMainMenu, "menu.back", 0, needPause:false)
    ->addChoice(MenuChoice::Russian, "menu.lang.russian", 1, needPause:false)
    ->addChoice(MenuChoice::English, "menu.lang.english", 2, needPause:false)
    ;

    $currentMenu = $mainMenu;

    // game cycle

    $game = true;
    while($game&&$cat->isAlive){
        
        $saveFile->save($cat);
        $printer->clearScreen();
        $currentMenu->render();

        // $input = readline("> ");
        $choice = $currentMenu->getChoice($input->inputText("> "));
        $printer->clearScreen();

        if($choice["action"]===MenuChoice::DeleteCat) {
            $agreement=$user->askYesNo($lang->t("menu.deletion"));
            if($agreement===true){
                $saveFile->delete();
                exit(0);
            }
        }

        match ($choice["action"]) {
             MenuChoice::Exit => exit(0),
             MenuChoice::Russian => $lang->setLanguage("ru_ru"),
             MenuChoice::English => $lang->setLanguage("en_us"),
             MenuChoice::Language => $currentMenu=$langMenu,
             MenuChoice::ToMainMenu => $currentMenu=$mainMenu,
             MenuChoice::DeleteCat => $currentMenu=$mainMenu,

             MenuChoice::Feed => $printer->printText($cat->feed()),
             MenuChoice::Pet => $printer->printText($cat->pet()),
             MenuChoice::Play => $printer->printText($cat->play()),
             MenuChoice::View => $printer->printVetInfo($cat),
             null => $input->inputText($lang->t("menu.choice.invalid"))
        };
        

        if($choice["needPause"]===true) {
            echo PHP_EOL;
            readline($lang->t("menu.choice.continiue"));
        }
            
    }
    exit(0);
}
