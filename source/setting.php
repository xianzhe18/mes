<?php
/**
 * Created by PhpStorm.
 * User: Steel
 * Date: 6/18/2018
 * Time: 3:29 PM
 */
include_once('./include/config.php');

$connection = new mysqli($db_host, $db_username, $db_password, $db_database);

if (isset($_POST['user'])){
    $id = $_POST['user'];

    $role = isset($_POST['isEnabled'])?isset($_POST['isAdmin'])?ROLE_ADMIN:ROLE_USER:'0';

    if (isset($_POST['allowed'])) {
        $allowed = explode(",", $_POST['allowed']);
    } else {
        $allowed = [];
    }

    $query = "UPDATE `users` SET `role` = '$role' WHERE (`id` = '$id') ";

    $connection->query($query);

    $query = "DELETE FROM `user_match` WHERE `id1` = '$id' ;";

    $connection->query($query);

    foreach ($allowed as $one) {
        if (trim($one) != "") {
            $query = "INSERT INTO user_match (`id1`, `id2`) VALUES ('$id', '$one') ;";
            $connection->query($query);
        }
    }
} else if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != ROLE_ADMIN) {
    $connection->close();
    header("Location: check.php");
    exit();
}

if (isset($_GET['user'])){
    $id = $_GET['user'];
}

$query = "SELECT * FROM `users` WHERE `id` = '$id';";
$result = $connection->query($query);

if ($result) {
    $user = $result->fetch_assoc();
    $query = "SELECT * FROM `users` WHERE `id` <> '$id' AND `role` <> '" . ROLE_ADMIN . "' ;";
    $others = [];
    $result = $connection->query($query);
    while($row = $result->fetch_assoc()){
        $others[] = $row;
    }

    $allowed_id = [];
    $query = "SELECT * FROM `user_match` WHERE `id1` = '$id'  ;";
    $result = $connection->query($query);
    if ($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
            $allowed_id[] = $row['id2'];
        }
    }

    $query = "SELECT * FROM `user_match` WHERE `id2` = '$id' ;";
    $result = $connection->query($query);
    if ($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
            $allowed_id[] = $row['id1'];
        }
    }
}

$connection->close();

?>

<html>
<head>
    <title>User list</title>
    <meta charset='UTF-8' />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" id="css-main" href="assets/css/oneui.css">
    <link rel="stylesheet" href="assets/css/index.css?v=1">
    <style>
        #clear-allowed{
            cursor: pointer;
        }
    </style>
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <div class="block">
                <div class="block-header">
                    <div class="block-options">
                        <a class="" href="index.php">Chat</a>
                        <a class="" href="users.php">Users</a>
                        <a class="" href="out.php">Get OUT</a>
                    </div>
                    <h3 class="block-title">User</h3>
                </div>
                <div class="block-content">
                    <form class="form-horizontal" action="setting.php" method="post">
                        <div class="form-group">
                            <label class="col-xs-12" for="username">ID</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" id="username" name="username" value="<?php echo $user['username']; ?>" disabled>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="css-input switch switch-info">
                                <input type="checkbox" name="isAdmin" <?php if($user['role'] == ROLE_ADMIN) echo "checked"; ?>><span></span> Is Admin?
                            </label>
                            <label class="css-input switch switch-info">
                                <input type="checkbox" name="isEnabled" <?php if($user['role'] != '0') echo "checked"; ?>><span></span> Is Enabled?
                            </label>
                        </div>
                        <div class="form-group">
                            <label class="col-xs-9" for="allowed-select">Multiple Select</label>
                            <div class="col-xs-3" id="clear-allowed">clear</div>
                            <div class="col-sm-12">
                                <select class="form-control" id="allowed-select" size="5" multiple>
                                    <?php
                                    foreach ($others as $one) {
                                    ?>
                                        <option value="<?php echo $one['id']; ?>" <?php if (in_array($one['id'], $allowed_id)) echo "selected"; ?>><?php echo $one['username']; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <input type="hidden" id="allowed" name="allowed" value="<?php echo implode(",", $allowed_id); ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="hidden" name="user" value="<?php echo $id; ?>"/>
                                <button class="btn btn-sm btn-primary" type="submit">Confirm</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        $("#allowed-select").on("change", function(){
            $("#allowed").val($(this).val().join(","));
        });

        $("#clear-allowed").on("click", function(){
            $("#allowed-select").val("");
            $("#allowed").val("");
        });
    });
</script>

</body>
</html>