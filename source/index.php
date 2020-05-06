<?php
include_once("./include/config.php");
include_once('./include/checklogin.php');

$connection = new mysqli($db_host, $db_username, $db_password, $db_database);

$users = [];

if ($_SESSION['user']['role'] == 5) {
    $query = "SELECT * FROM `users` WHERE `id` <> " . $_SESSION['user']['id'];
    $result = $connection->query($query);
    if ($result->num_rows > 0){
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
} else {
    // Get Admin
    $query = "SELECT * FROM users WHERE role = 5 ;";
    $result = $connection->query($query);

    if ($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }

    // Get other partners
    $query = "SELECT * FROM user_match WHERE id1 = " . $_SESSION['user']['id'] . " ;";
    $result = $connection->query($query);

    if ($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
            $query = "SELECT * FROM users WHERE id = " . $row['id2'];
            $result = $connection->query($query);

            if ($result->num_rows > 0){
                array_push($users, $result->fetch_assoc());
            }
        }
    }

    $query = "SELECT * FROM user_match WHERE id2 = " . $_SESSION['user']['id'] . " ;";
    $result = $connection->query($query);

    if ($result->num_rows > 0){
        while ($row = $result->fetch_assoc()) {
            $query = "SELECT * FROM users WHERE id = " . $row['id1'];
            $result = $connection->query($query);

            if ($result->num_rows > 0){
                array_push($users, $result->fetch_assoc());
            }
        }
    }


}

$connection->close();
?>
<!doctype html>
<html>
<head>
	<meta charset='UTF-8' />
    <link rel="stylesheet" href="assets/css/jquery-ui.min.css">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/oneui.css">
    <link rel="stylesheet" href="assets/css/theme-black.css">
    <link rel="stylesheet" href="assets/css/index.css?v=1">
	<script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.form.min.js"></script>
    <script src="assets/js/jquery-ui.min.js"></script>
    <script src="assets/js/jquery.ui-contextmenu.min.js"></script>
    <script src="assets/js/index.js?v=1"></script>
</head>

<body>
	<div id='body'>
        <div class="block">
            <div class="block-header">
                <h3 class="pull-left"><?php echo $_SESSION['user']['username']; ?></h3>
                <div class="block-options">
                <?php
                if ($_SESSION['user']['role'] == 5) {
                    ?>
                        <a class="" href="users.php">Users</a>
                <?php
                }
                ?>
                    <a class="" href="out.php">Logout</a>
                </div>
            </div>
        </div>
        <div class="content row">
            <div class="content-grid col-lg-3 col-xs-3">
                <div class="block-header">
                    <h3 class="block-title">People</h3>
                </div>
                <div data-height="480px" id="contact-list">
                    <?php
                    foreach ($users as $user){
                    	if ($user['role'] != '0') {
                        ?>
                        <a class="block block-rounded block-link-hover3 user-contact" id="user-<?php echo $user['id'];?>" data-id="<?php echo $user['id'] ?>" data-name="<?php echo $user['username'] ?>">
                            <div class="block-content block-content-full clearfix">
                                <div class="pull-left">
                                    <span class="online-status"></span>
                                </div>
                                <div class="text-right pull-left">
                                    <div class="font-w600 push-5"><?php echo $user['username']; ?></div>
                                </div>
                            </div>
                        </a>

                    	<?php
                    	}
                    }
                    ?>
                </div>
            </div>
            <div id="message-panel" class="col-xs-9">
                <!-- Scroll Height 300px -->
                <div class="block">
                    <div class="block-header">
                        <h3 class="block-title pull-left">Message</h3>
                        <button id="video-call-btn" class="btn btn-success btn-rounded pull-right"><i class="fa fa-phone"></i> </button>
                        <button id="clear-store-btn" class="btn btn-danger btn-rounded pull-right"><i class="fa fa-undo"></i> </button>
                    </div>
                    <div class="block-content block-content-full" style="border: solid 1px #999;">
                        <!-- SlimScroll Container -->
                        <div id="msg-content" data-last="0">
                        </div>
                    </div>
                    <div class="block-content block-content-full" style="border: solid 1px #999;">
                        <p style="margin: 0;">Leave a message (Press Enter to send message)</p>
                        <div class="row">
                            <div class="col-xs-9">
                                <textarea id="new-message-text" rows="3" style="width:100%;"></textarea>
                                <input type="hidden" id="user-id" value="<?php echo $_SESSION['user']['id'];?>"/>
                                <input type="hidden" id="user-name" value="<?php echo $_SESSION['user']['username'];?>"/>
                            </div>
                            <div class="col-xs-3">
                                <button id="msg-send-btn" class="btn btn-sm btn-info">
                                    <i class="fa fa-send push-5-r"></i>
                                    Send
                                </button>
                            </div>
                        </div>
                        <div class="block">
                            <form id="uploadForm" action="get.php" method="post">
                                <input type="hidden" id="action" name="action" value="upload-file"/>
                                <input type="hidden" id="partner-id" name="partner-id"/>
                                <span class="btn btn-info file-button">
                                    <i class="glyphicon-plus"></i>
                                    <span>Attach File</span>
                                    <input name="file" type="file" id="file"/>
                                </span>
                                <span id="file-name"></span>
                                <button id="upload-file-button" class="btn btn-info">
                                    <i class="glyphicon glyphicon-upload"></i>
                                    <span>Upload</span>
                                </button>
                            </form>
                        </div>
                        <div id="progress-div"><div id="progress-bar"></div></div>
                        <div id="targetLayer"></div>
                    </div>
                </div>
            </div>
        </div>
	</div>
<div id="modal-closed">
    <h1>Connection failed.</h1>
    <input id="connection-refresh-btn" type="button" class="btn btn-info" value="Refresh"/>
</div>
<div id="video-panel">
	<div id="my-video"></div>
	<div id="your-video"></div>
	<div id="controls">
		<button id="video-call-end" class="btn btn-danger btn-rounded"><i class="fa fa-phone"></i></button>
	</div>
</div>
</body>

</html>