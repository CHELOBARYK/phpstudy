<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Domain\Cat;
use App\domain\states\DeadState;

class UnboxingCatTest extends TestCase{
    public function test_unboxing_probability(){
        $succesedAttempts = 0;
        $failedAttempts = 0;

        for ($i=0;$i<50; $i++) {
            $cat = new Cat("alex","orange", 12);
            $cat->box();
            $cat->unbox();

            if($cat->isDead()) {$failedAttempts++;}
            else {$succesedAttempts++;}

            if ($succesedAttempts and $failedAttempts) {break;}
        }

        $this->assertGreaterThan(0, $succesedAttempts, "Кот никогда не умирал (всегда 1)");
        $this->assertGreaterThan(0, $failedAttempts, "Кот никогда не выживал (всегда 0)");
    }

    public function test_unboxing_only_possible_when_in_box() {
        $cat = new Cat("alex","orange", 12);
        $result = $cat->unbox();

        $this->assertEmpty($result, "Сработало хотя кот не в коробке");
    }
}