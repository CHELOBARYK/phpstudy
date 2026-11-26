<?php
namespace App\Domain;

use App\Domain\Achievements\Achievement;

/*
    - User class. Describes how User works and what it does.
    - treat like Cat class
*/

final class UserFactory {
    //const
    public const EMPTY_INPUT = 1;
    public const INVALID_USERNAME = 2;
    public const INVALID_PASSWORD = 3;
    // all data

    public function validate(string $username, string $password) {
        
        if (!self::validateUsername($username)){return self::INVALID_USERNAME;}
        if (!self::validatePassword($password)){return self::INVALID_PASSWORD;}

        return 0;
    }

    public function validateUsername(string $username) {return preg_match("/^[a-zA-Z0-9_]{3,20}$/",$username);}
    public function validatePassword(string $password) {return preg_match("/\S{4,}/",$password);}
    // ^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$ change to it later

    public static function createUser(string $username, string $password):User {
        $password = password_hash($password, PASSWORD_BCRYPT);
        return new User($username, $password);
    }

    public static function createFromArray(array $data): null|User {
        $factory = new self();
        if (!$factory->validateUsername($data["username"])) { return null;}
        return User::fromArray($data);
    }
}