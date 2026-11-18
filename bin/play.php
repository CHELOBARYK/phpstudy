<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Cli\consolePrinter;
use App\Cli\Dashboard\Dashboard;
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
        $saveFile->save($cat);
    }

    
    
    

    // menu creation

    $mainMenu = new Menu($lang);
    $langMenu = new Menu($lang);
    
    $mainMenu
    ->addChoice(MenuChoice::Exit, "menu.exit", 0, needPause:false)
    ->addChoice(MenuChoice::Language, "menu.lang", 1, needPause:false)
    ->addChoice(MenuChoice::DeleteCat, "menu.delete", 2, needPause:false)
    ->addChoice(MenuChoice::Feed, "menu.feed", 3)
    ->addChoice(MenuChoice::Pet, "menu.pet", 4)
    ->addChoice(MenuChoice::Play, "menu.play", 5)
    ->addChoice(MenuChoice::View, "menu.yearly_vet", 6)
    ->addChoice(MenuChoice::Kill, "menu.secret.kill", "kill",visible:false)
    ->addChoice(MenuChoice::Verity, "menu.secret.verity", "Verity",visible:false)
    ->addChoice(MenuChoice::Box, "menu.secret.in_box", "put in box",visible:false)
    ->addChoice(MenuChoice::Ressurect, "menu.secret_code", "1RSYhnIXR4" ,visible:false)
    ;
    
    $langMenu
    ->addChoice(MenuChoice::ToMainMenu, "menu.back", 0, needPause:false)
    ->addChoice(MenuChoice::Russian, "menu.lang.russian", 1, needPause:false)
    ->addChoice(MenuChoice::English, "menu.lang.english", 2, needPause:false)
    ;
    
    $currentMenu = $mainMenu;
    
    $lastSaveTime = $saveFile->getLastSaved();
    $HoursPassed = floor((time()-$lastSaveTime)/3600);
    $offline = $cat->applyOfflineTime($HoursPassed);
    
    if ($offline!==[]) {
        $printer->printArr($lang->translateArr($offline));
        $input->inputText($lang->t("menu.choice.continiue"));
        $offline=[];
    }

    // game cycle
    
    $game = true;
    $dashboard = new Dashboard($printer, $cat, $currentMenu);
    while($game){
        
        $saveFile->save($cat);
        $printer->clearScreen();
        $dashboard->render($currentMenu, $cat);


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

        if($choice["action"]===MenuChoice::Ressurect and $cat->isDead()) {
            $printer->printArr($lang->translateArr($cat->resurrect()));
            $input->inputText($lang->t("menu.choice.continiue"));
            continue;
        }

        match ($choice["action"]) {
             MenuChoice::Exit => exit(0),
             MenuChoice::Russian => $lang->setLanguage("ru_ru"),
             MenuChoice::English => $lang->setLanguage("en_us"),
             MenuChoice::Language => $currentMenu=$langMenu,
             MenuChoice::ToMainMenu => $currentMenu=$mainMenu,
             MenuChoice::DeleteCat => $currentMenu=$mainMenu,

             MenuChoice::Feed => $printer->printArr($lang->translateArr($cat->feed())),
             MenuChoice::Pet => $printer->printArr($lang->translateArr($cat->pet())),
             MenuChoice::Play => $printer->printArr($lang->translateArr($cat->play())),
             MenuChoice::View => $printer->printArr($lang->translateArr($cat->yearlyVetLab())),

             MenuChoice::Kill => $printer->printArr($lang->translateArr($cat->death())),
             MenuChoice::Verity => $printer->printText($cat->setHappiness(100)),
             MenuChoice::Box => $printer->printArr($lang->translateArr($cat->box())),
             null => $input->inputText($lang->t("menu.choice.invalid"))
        };
        

        if($choice["needPause"]===true) {
            echo PHP_EOL;
            readline($lang->t("menu.choice.continiue"));
        }
            
        $saveFile->save($cat);
    }
    exit(0);

}
