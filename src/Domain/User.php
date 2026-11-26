<?php
namespace App\Domain;

final class User {
    private string $id;
    private array $achievements = [];

    public function __construct(
        private string $username,
        private string $password,
        private string $lang = "en_us"
    ) {
        $this->id = uniqid();
        $this->username;
        $this->password;
        $this->lang;
        $this->achievements = [];
    }
    //getters
    public function getPassword():string {return $this->password;}
    public function getName(): string {return $this->username;}
    public function getLang(): string {return $this->lang;}
    public function getAchievements(): array {return $this->achievements;}
    public function getId():string {return $this->id;}

    //setters
    public function setLang(string $lang) {$this->lang = $lang;}

    /*  */
    public function hasAchievement(string $id):bool {
        return in_array($id, $this->achievements);
    }

    public function unlockAchievement(string $id) {
        if($this->hasAchievement($id)===false) {$this->achievements[]=$id;}
    }

    public static function fromArray(array $data):self {
        $user = new self($data["username"], $data["password"]);

        $user->id = $data["id"];
        $user->lang = $data["lang"] ?? "en_us";
        $user->achievements = $data["achievements"] ?? [];

        return $user;
    }

    public function toArray():array {
        $data = [
            "id"=>$this-> id,
            "username"=>$this-> username,
            "lang"=>$this-> lang,
            "password"=>$this-> password,
            "achievements"=>$this->achievements,
        ];

        return $data;
    }

}