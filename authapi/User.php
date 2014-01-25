<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of User
 *
 * @author manus
 */
include_once 'AuthCore.php';

class User {

    //put your code here
    var $id, $name, $surname, $email, $pass, $token, $code;
    var $core;
    private $login;
    private $verificated;

    public function __construct($core = NULL) {
        $this->login = false;
        if ($core == NULL)
            $this->core = new AuthCore();
        else
            $this->core = $core;
    }

    public function login($email, $token) {
        $res = $this->core->loginUser($email, $token);
        if (is_array($res)) {
            //Load some data for the user
            $this->setData($res);
            //Make the user loged in
            $this->login = true;
            $this->token = $token;
            return true;
        }
    }

    public function setData($arr) {
        $this->id = $arr['id'];
        $this->email = $arr[Constants::USERS_COLUM_EMAIL];
        $this->name = $arr[Constants::USERS_COLUM_NAME];
        $this->surname = $arr['surname'];
        $this->pass = $arr[Constants::USERS_COLUM_PASS];
        $this->code = $arr[Constants::USERS_COLUM_VERFICATIONCODE];
        $this->verificated = $arr[Constants::USERS_COLUM_VERIFICATED] == 0 ? false : true;
    }

    public function isLogin() {
        return $this->login;
    }

    public function isVerificated() {
        return $this->code == "" || $this->verificated;
    }

    public function getId() {
        return $this->id;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getToken() {
        return $this->token;
    }

    public function getVerificationCode() {
        return $this->code;
    }

    public function getName() {
        return $this->name;
    }

    public function getSurname() {
        return $this->surname;
    }

    public function verify($code) {
        if ($code == $this->code) {
            if ($this->core->verifyUser($this)) {
                $this->verificated = true;
                return true;
            }
        }
        return false;
    }

    function setNewPassword($pass) {
        $this->core->changePassword($this->email, $pass);
    }

    function sendVerificationMail() {
        return $this->core->sendValidationMail($this);
    }

}

?>
