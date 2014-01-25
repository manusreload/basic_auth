<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * 
 *
 * @author manus
 */
include_once 'MysqlConnector.php';
include_once 'User.php';
include_once 'Constants.php';

class AuthCore {

    var $conn;

    public function __construct($mysql = true, $config = null) {
        if (!isset($config)) {
            $config = __DIR__ . "/config.inc.php";
        }

        require_once $config;
        //echo $config;
        if (!class_exists("Config")) {
            throw "No config defined";
        }
        $this->conn = new MysqlConnector();
        if ($mysql)
            if (!$this->conn->connect())
                die("La db no esta configurada o no se puede conectar!");
    }

    /**
     * Insert new user, and send an verification e-mail.
     * @param type $email
     * @param type $pass
     * @param type $name
     * @return 0 succees, -1 send email error, -2 email exists
     */
    public function registerUser($email, $pass, $name, $surname) {
        $email = mysql_real_escape_string($email);
        $pass = mysql_real_escape_string($pass);
        $name = mysql_real_escape_string($name);
        $surname = mysql_real_escape_string($surname);
        $verfication_code = $this->random_string(64);

        try {
            if ($this->conn->query("INSERT INTO `" . Constants::TABLE_USERS . "` (
            `" . Constants::USERS_COLUM_EMAIL . "`,
            `" . Constants::USERS_COLUM_PASS . "`,
            `" . Constants::USERS_COLUM_NAME . "`,
            `" . Constants::USERS_COLUM_SURNAME . "`,
            `" . Constants::USERS_COLUM_VERFICATIONCODE . "`
                
            ) VALUES (
            '" . $email . "',
            '" . md5($pass) . "',
            '" . $name . "',
            '" . $surname . "',
            '" . $verfication_code . "'
            )")) {
                $user = new User($this);
                $user->setData(array(Constants::USERS_COLUM_EMAIL => $email,
                    Constants::USERS_COLUM_PASS => md5($pass),
                    Constants::USERS_COLUM_NAME => $name,
                    Constants::USERS_COLUM_SURNAME => $surname,
                    Constants::USERS_COLUM_VERFICATIONCODE => $verfication_code,
                    Constants::USERS_COLUM_VERIFICATED => 0));
                if ($this->sendValidationMail($user)) {
                    return 0;
                } else {
                    return -1;
                }
            }
        } catch (Exception $e) {
            
        }
        return -2;
    }

    /**
     * 
     * @param User $user
     * @return boolean
     */
    public function sendValidationMail($userC) {
        $user = $userC->getName();
        $code = $userC->getVerificationCode();
        $patterns = array("/%user/", "/%code/");
        $replacements = array($user, $code);
        $message = preg_replace($patterns, $replacements, Config::VERIFY_MAIL_MESSAGE);
        $send_res = $this->send_mail($userC->getEmail(), Config::VERIFY_MAIL_TITLE, $message);
        return($send_res == "1");
    }

    public function validateToken($email, $token) {
        if ($token == "") {
            $token = $this->random_string(64, true, true);
            if (!$this->setToken($email, $token)) {
                $token = "";
            }
        }
        return $token;
    }

    private function setToken($email, $token) {
        return $this->conn->query("UPDATE `" . Constants::TABLE_USERS . "` SET `" . Constants::USERS_COLUM_TOKEN . "` = '" . $token . "' WHERE `" . Constants::USERS_COLUM_EMAIL . "` = '" . $email . "'");
    }

    public function getTokenByPass($email, $pass, $hased = false) {
        $email = mysql_real_escape_string($email);
        $pass = mysql_real_escape_string($pass);
        if (!$hased)
            $pass = md5($pass);
        $res = $this->conn->query("SELECT * FROM `" . Constants::TABLE_USERS . "` WHERE 
            `" . Constants::USERS_COLUM_EMAIL . "` = '" . $email . "' AND 
            `" . Constants::USERS_COLUM_PASS . "` = '" . $pass . "'");
        if ($res) {
            if ($row = mysql_fetch_array($res)) {

                return $this->validateToken($email, $row[Constants::USERS_COLUM_TOKEN]);
            }
        }
        return false;
    }

    public function loginUser($email, $token) {
        $email = mysql_real_escape_string($email);
        $token = mysql_real_escape_string($token);
        $res = $this->conn->query("SELECT * FROM `" . Constants::TABLE_USERS . "` WHERE 
            `" . Constants::USERS_COLUM_EMAIL . "` = '" . $email . "' AND 
            `" . Constants::USERS_COLUM_TOKEN . "` = '" . $token . "'");
        if ($res) {

            if ($row = mysql_fetch_array($res)) {
                return $row;
            }
        }
        return false;
    }

    /**
     * 
     * @param User $user
     */
    function verifyUser($user) {
        if ($user->isLogin()) {
            return $this->conn->query("UPDATE `" . Constants::TABLE_USERS . "` SET `" . Constants::USERS_COLUM_VERIFICATED . "` = 1 WHERE `" . Constants::USERS_COLUM_EMAIL . "` = '" . $user->getEmail() . "'");
        }
        return false;
    }

    function random_string($length = 10, $uc = TRUE, $n = TRUE, $sc = FALSE, $lc = TRUE) {
        $source = '';
        if ($lc == 1)
            $source .= 'abcdefghijklmnopqrstuvwxyz';
        if ($uc == 1)
            $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        if ($n == 1)
            $source .= '1234567890';
        if ($sc == 1)
            $source .= '|@#~$%()=^*+[]{}-_';
        if ($length > 0) {
            $rstr = "";
            $source = str_split($source, 1);
            for ($i = 1; $i <= $length; $i++) {
                mt_srand((double) microtime() * 1000000);
                $num = mt_rand(1, count($source));
                $rstr .= $source[$num - 1];
            }
        }
        return $rstr;
    }

    function send_mail($email, $subject, $message) {
        require("phpmailer-gmail/class.phpmailer.php");
        require("phpmailer-gmail/class.smtp.php");

        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465;
        $mail->Username = Config::EMAIL_NAME;
        $mail->Password = Config::EMAIL_PASS;

        $mail->From = Config::EMAIL_FROM;
        $mail->FromName = Config::EMAIL_FROMNAME;
        $mail->Subject = $subject;
        $mail->AltBody = $message;
        $mail->MsgHTML(nl2br($message));
        $mail->AddAddress($email);
        $mail->IsHTML(true);

        return $mail->Send();
    }

}

?>
