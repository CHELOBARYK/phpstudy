<?php
namespace Tests;

use function PHPUnit\Framework\assertIsArray;
use PHPUnit\Framework\TestCase;

class TranslationTest extends TestCase{
    public function test_translation_keys() {
        $langFilepath = __DIR__."/../lang/";

        $rujson = $langFilepath."ru_ru.json";
        $enjson = $langFilepath."en_us.json";

        $eng = json_decode(file_get_contents($enjson),1);
        $rus = json_decode(file_get_contents($rujson),1);
        
        $this->assertIsArray($eng, $langFilepath."en_us.json"." not a valid JSON");
        $this->assertIsArray($rus, $langFilepath."ru_ru.json"." not a valid JSON");

        $missingRU = array_diff(array_keys($eng), array_keys($rus));
        $missingEN = array_diff(array_keys($rus), array_keys($eng));

        $this->assertEmpty($missingEN, "Missing keys in EN_US".implode(", ", $missingEN));
        $this->assertEmpty($missingEN, "Missing keys in RU_RU".implode(", ", $missingRU));
        
    }
}