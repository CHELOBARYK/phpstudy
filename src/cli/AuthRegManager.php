<?php
namespace App\cli;

use App\cli\UserInteraction;
use App\Infrastructure\UserStorage;
use App\Domain\User;
use App\Infrastructure\Translator;
use App\Cli\ConsolePrinter;

final class AuthRegManager {
    public function __construct(
        private UserStorage $userStorage,
        private UserInteraction $ui,
        private ConsolePrinter $printer,
        private Translator $lang
    ){}

    public function start():User {
        if($this->ui->askYesNo($this->lang->t("user.reg_auth"))) { // у вас есть аккаунт?
            return $this->authentification(); // да
        } else {
            return $this->registration(); // нет
        }
    }

    public function registration():User {
        return $this->ui->collectUserData();
    }
    public function authentification(): string|User {
        while(true) {
            $username = $this->ui->collectUsername();
            $password = $this->ui->collectPassword();

            $user = $this->userStorage->loadUser($username);
            if ($user===null) {
                echo PHP_EOL;
                echo PHP_EOL;
                $this->printer->printText($this->lang->t("auth.error.reg"));
                return $this->start();
            }

            $this->printer->printText($this->lang->t("auth.welcome_back"));
            $realPassword = $user->getPassword();
            if (password_verify($password, $realPassword)) {
                return $user;
            } else {
                $this->printer->printText($this->lang->t("auth.invalid_passport"));
            }
        }
    }

}