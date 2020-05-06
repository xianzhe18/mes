<?php
/**
 * Created by PhpStorm.
 * User: WangXianzhe
 * Date: 8/22/2016
 * Time: 9:44 PM
 */
if (!isset($_SESSION)) {
    session_start();
}

define('ROLE_USER', '1');
define('ROLE_ADMIN', '5');

$db_host = "localhost";
$db_database = "kkliw201_mc-app";
$db_username = "root";
$db_password = "";
