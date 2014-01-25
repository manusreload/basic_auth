<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Constants
 *
 * @author manus
 */
class Constants {

    /**
     * 
      CREATE TABLE IF NOT EXISTS `users` (
      `email` varchar(64) COLLATE utf8_bin NOT NULL,
      `hash` varchar(32) COLLATE utf8_bin NOT NULL,
      `token` varchar(64) COLLATE utf8_bin NOT NULL,
      `name` varchar(16) COLLATE utf8_bin NOT NULL,
      `surname` varchar(32) COLLATE utf8_bin NOT NULL,
      `verificaton_code` varchar(64) COLLATE utf8_bin NOT NULL,
      `verificated` int(11) NOT NULL
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
      ALTER TABLE  `users` ADD UNIQUE (
      `email`
      );
     * 
     */
    const TABLE_USERS = "users";
    const USERS_COLUM_EMAIL = "email";
    const USERS_COLUM_PASS = "hash";
    const USERS_COLUM_TOKEN = "token";
    const USERS_COLUM_NAME = "name";
    const USERS_COLUM_SURNAME = "surname";
    const USERS_COLUM_VERFICATIONCODE = "verificaton_code";
    const USERS_COLUM_VERIFICATED = "verificated";

}

?>
