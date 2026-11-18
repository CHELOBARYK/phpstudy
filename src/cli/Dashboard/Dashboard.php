<?php
namespace App\Cli\Dashboard;

use App\Cli\ConsolePrinter;
use App\Domain\Cat;
use App\cli\Menu;


class Dashboard {
    
    private const LEFT_WIDTH = 35;
    private const RIGHT_WIDTH = 20;

    public function __construct(
        private ConsolePrinter $printer,
        private Cat $cat,
        private Menu $menu
    ){}

    /*
    
    - takes cat img
    - takes menu as strings array
    - takes cats stats
    
    - renders it all
    */
    public function render(Menu $menu, Cat $cat) {
    
        $rightpanel= CatArt::get($cat);
        $rightpanel[] = "";
        $rightpanel[] = "  ".$this->getBar($cat->getHappiness())." HAP";
        $rightpanel[] = "  ".$this->getBar($cat->getSaturation())." SAT";

        $leftPanel= $menu->getLines(); //gives us arr with strings;

        // top
        $top = "┌" . str_repeat("─", self::LEFT_WIDTH) . "┬" . str_repeat("─", self::RIGHT_WIDTH) . "┐";
        $this->printer->printText($top);

        // middle left+right
        $maxLines = max(count($leftPanel), count($rightpanel));

        for ($i=0; $i<$maxLines; $i++) {
            $left  = $leftPanel[$i] ?? " ";
            $right = $rightpanel[$i] ?? " ";
            
            $line = "│" . $this->pad($left, self::LEFT_WIDTH) . "│" . $this->pad($right, self::RIGHT_WIDTH) . "│";
            $this->printer->printText($line);
        }

        //bottom

        $bottom = "└" . str_repeat("─", self::LEFT_WIDTH) . "┴" . str_repeat("─", self::RIGHT_WIDTH) . "┘";
        $this->printer->printText($bottom);

    }
    
    public function pad(string $text, int $length) {
        $textLength = mb_strlen($text);
        $padding = max(0, $length - $textLength);
        return $text . str_repeat(' ', $padding);
    }

    private function getBar(int $value, int $max = 100, int $length = 10): string
    {
        $filled = round(($value / $max) * $length);
        $empty = $length - $filled;
        return "[" . str_repeat("█", (int)$filled) . str_repeat("░", (int)$empty) . "]";
    }
    

}