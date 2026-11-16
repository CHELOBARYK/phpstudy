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

        $this->choices[$id]=["label"=>$label, "action"=>$action, "needPause"=>$needPause];

        return $this;
    }

    function getChoice(string $user):?array {
        $action = $this->choices[trim($user)] ?? ["action"=>null, "needPause"=>false];

        return $action;
    }

    function render () {
        
        foreach ($this->choices as $id=>$item) {
            echo $id.". ".$this->t->t($item["label"]).PHP_EOL;
        }
    }



}




?>