<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mysqlConnector
 *
 * @author manus
 */
class MysqlConnector {

    var $mysql_conn;

    function __construct() {
        
    }

    //Tested :D
    function connect() {
        $this->addr = Config::DBCONNECTION_SERVER;
        $this->user = Config::DBCONNECTION_USER;
        $this->pass = Config::DBCONNECTION_PASS;
        $this->table = Config::DBCONNECTION_TABLE;

        $result = $this->mysql_conn = mysql_connect($this->addr, $this->user, $this->pass);
        if ($result) {
            return mysql_select_db($this->table, $this->mysql_conn);
        }
        return false;
    }

    function query($query = "") {
        $res = mysql_query($query, $this->mysql_conn);
        if (!$res) {
            throw new Exception(mysql_error($this->mysql_conn));
        }
        return $res;
    }

    function close() {
        mysql_close($this->mysql_conn);
        return true;
    }

}

?>
