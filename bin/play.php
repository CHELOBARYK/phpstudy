<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Cli\consolePrinter;
use App\Cli\Dashboard\Dashboard;
use App\cli\Input;
use App\cli\MenuChoice;
use App\Cli\MenuFactory;
use App\cli\UserInteraction;
use App\Domain\catFabric;
use App\Infrastructure\jsonCat;
use App\Infrastructure\Translator;
use App\Infrastructure\Env;


$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$secretCode = Env::get("SECRET_CODE");

$printer = new consolePrinter();
$catFabric = new catFabric();
$saveFile = new jsonCat(__DIR__."/cat.json");
$cat=$saveFile->load();
$lang = new Translator('en_us');
$input = new Input;
$menuFactory = new MenuFactory($lang);
$user = new UserInteraction($printer, $lang, $catFabric, $input);

while ($cat===null) {
        $printer->printText($lang->t("game.title"));
        $agreement = $user->askYesNo($lang->t("create.perm.question"));
        if($agreement===false) {exit(0);}
        $cat = $user->collectCatData();
        $saveFile->save($cat);
}
    // menu creation
    $mainMenu = $menuFactory->createMainMenu();
    $langMenu = $menuFactory->createLangMenu();
    $currentMenu = $mainMenu;
    // temp workaround
    $SystemChoices = [
        MenuChoice::Exit,
        MenuChoice::Language,
        MenuChoice::DeleteCat,
        MenuChoice::ChangeName,
        MenuChoice::ToMainMenu,
        MenuChoice::Russian,
        MenuChoice::English,
        "Verity",
        null,
    ];

    $lastSaveTime = $saveFile->getLastSaved();
    $HoursPassed = floor((time()-$lastSaveTime)/3600);
    $offline = $cat->applyOfflineTime($HoursPassed);
    
    if ($offline!==[]) {
        $printer->printArrAndWaitEnter($lang->translateArr($offline));
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
        $choice = $currentMenu->parseInputinChoice($input->inputText("> "));
        $printer->clearScreen();

        if($choice["action"]===MenuChoice::DeleteCat and $user->askYesNo($lang->t("menu.deletion"))) {
            $saveFile->delete();
            $printer->printText($lang->t("cat.deletion.warning"));
            exit(0);
        }

        //system actions
        switch ($choice["action"]) {
            case MenuChoice::Exit: exit(0);
            case MenuChoice::Russian: $lang->setLanguage("ru_ru"); break;
            case MenuChoice::English: $lang->setLanguage("en_us"); break;
            case MenuChoice::Language: $currentMenu=$langMenu; break;
            case MenuChoice::ToMainMenu: $currentMenu=$mainMenu; break;
            case MenuChoice::ChangeName: $cat->setName($user->renameCat()); break;
            case "Verity": $cat->setHappiness(100); break;
            case null: $input->inputText($lang->t("menu.choice.invalid"));
        };
        if(in_array($choice["action"], $SystemChoices)) {
            continue;
        }
        $messages = match ($choice["action"]) {
             MenuChoice::UnboxCat => $cat->unbox(),
             MenuChoice::Feed => $cat->feed(),
             MenuChoice::Pet => $cat->pet(),
             MenuChoice::Play => $cat->play(),
             MenuChoice::View => $cat->yearlyVetLab(),
             "kill" => $cat->death(),
             "put in box" => $cat->box(),
             $secretCode => $cat->resurrect(),
        };

        if(empty($messages)) {
            $input->inputText($lang->t("menu.choice.invalid"));
        } else {
            $printer->printArrAndWaitEnter($lang->translateArr($messages));
        }
        $saveFile->save($cat);
    }