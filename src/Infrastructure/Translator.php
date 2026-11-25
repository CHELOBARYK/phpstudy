<?php
namespace App\Infrastructure;

/*
    потом парсим в [1=>[name=>Russian, locale=>ru_ru], 2=>[...]]
    и потом отправляем в Menu аля MenuChoice::Language, _meta.name, $id и потом чекаем айди 
*/

class Translator {

    public string $langFilepath = __DIR__."/../../lang";
    public string $file;
    public array $translation;

    public function __construct(
        public string $locale,
    )
    {
        $this->translation = $this->load();

    }

    function setLanguage(string $locale): void{
        $this->locale = $locale;
        $this->translation = $this->load();
    }

    public function load (): array {
        $file = $this->langFilepath."/".mb_strtolower($this->locale).".json";
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

        return $text;

    }

    public function translateArr(array $messages):array {
        $translatedArr=[];
        foreach ($messages as $msg) {
            $params = $msg["params"] ?? [];
            if ($msg===null||!isset($msg["key"])) {continue;}

            $translatedArr[] = $this->t($msg["key"],$params);
        }
    
        return $translatedArr;
    }

    public function loadLanguages(): array {
        $languagesFiles = [];
        $languages = [];

        // [ru_ru.json etc]

        $languagesFiles = array_diff(scandir($this->langFilepath."/"), [".",".."]);

        foreach($languagesFiles as $language) {
            $file = $this->langFilepath."/".$language;
            $data = json_decode(file_get_contents($file),1) ?? ["_meta.name"=>null, "_meta.locale"=>null];

            $languages[] = ["name"=>$data["_meta.name"], "locale"=>$data["_meta.locale"]];
        }
        return $languages;
    }
}
