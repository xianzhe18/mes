<?php
/**
 * Created by PhpStorm.
 * User: Steel
 * Date: 6/18/2018
 * Time: 3:29 PM
 */
include_once('./include/config.php');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != ROLE_ADMIN)
    header("Location: login.php");

$connection = new mysqli($db_host, $db_username, $db_password, $db_database);

$query = "SELECT * FROM users WHERE id <> ".$_SESSION['user']['id'];
$result = $connection->query($query);
$users = [];

if ($result->num_rows > 0){
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$connection->close();

$no = 1;
?>

<html>
<head>
    <title>User list</title>
    <meta charset='UTF-8' />
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" id="css-main" href="assets/css/oneui.css">
    <link rel="stylesheet" href="assets/css/index.css?v=1">
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
</head>
<body>
<div class="block">
    <div class="block-header">
        <div class="block-options">
            <a class="" href="index.php">Chat</a>
            <a class="" href="users.php">Users</a>
            <a class="" href="out.php">Get out</a>
        </div>
        <h3 class="block-title">Users</h3>
    </div>
    <div class="block-content">
        <table class="table table-striped table-borderless table-header-bg">
            <thead>
            <tr>
                <th class="text-center" style="width: 50px;">#</th>
                <th>ID</th>
                <th class="hidden-xs" style="width: 15%;">Access</th>
                <th class="text-center" style="width: 100px;">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($users as $user) {
                ?>
                <tr>
                    <td class="text-center"><?php echo $no; ?></td>
                    <td><?php echo $user['username']; ?></td>
                    <td class="hidden-xs">
                        <span class="label label-<?php switch ($user['role']){
                            case ROLE_USER:
                                echo "info";
                                break;
                            case ROLE_ADMIN:
                                echo "success";
                        } ?>"><?php switch ($user['role']){
                                case ROLE_USER:
                                    echo "User";
                                    break;
                                case ROLE_ADMIN:
                                    echo "Admin";
                            } ?></span>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <a href="setting.php?user=<?php echo $user['id']; ?>">
                                <button class="btn btn-xs btn-default" type="button" data-toggle="tooltip" title="Edit Client"><i class="fa fa-pencil"></i></button>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php
                $no ++;
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>