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

    // Language choice
    case Russian;
    case English;
    case ToMainMenu;

    // add secret choices

}

?>