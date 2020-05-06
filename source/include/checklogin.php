<?php
/**
 * Created by PhpStorm.
 * User: WangXianzhe
 * Date: 8/22/2016
 * Time: 9:29 PM
 */
if (!isset($_SESSION['user']))
    header("Location: check.php");

