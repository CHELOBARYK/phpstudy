<?php

namespace App\cli;

enum MenuChoice {

    // Main menu 
    case Exit;
    case Feed;
    case Pet;
    case Play;
    case View;
    case Language;
    case DeleteCat;
    case ChangeName;
    case UnboxCat;
    case ChangeLang;

    // Language choice
    case ToMainMenu;
    case Achievements;
}

?>