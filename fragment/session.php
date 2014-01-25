<?php

/*
 * Use this script to init user functions
 */

session_start();
require_once __DIR__ . '/../authapi/AuthCore.php';

//Chech request login:
if (isset($_GET['action']) && $_GET['action'] == 'do_login') {
    //Test login, and store result in: $GLOBALS['login_result']
    if (isset($_POST['email']) && isset($_POST['pass'])) {
        $core = new AuthCore();
        $token = $core->getTokenByPass($_POST['email'], $_POST['pass']);
        if (!empty($token)) {
            $_SESSION['logeduser'] = $_POST['email'];
            $_SESSION['logedpass'] = $token;
            if(isset($_POST['rememver']))
            {
                setcookie('username',$_POST['email'] );
                setcookie('token', $token );
            }
        } else {
            $GLOBALS['login_errors'] = true;
        }
    }
}
// Logout request. 
if (isset($_GET['logout'])) {
    $GLOBALS['isLogin'] = false;
    unset($_SESSION['logedpass']);
    unset($_SESSION['logeduser']);
    unset($_COOKIE['username']);
    unset($_COOKIE['token']);
    setcookie('username', null, -1, '/');
    setcookie('token', null, -1, '/');
}

if(isset($_COOKIE['username']) && isset($_COOKIE['token']))
{
    $_SESSION['logeduser'] = $_COOKIE['username'];
    $_SESSION['token'] = $_COOKIE['token'];
}

if (isset($_SESSION['logeduser']) && isset($_SESSION['logedpass'])) {
    $user = new User();
    if ($user->login($_SESSION['logeduser'], $_SESSION['logedpass'])) {
        $GLOBALS['logeduser_obj'] = $user;
        $GLOBALS['is_login'] = true;
        $GLOBALS['login_result'] = 1;
    } else {
        $GLOBALS['login_result'] = 0;
        $GLOBALS['is_login'] = false;
        unset($_SESSION['logedpass']);
        unset($_SESSION['logeduser']);
    }
}



function isLogin() {
    return isset($GLOBALS['is_login']) && $GLOBALS['is_login'] === true;
}

function get_user_id() {
    return $GLOBALS['logeduser_obj']->id;
}

function get_user_data() {
    return $GLOBALS['logeduser_obj']->data;
}

/**
 * 
 * @return User
 */
function get_user() {
    return $GLOBALS['logeduser_obj'];
}
