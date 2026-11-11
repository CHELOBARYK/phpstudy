<?php
namespace App\Infrastructure;

/*

    Takes lang/.json file and returns according string

    -- add load file function that returns an array
    -- add that to play.php


*/ 

class Translator {

    public string $langFilepath = __DIR__."/../../lang/";
    public string $file;
    public array $translation;

    public function __construct(
        public string $locale,
    )
    {

        $this->file = $this->langFilepath.mb_strtolower($this->locale).".json";
        $this->translation = $this->load($this->file);
    }

    public function load (string $file): array {
        if (!is_file($file)) {
            echo "NO_LOCALE_FILE: $this->file";
            return [];
        }

        $translation = json_decode(file_get_contents($file),1) ?? [];
        return $translation;
    }

    public function t (string $key, array $params=[]): string {
        $text = $this->translation[$key] ?? "MISSING_KEY: $key";

        foreach ($params as $k => $v) {
            $text = str_replace('{'.$k.'}',(string) $v,$text);
        }

        return $text.PHP_EOL;

    }

}


?>