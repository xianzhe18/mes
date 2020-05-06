<?php
/**
 * Created by PhpStorm.
 * User: WangXianzhe
 * Date: 8/23/2016
 * Time: 2:38 AM
 */
include_once("include/config.php");

unset($_SESSION['user']);
header("Location: check.php");