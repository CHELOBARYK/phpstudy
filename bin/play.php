<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Cli\AuthRegManager;
use App\Cli\consolePrinter;
use App\Cli\Dashboard\Dashboard;
use App\cli\Input;
use App\cli\MenuChoice;
use App\Cli\MenuFactory;
use App\cli\UserInteraction;
use App\Domain\Achievements\Achievement;
use App\Domain\Achievements\AchievementsManager;
use App\Domain\catFabric;
use App\Domain\UserFactory;
use App\Infrastructure\jsonCat;
use App\Infrastructure\Translator;
use App\Infrastructure\Env;
use App\Infrastructure\UserStorage;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();
$secretCode = Env::get("SECRET_CODE");
$lang = new Translator('en_us');
$userFactory = new UserFactory;
$printer = new consolePrinter();
$catFabric = new catFabric();
$userStorage = new UserStorage(__DIR__."../../storage/");
$input = new Input;
$menuFactory = new MenuFactory($lang);
$ui = new UserInteraction($printer, $lang, $catFabric, $userFactory, $input);
$authManager = new AuthRegManager($userStorage,$ui, $printer, $lang);

$agreement = $ui->askYesNo($lang->t("create.perm.question"));
if($agreement===false) {exit(0);}
$user = $authManager->start();
$userStorage->saveUser($user);
$achievements = new AchievementsManager($user);
$saveFile = new jsonCat(__DIR__."../../storage/", $user->getId());
$lang->setLanguage($user->getLang());
$cat=$saveFile->load();

while ($cat===null) {
        $printer->printText($lang->t("game.title"));
        $cat = $ui->collectCatData($user->getId());
        $saveFile->save($cat);
}
    $cat->attachObserver($achievements);


    // menu creation
    $mainMenu = $menuFactory->createMainMenu();
    $avaivableLangs = $lang->loadLanguages();
    $langMenu = $menuFactory->createLangMenu($avaivableLangs);

    $currentMenu = $mainMenu;
    // temp workaround
    $SystemChoices = [
        MenuChoice::Exit,
        MenuChoice::Language,
        MenuChoice::DeleteCat,
        MenuChoice::ChangeName,
        MenuChoice::ToMainMenu,
        MenuChoice::ChangeLang,
        MenuChoice::Achievements,
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
        $userStorage->saveUser($user);
        $printer->clearScreen();
        $dashboard->render($currentMenu, $cat);
        $choice = $currentMenu->parseInputinChoice($input->inputText("> "));
        $printer->clearScreen();

        if($choice["action"]===MenuChoice::DeleteCat and $ui->askYesNo($lang->t("menu.deletion"))) {
            $saveFile->delete();
            $printer->printText($lang->t("cat.deletion.warning"));
            exit(0);
        }
        //system actions
        switch ($choice["action"]) {
            case MenuChoice::Exit: exit(0);
            case MenuChoice::Achievements:
                $msgs = [];
                if($user->getAchievements()===[]) {$printer->printTextAndWaitEnter("No Achievements"); break;}
                foreach ($user->getAchievements() as $achId) {
                    $ach = $achievements->getAchievement($achId);
                    $transAch=$lang->translateArr([["key"=>$ach["titleKey"],"params"=>[]], ["key"=>$ach["descKey"],"params"=>[]]]);
                    $msgs[] = $ach["icon"]." ".$transAch[0].PHP_EOL.$transAch[1].PHP_EOL;
                }
                $printer->printArrAndWaitEnter($msgs);
            break;
            case MenuChoice::Language: $currentMenu=$langMenu; break;
            case MenuChoice::ChangeLang:
                $lang->setLanguage($choice["payload"]["locale"]);
                $user->setLang($choice["payload"]["locale"]);
            break;
            case MenuChoice::ToMainMenu: $currentMenu=$mainMenu; break;
            case MenuChoice::ChangeName: $cat->setName($ui->renameCat()); break;
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
            $achievementsArr = $achievements->showNewAchievement();
            if(!empty($achievementsArr)) {
                foreach($achievementsArr as $ach) {
                    $transAch=$lang->translateArr([["key"=>$ach->getTitleKey(),"params"=>[]], ["key"=>$ach->getDecrpKey(),"params"=>[]]]);
                    $msg = $ach->getIcon()." ".$transAch[0].PHP_EOL.$transAch[1].PHP_EOL;
                    $printer->printText($msg);
                }
            }
            $printer->printArrAndWaitEnter($lang->translateArr($messages));
        }
        $userStorage->saveUser($user);
        $saveFile->save($cat);
    }