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

    public function createMainMenu(){        
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
        ->addChoice(MenuChoice::UnboxCat, "menu.unbox", 12)

        ->addHiddenChoice("menu.secret.kill", "kill")
        ->addHiddenChoice("menu.secret.verity", "Verity")
        ->addHiddenChoice("menu.secret.in_box", "put in box")
        ->addHiddenChoice("menu.secret_code", Env::get("SECRET_CODE"))
    ;
    }

    public function createLangMenu() {
        $langMenu = $this->createMenu();

        return $langMenu
        ->addChoice(MenuChoice::ToMainMenu, "menu.back", 0)
        ->addChoice(MenuChoice::Russian, "menu.lang.russian", 1)
        ->addChoice(MenuChoice::English, "menu.lang.english", 2)
        ;
    }
}