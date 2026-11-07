<?php
class Cat 
{

    public function __construct(
        private readonly string $color,
        private readonly string $name,
        private readonly int $age
    ){}

    public function loaf(): void {
        echo "*loafs*\n";
    }

    public function meow(?string $food = null): void {
        if ($food) {
            echo "meow\n";
        } else {
            echo "*leaves*\n";
        }
    }

    public function random(): void {
        $behaviours = [
            "meow\n",
            "*aura farms*\n",
            "mystic\n",
            "silly\n",
            "smarty\n",
            "*void*\n",
            "*leaves you for food*\n",
            "*MEOW*\n",
            "0w0\n",
        ];
        echo $behaviours[array_rand($behaviours)];
    }

    function vetInfo():void {
        echo <<<END
        Name: $this->name
        Color: $this->color
        Age: $this->age
        END;
    }

} 

$mycat = new Cat('orange', 'Alex', 12);
$mycat->loaf();
$mycat->random();
$mycat->meow('fish');
$mycat->vetinfo();

?>