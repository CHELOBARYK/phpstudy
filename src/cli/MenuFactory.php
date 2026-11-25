<?php
namespace App\Cli;

use App\cli\MenuChoice;
use App\cli\Menu;
use App\Infrastructure\Env;
use App\Infrastructure\Translator;

class MenuFactory {
    /*
        creates diff types of menu
    */
    public function __construct(
        private Translator $lang
    ){}

    private function createMenu() {
        return $menu = new Menu($this->lang);
    }

    public function createMainMenu():Menu {        
        $mainMenu = $this->createMenu();

        return $mainMenu
        ->addChoice(MenuChoice::Exit, "menu.exit", 0)
        ->addChoice(MenuChoice::Language, "menu.lang", 1)
        ->addChoice(MenuChoice::DeleteCat, "menu.delete", 2)
        ->addChoice(MenuChoice::Feed, "menu.feed", 3)
        ->addChoice(MenuChoice::Pet, "menu.pet", 4)
        ->addChoice(MenuChoice::Play, "menu.play", 5)
        ->addChoice(MenuChoice::View, "menu.yearly_vet", 6)
        ->addChoice(MenuChoice::ChangeName, "menu.cat.change_name", 7)
        ->addChoice(MenuChoice::Achievements, "menu.achievements", 8)
        ->addChoice(MenuChoice::UnboxCat, "menu.unbox", 12)

        ->addHiddenChoice("menu.secret.kill", "kill")
        ->addHiddenChoice("menu.secret.verity", "Verity")
        ->addHiddenChoice("menu.secret.in_box", "put in box")
        ->addHiddenChoice("menu.secret_code", Env::get("SECRET_CODE"))
    ;
    }

    public function createLangMenu(array $langs):Menu {
        $langMenu = $this->createMenu();
        $langMenu->addChoice(MenuChoice::ToMainMenu, "menu.back", 0);

        for($i=0; $i<count($langs); $i++) {
            $label = ["key"=>"menu.lang.name","params"=>["lang_name"=>$langs[$i]["name"]]];
            $langMenu->addChoice(MenuChoice::ChangeLang, $label, id:$i+1, payload:["locale"=>$langs[$i]["locale"]]);
        }

        return $langMenu;
    }
}