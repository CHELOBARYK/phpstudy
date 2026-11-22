<?php

namespace App\cli;

use App\Cli\MenuChoice;
use App\Infrastructure\Translator;

class Menu {

    private array $choices = [];

    public function __construct(private Translator $t) {
        $this->choices = []; 
    }

    function addChoice(MenuChoice $action, string $label, int $id, bool $needPause=true, bool $visible = true):self {
        if (isset($this->choices[$id])) {
            throw new \LogicException("Duplicate menu key:".$id);
        }

        $this->choices[$id]=["label"=>$label, "action"=>$action, "needPause"=>$needPause, "visible"=>$visible];

        return $this;
    }

    public function getAllChoices():array {
        return $this->choices;
    }

    public function changeVisibility(int $id, bool $value):self {
        $visibility = $value;

        $this->choices[$id]["visible"] = $visibility;

        return $this;
    }

    function parseInputinChoice(string $input): array {
        $userChoice = $this->choices[trim($input)] ?? ["action"=>null, "needPause"=>false];

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

    public function deleteChoice(int $id): ?self {
        if (!isset($this->choices[$id])) {return null;}

        unset($this->choices[$id]);
        return $this;
    }


}




?>