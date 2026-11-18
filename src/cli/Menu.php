<?php

namespace App\cli;

use App\Cli\MenuChoice;
use App\Infrastructure\Translator;

class Menu {

    public array $choices = [];

    public function __construct(private Translator $t) {
        $this->choices = []; 
    }

    function addChoice(MenuChoice $action, string $label, $id, bool $needPause=true, bool $visible = true):self {
        if (isset($this->choices[$id])) {
            throw new \LogicException("Duplicate menu key:".$id);
        }

        $this->choices[$id]=["label"=>$label, "action"=>$action, "needPause"=>$needPause, "visible" => $visible];

        return $this;
    }

    function getChoice(string $user):?array {
        $action = $this->choices[trim($user)] ?? ["action"=>null, "needPause"=>false];

        return $action;
    }

    function getLines ():array {
        $lines = [];

        foreach ($this->choices as $id=>$item) {
            if ($item["visible"]===false) {continue;}
            $lines[] = " ".$id.". ".$this->t->t($item["label"]);
        }

        return $lines;
    }



}




?>