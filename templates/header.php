<!DOCTYPE html>

<html>

    <head>

        <link href="css/bootstrap.css" rel="stylesheet"/>
        <link href="css/bootstrap-responsive.css" rel="stylesheet"/>
        <link href="css/styles.css" rel="stylesheet"/>
        <link href="css/jquery-ui-1.10.3.custom.css" rel="stylesheet">

        <link rel="icon" type="image/png" href="img/icon.png">
        <?php if (!empty($title)): ?>
            <title>CloudSync: <?= htmlspecialchars($title); ?></title>
        <?php else: ?>
            <title>CloudSync</title>
        <?php endif ?>

        <script type="text/javascript" src="js/jquery-1.8.2.js"></script>
        <script type="text/javascript" src="js/bootstrap.js"></script>
        <script type="text/javascript" src="js/sorttable.js"></script>
        <script type="text/javascript" src="js/jquery.highlight-upd.js"></script>

    </head>
    <body style="background: #c1c1c1 /*url(img/back.png) repeat-x;">

        <div class="container-fluid">

            <div id="top">
                <div class="navbar">
                    <div class="navbar-inner">
                        <img class="brand topp" alt="logo" src="img/textEffect.png" style="margin-bottom: -10px; margin-top: -3px;">
                        <ul class="nav">
                            <?php if (!isset($_SESSION['id'])) : ?>
                                <li <?php if($_SERVER["PHP_SELF"] == "/login.php") echo 'class="active"'; ?> >
                                    <a href="/login.php"><i class="icon-home"></i> <strong> Login</strong></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li <?php if($_SERVER["PHP_SELF"] == "/register.php") echo 'class="active"'; ?> >
                                    <a href="register.php"><i class="icon-file"></i> <strong> Register</strong> New User</a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li <?php if($_SERVER["PHP_SELF"] == "/forgot.php") echo 'class="active"'; ?> >
                                    <a href="forgot.php"><i class="icon-flag"></i> <strong> Forgot</strong> Password</a>
                                </li>
                            <?php else: ?>
                                <?php $name = query("SELECT * FROM users WHERE id = ?", $_SESSION["id"]);?>
                                <li <?php if($_SERVER["PHP_SELF"]=="/index.php") echo 'class="active"'; ?> >
                                    <a href="/"><i class="icon-home"></i> <strong> Home</strong></a>
                                </li>
                                <li class="divider-vertical"></li>
                                <li><a href="logout.php" title="Goodbye"><i class="icon-share-alt"></i> <strong> Log</strong> Out</a></li>
                            <?php endif ?>
                        </ul>
                        <?php if(isset($_SESSION['id'])) echo"<p class='navbar-text' style='text-align: right;'>Signed in as ".ucfirst($name[0]["username"])."</p>";?>
                    </div>
                </div>
            </div>
            <div id="middle">