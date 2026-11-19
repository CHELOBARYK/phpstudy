<?php

namespace App\cli;

use App\Cli\MenuChoice;
use App\Infrastructure\Translator;

class Menu {

    public array $choices = [];

    public function __construct(private Translator $t) {
        $this->choices = []; 
    }

    function addChoice(MenuChoice $action, string $label, int $id, bool $needPause=true):self {
        if (isset($this->choices[$id])) {
            throw new \LogicException("Duplicate menu key:".$id);
        }

        $this->choices[$id]=["label"=>$label, "action"=>$action, "needPause"=>$needPause, "visible"=>true];

        return $this;
    }

    function getChoice(string $input): array {
        $userChoice = $this->choices[trim($input)] ?? ["action"=>null, "needPause"=>false];
        if (is_string($userChoice["action"])===true) {
            $userChoice["action"] = mb_strtolower($userChoice["action"]);
        }

        return $userChoice;
    }

    function getLines ():array {
        $lines = [];

        foreach ($this->choices as $id=>$item) {
            if ($item["visible"]===false) {continue;}
            $lines[] = " ".$id.". ".$this->t->t($item["label"]);
        }

        return $lines;
    }

    public function addHiddenChoice(string $label, string $action, bool $needPause=true):self {
        if (isset($this->choices[$action])) {
            throw new \LogicException("Duplicate menu key:".$action);
        }

        $this->choices[$action]=["label"=>$label, "action"=>$action, "needPause"=>$needPause, "visible"=>false];
        return $this;
    } 



}




?>