<?php 
namespace App\cli;

use App\Cli\ConsolePrinter;
use App\Domain\Cat;
use App\Domain\CatFabric;
use App\Domain\UserFactory;
use App\Infrastructure\Translator;

class UserInteraction {

    public function __construct(
        private ConsolePrinter $printer,
        private Translator $lang,
        private CatFabric $catFabric,
        private UserFactory $userFactory,
        private Input $input
    ) {}
    
    public function askYesNo (string $question) {
        $yes = ["yes","y",1,"да","д"];
        $no = ["no","n",0,"нет","н"];

        
        while (true) {
            $input = strtolower($this->input->inputText($question));

            if (in_array($input, $yes)) {return true;}
            if (in_array($input, $no)) {return false;}
            
            $this->printer->printText($this->lang->t("menu.choice.invalid"));
        }    
        
    }

    public function collectCatData () {
        $result = catFabric::EMPTY_INPUT;
        $data = ["name"=>" ","age"=>1,"color"=>" ",];

        $errors = [
            CatFabric::EMPTY_INPUT => ["msg"=>"create.empty_input", "fields"=>["name", "color", "age"]],
            CatFabric::INVALID_AGE => ["msg"=>"create.valid_age", "fields"=>["age"]],
            CatFabric::INVALID_COLOR => ["msg"=>"create.valid_color", "fields"=>["color"]],
            CatFabric::INVALID_NAME => ["msg"=>"create.valid_name", "fields"=>["name"]],
        ];
        
        while ($result !== 0) {
            $this->printer->clearScreen();

            $error = $errors[$result] ?? $errors[CatFabric::EMPTY_INPUT];

            $this->printer->printText($this->lang->t($error["msg"]));
            foreach ($error["fields"] as $field) {
                $data[$field] = $this->input->inputText($this->lang->t("create.ask.$field"));
            }

            $result = $this->catFabric -> validate($data["name"],$data["age"],$data["color"]);
        }

        return catFabric::createCat($data["name"],$data["age"],$data["color"]);
    }

    public function renameCat():string {
        $newName = "";
        $result = false;
        
        while (!$result) {
            $this->printer->printText($this->lang->t("create.new_name"));
            $newName = $this->input->inputText($this->lang->t("create.ask.name"));
            $result = $this->catFabric->validateName($newName);
        }

        return $newName;
    }

    public function collectUserData() {
        $result = UserFactory::EMPTY_INPUT;
        $data = ["name"=>" ", "password"=>" ",];

        $errors = [
            UserFactory::EMPTY_INPUT => ["msg"=>"create.empty_input", "fields"=>["name", "password"]],
            UserFactory::INVALID_PASSWORD => ["msg"=>"create.valid_password", "fields"=>["password"]],
            UserFactory::INVALID_USERNAME => ["msg"=>"create.valid_username", "fields"=>["username"]],
        ];
        
        while ($result !== 0) {
            $this->printer->clearScreen();

            $error = $errors[$result] ?? $errors[UserFactory::EMPTY_INPUT];

            $this->printer->printText($this->lang->t($error["msg"]));
            foreach ($error["fields"] as $field) {
                $data[$field] = $this->input->inputText($this->lang->t("create.ask.$field"));
            }

            $result = $this->userFactory -> validate($data["name"], $data["password"]);
        }

        return UserFactory::createUser($data["name"],$data["password"]);
    }

    public function collectUsername():string {
        $username = "";
        $result = false;
        while (!$result) {
            $username = $this->input->inputText($this->lang->t("reg.ask.username"));
            $result = $this->userFactory->validateUsername($username);
        }
        return $username;
    }
    public function collectPassword():string {
        $password = "";
        $result = false;
        while (!$result) {
            $password = $this->input->inputText($this->lang->t("reg.ask.password"));
            $result = $this->userFactory->validatePassword($password);
        }
        return $password;
    }
}