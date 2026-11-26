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
        while(true){if($this->ui->askYesNo($this->lang->t("user.reg_auth"))) { // у вас есть аккаунт?
            $user = $this->authentification(); // да
        } else {
            $user = $this->registration(); // нет
        }

        if($user!==null) {return $user;}
        continue;
        }
    }

    public function registration(): null|User {
        while (true) {
            $username = $this->ui->collectUsername();
            if($username==="EXIT"){return null;}
            $user = $this->userStorage->loadUser($username);
            if ($user !== null) {
                $this->printer->printText($this->lang->t("user.username_exist"));
                continue;
            }
            return $this->ui->collectUserData();
        }
    }
    public function authentification(): null|User {
        while(true) {
            $username = $this->ui->collectUsername();
            if($username==="EXIT"){return null;}
            
            $user = $this->userStorage->loadUser($username);
            if ($user===null) {
                $this->printer->printText($this->lang->t("auth.error.reg"));
                return $this->start();
            }

            $this->printer->printText($this->lang->t("auth.welcome_back"));
            $password = $this->ui->collectPassword();
            $realPassword = $user->getPassword();
            if (password_verify($password, $realPassword)) {return $user;}
            
            $this->printer->printText($this->lang->t("auth.invalid_password"));
        }
    }

}