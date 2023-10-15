<?php
error_reporting(0);

require '../assets/authdata/credentials.php';
require '../assets/authdata/keyauth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['un'])) {
    header("Location: ../dashboard/");
    exit();
}

$KeyAuthApp = new KeyAuth\api($name, $OwnerId, $version);

if (!isset($_SESSION['sessionid'])) {
    $KeyAuthApp->init();
}

?>

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta property="og:type" content="website" />
    <meta property="og:image" content="" />

    <link href="https://cdn.keyauth.cc/front/assets/img/favicon.png" rel="icon">

    <link rel="stylesheet" media="screen" href="static/img/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.keyauth.win/dashboard/unixtolocal.js"></script>

    <link rel="stylesheet" href="vendor/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700">

    <link rel="stylesheet" media="screen" href="../assets/static/img/styles.css">
    <link rel="stylesheet" href="../assets/static/css/style.css?v=1.2">
    <link rel="stylesheet" href="../assets/css/style.default.css" id="theme-stylesheet">
    <?php
    echo '
	    <title>KeyAuth - Register to ' . $name . ' Panel</title>
        <meta name="description" content="Register to webportal of' . $name . '">
        ';
    ?>


</head>
<!DOCTYPE html>
<html lang="en">

<script>
    function checkForm(form) {
        if (!form.terms.checked) {
            alert("Please indicate that you accept the Terms and Conditions");
            form.terms.focus();
            return false;
        }
        return true;
    }
</script>

<!DOCTYPE html>
<html long="eng">

<div id="particles-js"></div>
<div class="count-particles">
    <span class="js-count-particles"></span>
</div>
<script src="../assets/static/img/particles.js"></script>
<script src="../assets/static/img/app.js"></script>
<script src="../assets/static/img/lib/stats.js"></script>

<body>
    <div class="body">
        <div class="register">
            <form class="form" method="post">

                <div class="titulo">
                    <a href="index.php" class="">
                        <h2>Register</h2>
                    </a>
                </div>

                <div class="inputBox">
                    <input type="text" name="username" required="required">
                    <span>Username</span>
                    <i></i>
                </div>
                <div class="inputBox">
                    <input type="password" name="password" required="required">
                    <span>Password</span>
                    <i></i>
                </div>

                <div class="inputBox">
                    <input type="text" name="license" required="required">
                    <span>Key</span>
                    <i></i>
                </div>

                <input type="submit" name="register" value="Regsiter">
        </div>
    </div>

    <script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

    <script src="../assets/js/main.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

    <?php
    if (isset($_POST['register'])) {
        if ($KeyAuthApp->register($_POST['username'], $_POST['password'], $_POST['license'])) {
            $_SESSION['un'] = $_POST['username'];
            echo "<meta http-equiv='Refresh' Content='2; url=/dashboard/home'>";
            echo '
                        <script type=\'text/javascript\'>
                        
                        const notyf = new Notyf();
                        notyf
                          .success({
                            message: \'You have successfully registered!\',
                            duration: 3500,
                            dismissible: true
                          });                
                        
                        </script>
                        ';
        }
    }
    ?>
</body>

</html>