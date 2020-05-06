<?php

require_once("./include/config.php");

if(isset($_POST['username']) && isset($_POST['password']))
{
	$connection = new mysqli($db_host, $db_username, $db_password, $db_database);

    $id = $_POST['username'];
    $password = $_POST['password'];

    $query = "
            SELECT
                *
            FROM users
            WHERE
                username = '" . $id."'
        ";

    try
    {
        $result = $connection->query($query);
    }
    catch(Exception $ex)
    {
        die("Failed to run query: " . $ex->getMessage());
    }

    if($result->num_rows > 0)
    {
        $row = $result->fetch_assoc();
        
        $connection->close();
        if ($row['role'] != '0') {
            if (md5($password) === $row['pwd']) {
                $_SESSION['user'] = $row;
                header("Location: index.php");
            } else {
                $_SESSION['error'] = "Password is wrong.";
            }
        } else {
            $_SESSION['error'] = "You are disallowed.";
        }
    } else {
        $connection->close();

        $_SESSION['error'] = "We don't know the ID. Why don't you register?";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">

    <title>PHP</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="assets/css/style.css" rel="stylesheet">

    <link href="assets/css/custom.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="assets/js/html5shiv.js"></script>
    <script src="assets/js/respond.min.js"></script>
    <![endif]-->
</head>

<body class="login-body">

<div class="container">

    <form class="form-signin" action="" method="post" id="form-signin">
        <h2 class="form-signin-heading">Login</h2>
        <div class="login-wrap">
            <?php if (isset($_SESSION['error'])) echo '<p class="help-block">'.$_SESSION["error"].'</p>'; unset($_SESSION['error']);?>
            <input type="text" class="form-control" placeholder="ID" autofocus name="username" value="<?php if (isset($id)) echo $id; ?>" id="username" required>
            <input type="password" class="form-control" placeholder="Password" name="password" value="" id="password" required>
            <button class="btn btn-lg btn-login btn-block" type="submit">Login</button>
        </div>
        <div class="login-wrap text-right">
            <a href = "register.php">Register</a>
        </div>
    </form>

</div>



<!-- js placed at the end of the document so the pages load faster -->
<script src="assets/js/jquery.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.validate.min.js"></script>
<!--script for this page-->
<script>
    $().ready(function() {
        $("#form-signin").validate({
            rules: {
                username: {
                    required: true,
                    minlength: 2
                },
                password: {
                    required: true
                }
            },
            messages: {
                username: {
                    required: "Please enter a username",
                    minlength: "Your username must consist of at least 2 characters"
                },
                password: {
                    required: "Please provide a password"
                }
            }
        });
    });
</script>

</body>
</html>