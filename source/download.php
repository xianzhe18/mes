<?php
session_start();

date_default_timezone_set('Asia/Tokyo');
include_once('include/checklogin.php');
include_once('include/config.php');

if (isset($_GET['file'])) {
    $connection = new mysqli($db_host, $db_username, $db_password, $db_database);
    $fileName = $_GET['file'];

    $query = "SELECT * FROM `files` WHERE `modified_name` = '$fileName';";
    $res = $connection->query($query);
    if ($res->num_rows > 0) {
        $file = 'upload_files/' . $fileName;
        $row = $res->fetch_assoc();
        $originalName = $row['original_name'];
        $connection->close();

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$originalName.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }
} else {
    exit("Not valid request...");
}