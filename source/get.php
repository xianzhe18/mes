<?php
session_start();

date_default_timezone_set('Asia/Tokyo');

if (isset($_POST['action'])) {
    include_once('include/checklogin.php');
    include_once('include/config.php');

    $connection = new mysqli($db_host, $db_username, $db_password, $db_database);
    $datetime = date('Y-m-d H:i:s', time());
    $id = $_SESSION['user']['id'];

    switch ($_POST['action']){
        case 'get-msg':
            $pid = $_POST['partner-id'];
            $offset = $_POST['offset'];

            if (!checkAdmin($connection, [$id, $pid])) {
                if (!checkPermission($connection, $id, $pid)) {
                    echo json_encode(array(
                        'status' => 'error',
                        'code' => 'no_permission',
                        'timestamps' => $datetime
                    ));
                    break;
                }
            }

            $query = "UPDATE `message` SET `read_date` = '$datetime' WHERE `from` = '$pid' AND `to` = '$id' AND `read_date` IS NULL;";
            $connection->query($query);

            $query = "SELECT * FROM `message` LEFT OUTER JOIN `files` ON `message`.`id` = `files`.`message_id` WHERE (`from` = '$id' AND `to` = '$pid') OR (`from` = '$pid' AND `to` = '$id') ORDER BY `send_date` DESC LIMIT $offset, 50;";

            $res = $connection->query($query);
            $output = [];
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    $output[] = $row;
                }
            }

            echo json_encode(array(
                'status' => 'success',
                'offset' => $offset + sizeof($output),
                'hasOlder' => sizeof($output) == 50,
                'data' => $output
            ));
            break;
        case 'send-fish':
            $pid = $_POST['partner-id'];

            if (!checkAdmin($connection, [$id, $pid])) {
                if (!checkPermission($connection, $id, $pid)) {
                    echo json_encode(array(
                        'status' => 'error',
                        'code' => 'no_permission'
                    ));
                    break;
                }
            }

            $query = "UPDATE `message` SET `read_date` = '$datetime' WHERE `from` = '$pid' AND `to` = '$id' AND `read_date` IS NULL;";
            $connection->query($query);

            $type = $_POST['type'];
            $content = $_POST['content'];

            if ($type == 'text') {
                $query = "INSERT INTO `message` (`from`, `to`, `type`, `content`, `send_date`) VALUES ('$id', '$pid', 'text', '$content', '$datetime')";
                $res = $connection->query($query);

                if ($res) {
                    echo json_encode(array(
                        'status' => 'success',
                        'timestamps' => $datetime
                    ));
                } else {
                    echo json_encode(array(
                        'status' => 'error',
                        'code' => 'db_error'
                    ));
                }
            } else if ($type == 'file') {

            }
            break;
        case 'check-new':
            $lastFish = $_POST['last'];
            $query = "SELECT * FROM `message` LEFT OUTER JOIN `files` ON `message`.`id` = `files`.`message_id` WHERE `read_date` IS NULL AND `to` = '$id' AND `id` > '$lastFish';";
            $res = $connection->query($query);

            $fishes = [];
            while($row = $res->fetch_assoc()) {
                $fishes[] = $row;
            }

            $newTime = date('Y-m-d H:i:s', strtotime('-30 seconds'));
            $query = "SELECT `id` FROM `users` WHERE `status` > '$newTime';";
            $res = $connection->query($query);

            $users = [];
            while($row = $res->fetch_assoc()) {
                $users[] = $row;
            }

            $query = "UPDATE `users` SET `status` = '$datetime' WHERE `id` = '" . $_SESSION['user']['id'] .  "';";
            $res = $connection->query($query);

            if ($res) {
                echo json_encode(array(
                    'status' => 'success',
                    'fishes' => $fishes,
                    'users' => $users
                ));
            } else {
                echo json_encode(array(
                    'status' => 'error',
                    'code' => 'db_error'
                ));
            }
            break;
        case 'clear-store':
            $partnerId = $_POST['partner-id'];
            $query = "SELECT * FROM `message` WHERE (`from` = '$id' AND `to` = '$partnerId') OR (`from` = '$partnerId' AND `to` = '$id');";
            $res = $connection->query($query);
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    if ($row['type'] == 'file') {
                        $query1 = "SELECT * FROM `files` WHERE `message_id` = '" . $row['id'] . "';";
                        $res1 = $connection->query($query1);
                        if ($res1->num_rows > 0) {
                            while ($row1 = $res1->fetch_assoc()) {
                                unlink('upload_files/' . $row1['modified_name']);
                                $query2 = "DELETE FROM `files` WHERE `file_id` = '" . $row1['file_id'] . "';";
                                $connection->query($query2);
                            }
                        }
                    }
                    $query3 = "DELETE FROM `message` WHERE `id` = '" . $row['id'] . "';";
                    $connection->query($query3);
                }
            }

//            $query = "DELETE FROM `message` WHERE (`from` = '$id' AND `to` = '$partnerId') OR (`from` = '$partnerId' AND `to` = '$id');";
//            $res = $connection->query($query);

            if ($res) {
                echo 'success';
            } else {
                echo 'fail';
            }
            break;
        case 'upload-file':
            if(!empty($_FILES)) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $file = $_FILES['file'];
                    $pid = $_POST['partner-id'];

                    $sourceFile = $_FILES['file']['tmp_name'];
                    $targetFile = $_SESSION['user']['username'] . date("YmdHis");
                    if (move_uploaded_file($sourceFile, "upload_files/" . $targetFile)) {
                        $query = "INSERT INTO message (`from`, `to`, `type`, `content`, `send_date`) VALUES ('$id', '$pid', 'file', '', '".date("Y-m-d H:i:s")."')";
                        $connection->query($query);
                        $last_id = $connection->insert_id;
                        $query1 = "INSERT INTO files (`message_id`, `original_name`, `modified_name`) VALUES ('".$last_id."','".$_FILES['file']['name']."','".$targetFile."')";
                        $connection->query($query1);
                        echo $targetFile;
                    }
                } else {
                    echo 'fail';
                }
            } else {
                echo 'fail';
            }
            break;
    }

    $connection->close();
}

function checkAdmin($connection, $array) {
    $query = 'SELECT * FROM `users`';
    foreach ($array as $idx => $item) {
        if ($idx == 0) {
            $query .= " WHERE `id` = '$item'";
        } else {
            $query .= " OR `id` = '$item'";
        }
    }
    $query .= ';';
    $res = $connection->query($query);

    if ($res->num_rows == 0) {
        return false;
    } else {
        while ($row = $res->fetch_assoc()) {
            if ($row['role'] == ROLE_ADMIN) {
                return true;
            }
        }
    }
}

function checkPermission($connection, $id, $pid) {
    $query = "SELECT * FROM `user_match` WHERE (`id1` = '$id' AND `id2` = '$pid') OR (`id1` = '$pid' AND `id2` = '$id');";
    $res = $connection->query($query);
    return $res->num_rows > 0;
}