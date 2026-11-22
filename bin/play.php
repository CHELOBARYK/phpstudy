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
use App\Infrastructure\Env;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$secretCode = Env::get("SECRET_CODE");

// the start of the game and entry point
$printer = new consolePrinter();
$catFabric = new catFabric();
$saveFile = new jsonCat(__DIR__."/cat.json");
$cat=$saveFile->load();
$lang = new Translator('en_us');
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
    ->addChoice(MenuChoice::ChangeName, "menu.cat.change_name", 7, needPause:false)
    ->addChoice(MenuChoice::UnboxCat, "menu.unbox", 12)
    //refactor later, addHiddenChoice
    ->addHiddenChoice("menu.secret.kill", "kill")
    ->addHiddenChoice("menu.secret.verity", "Verity")
    ->addHiddenChoice("menu.secret.in_box", "put in box")
    ->addHiddenChoice("menu.secret_code", $secretCode )
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

        $mainMenu->changeVisibility(12, $cat->isInBox());

        $saveFile->save($cat);
        $printer->clearScreen();
        $dashboard->render($currentMenu, $cat);


        // $input = readline("> ");
        $choice = $currentMenu->parseInputinChoice($input->inputText("> "));
        $printer->clearScreen();


        if($choice["action"]===MenuChoice::DeleteCat) {
            $agreement=$user->askYesNo($lang->t("menu.deletion"));
            if($agreement===true){
                $saveFile->delete();
                exit(0);
            }
        }

        if($choice["action"]===$secretCode and $cat->isDead()) {
            $printer->printArr($lang->translateArr($cat->resurrect()));
            $input->inputText($lang->t("menu.choice.continiue"));
            continue;
        } elseif ($choice["action"]===$secretCode) {
            continue;
        }

        match ($choice["action"]) {
             MenuChoice::Exit => exit(0),
             MenuChoice::Russian => $lang->setLanguage("ru_ru"),
             MenuChoice::English => $lang->setLanguage("en_us"),
             MenuChoice::Language => $currentMenu=$langMenu,
             MenuChoice::ToMainMenu => $currentMenu=$mainMenu,
             MenuChoice::DeleteCat => $currentMenu=$mainMenu,
             MenuChoice::ChangeName => $cat->setName($user->renameCat()),
             MenuChoice::UnboxCat => $printer->printArr($lang->translateArr($cat->unbox())),

             MenuChoice::Feed => $printer->printArr($lang->translateArr($cat->feed())),
             MenuChoice::Pet => $printer->printArr($lang->translateArr($cat->pet())),
             MenuChoice::Play => $printer->printArr($lang->translateArr($cat->play())),
             MenuChoice::View => $printer->printArr($lang->translateArr($cat->yearlyVetLab())),

             "kill" => $printer->printArr($lang->translateArr($cat->death())),
             "Verity" => $printer->printText($cat->setHappiness(100)),
             "put in box" => $printer->printArr($lang->translateArr($cat->box())),
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
