<?php

/*
 * Sample config
 */

class Config {

    const DBCONNECTION_SERVER = 'servername.com';
    const DBCONNECTION_USER = 'root';
    const DBCONNECTION_PASS = 'password';
    const DBCONNECTION_TABLE = 'table';
    const EMAIL_VERIFICATION = false;
    const EMAIL_NAME = "email@gmail.com";
    const EMAIL_PASS = "email_pass";
    const EMAIL_FROM = "Pass";
    const EMAIL_FROMNAME = "Registration";

    /**
      use %user for replace for user name, and %code for verification code
     */
    const VERIFY_MAIL_MESSAGE = "Hi %user! Your verification code is: %code";
    const VERIFY_MAIL_TITLE = "Verification Code";

}

?>
