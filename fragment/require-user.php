<?php

/*
 * Use this script to ensure user loged-in
 */


require_once __DIR__ . '/session.php';

if(!isLogin())
{
    
    // Redirect after login.
    $location = "";
    if(!isset($_REQUEST['logout']))
        $location = $_SERVER['REQUEST_URI'];
    header("Location: login.php?goto=" . urlencode( $location) );
    die();
}
function getCore()
{
    if(!isset($GLOBALS['core']))
        $GLOBALS['core'] = new TwitterCore();
    return $GLOBALS['core'];
}
