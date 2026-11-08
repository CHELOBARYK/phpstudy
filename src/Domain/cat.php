<?php
namespace App\Domain;

class Cat 
{
    public int $age;

    public function __construct(
        private readonly string $color,
        private readonly string $name,
        int $age
    ){
        $this->age = $age;
    }

    public function printer(string $text) {
        echo $text, PHP_EOL;
    }

    public function loaf(): void {
        $this->printer("*loafs*");
    }

    public function meow(?string $food = null): void {
        if ($food) {
            $this->printer("*leaves happy*");
        } else {
            $this->printer("MEOW"); 
        }
    }

    public function random(): void {
        $behaviours = [
            "meow",
            "*aura farms*",
            "mystic",
            "silly",
            "smarty",
            "*void*",
            "*leaves you for food*",
            "*MEOW*",
            "0w0",
        ];
        $behaivour = $behaviours[array_rand($behaviours)];
        $this->printer($behaivour);
    }

    public function vetInfo():void {
        $info = <<<END
        Name: $this->name
        Color: $this->color
        Age: $this->age
        END;

        $this->printer($info);
    }

    public function yearlyVetLab():void {
        $healthStatus=[
            "healthy",
            "ill",
            "happi",
        ];
        $diagnose=$healthStatus[array_rand($healthStatus)];
        $this->age++;
        $info = <<<END
        Name: $this->name
        Color: $this->color
        Age: $this->age
        Diagnose: $diagnose
        Happy birthday!
        END;
        $this->printer($info);
    }

} 


?>