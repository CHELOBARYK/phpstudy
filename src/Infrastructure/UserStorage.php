<?php
namespace App\Infrastructure;

use App\Domain\Cat;
use App\Domain\UserFactory;
use App\Domain\User;

/*
    отвечать + выполненные
*/


class UserStorage {

    private string $dirPath;
    private const DATA_FORMAT = "Y-m-d h:i:s";
    
    function __construct(string $dirPath){
        $this ->dirPath = $dirPath;
        if (!is_dir($this->dirPath)) {
            mkdir($this->dirPath, 0777);
        }
    }

    public function clearName(string $username):string {
        return preg_replace("/[^a-zA-Z0-9_\-]/","", mb_strtolower($username));
    }

    public function getUserFile(string $username):string {
        $clearUsername = $this->clearName($username);
        return $this->dirPath."/".$clearUsername.".json";
    }
        
    function isRegistred(string $username): bool {
        return file_exists($this->getUserFile($username));
    }

    function loadUser(string $username): null|User {
        if(!$this->isRegistred($username)) {return null;}
        $userFile = $this->getUserFile($username);
        $data = json_decode(file_get_contents($userFile),1) ?? null;
        
        if ($data===null) {return null;}

        $user = UserFactory::createFromArray($data);
        return $user;
    }

    function saveUser(User $user) {
        $data = $user->toArray();
        if($this->isRegistred($data["username"])){;}
        $username = $this->getUserFile($data["username"]);
        file_put_contents(
            $username,
            json_encode($data,JSON_PRETTY_PRINT)
        );
        
    }
}