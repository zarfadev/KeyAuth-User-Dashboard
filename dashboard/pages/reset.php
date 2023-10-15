<?php
error_reporting(0);

require '../../assets/authdata/credentials.php';
require '../../assets/authdata/keyauth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['un'])) {
    die("not logged in");
}

if (isset($_POST['logout'])) {
    unset($_SESSION['notificacionesLeidas']);
    session_destroy();
    header("Location: /");
    exit();
}

$KeyAuthApp = new KeyAuth\api($name, $OwnerId, $version);

$url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=getsettings";

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

$resp = curl_exec($curl);
$json = json_decode($resp);

if (!$json->success) {
    die("Error: {$json->message}");
}

function findSubscription($name, $list)
{
    for ($i = 0; $i < count($list); $i++) {
        if ($list[$i]->subscription == $name) {
            return true;
        }
    }
    return false;
}

$username = $_SESSION["user_data"]["username"];
$subscriptions = $_SESSION["user_data"]["subscriptions"];
$subscription = $_SESSION["user_data"]["subscriptions"][0]->subscription;
$expiry = $_SESSION["user_data"]["subscriptions"][0]->expiry;
$ip = $_SESSION["user_data"]["ip"];
$hwid = $_SESSION["user_data"]["hwid"];
$createdate = $_SESSION["user_data"]["createdate"];
$lastLogin = $_SESSION["user_data"]["lastlogin"];

$download = $json->download;
$webdownload = $json->webdownload;
$appcooldown = $json->cooldown;

$numKeys = $KeyAuthApp->numKeys;
$numUsers = $KeyAuthApp->numUsers;
$numOnlineUsers = $KeyAuthApp->numOnlineUsers;
$customerPanelLink = $KeyAuthApp->customerPanelLink;
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>
        <?php echo $name; ?>
    </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="stylesheet" href="../assets/vendor/choices.js/public/assets/styles/choices.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Muli:300,400,700">
    <link rel="stylesheet" href="../assets/css/style.default.css" id="theme-stylesheet">
    <link rel="stylesheet" href="../assets/css/custom.css">
    <link rel="stylesheet" href="../assets/css/style.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
    <script src="https://cdn.keyauth.win/dashboard/unixtolocal.js"></script>

    <link rel="shortcut icon" href="https://cdn.keyauth.cc/front/assets/img/favicon.png">
</head>

<body>
    <header class="header">
        <nav class="navbar navbar-expand-lg py-3 bg-dash-dark-2 border-bottom border-dash-dark-1 z-index-10">
            <div class="search-panel">
                <div class="search-inner d-flex align-items-center justify-content-center">
                    <div
                        class="close-btn d-flex align-items-center position-absolute top-0 end-0 me-4 mt-2 cursor-pointer">
                        <span>Close </span>
                        <svg class="svg-icon svg-icon-md svg-icon-heavy text-gray-700 mt-1">
                            <use xlink:href="#close-1"> </use>
                        </svg>
                    </div>
                    <div class="row w-100">
                        <div class="col-lg-8 mx-auto">
                            <form class="px-4" id="searchForm" action="#">
                                <div class="input-group position-relative flex-column flex-lg-row flex-nowrap">
                                    <input class="form-control shadow-0 bg-none px-0 w-100" type="search" name="search"
                                        placeholder="What are you searching for...">
                                    <button
                                        class="btn btn-link text-gray-600 px-0 text-decoration-none fw-bold cursor-pointer text-center"
                                        type="submit">Search</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid d-flex align-items-center justify-content-between py-1">
                <div class="navbar-header d-flex align-items-center"><a class="navbar-brand text-uppercase text-reset"
                        href="Dashboard.php">
                        <div class="brand-text brand-big"><strong
                                class="text-primary">Desing</strong><strong>KeyAuth</strong></div>
                        <div class="brand-text brand-sm"><strong class="text-primary">D</strong><strong>K</strong>
                        </div>
                    </a>
                    <button class="sidebar-toggle">
                        <svg class="svg-icon svg-icon-sm svg-icon-heavy transform-none">
                            <use xlink:href="#arrow-left-1"> </use>
                        </svg>
                    </button>
                </div>
                <ul class="list-inline mb-0">
                    <li class="list-inline-item dropdown px-lg-2">
                        <a class="nav-link text-reset px-1 px-lg-0" id="notificationDropdown" href="#"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <svg class="svg-icon svg-icon-xs svg-icon-heavy">
                                <use xlink:href="#envelope-1"> </use>
                            </svg>
                            <span class="badge bg-dash-color-1" id="badge">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                            aria-labelledby="notificationDropdown">
                            <div class="dropdown-divider"></div>
                            <div class="marcar-como-leido">
                                <button onclick="marcarComoLeido()">Mark as read</button>
                            </div>
                        </div>
                    </li>

                    <li class="list-inline-item logout px-lg-2">
                        <a class="nav-link text-sm text-reset px-1 px-lg-0" onclick="logout()" style="cursor: pointer;">
                            <form method="POST" id="logout-form">
                                <span class="d-none d-sm-inline-block">
                                    Logout
                                </span>
                                <svg class="svg-icon svg-icon-xs svg-icon-heavy">
                                    <use xlink:href="#disable-1"></use>
                                </svg>
                                <input type="hidden" name="logout" value="true">
                            </form>
                        </a>

                        <script>
                            function logout() {
                                localStorage.clear();

                                document.getElementById('logout-form').submit();
                            }
                        </script>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="d-flex align-items-stretch">
        <nav id="sidebar">
            <div class="sidebar-header d-flex align-items-center p-4">
                <img alt="Logo" src="https://avatars.githubusercontent.com/u/145287911?s=400&u=c6cfe206a3e2a6aeafbe329e3d183bc620268c1f&v=4"
                    style="width: 48px; height: 48px;">
                <div class="ms-3 title">
                    <h1 class="h5 mb-1">
                        <?php echo ucfirst($username); ?>
                    </h1>
                    <p class="text-sm text-gray-700 mb-0 lh-1"
                        style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; max-width: 190px;">
                        <?php echo $subscription; ?>
                    </p>
                </div>
            </div><span class="text-uppercase text-gray-600 text-xs mx-3 px-2 heading mb-2">Main</span>
            <ul class="list-unstyled">
                <li class="sidebar-item"><a class="sidebar-link" href="/dashboard/home">
                        <svg class="svg-icon svg-icon-sm svg-icon-heavy">
                            <use xlink:href="#real-estate-1"> </use>
                        </svg><span>Home </span></a></li>
                <li class="sidebar-item active"><a class="sidebar-link" href="/dashboard/pages/reset">
                        <svg class="svg-icon svg-icon-sm svg-icon-heavy">
                            <use xlink:href="#imac-screen-1"> </use>
                        </svg><span>Reset HWID </span></a></li>
            </ul><span class="text-uppercase text-gray-600 text-xs mx-3 px-2 heading mb-2">Extras</span>
            <ul class="list-unstyled">
                <li class="sidebar-item"><a class="sidebar-link" href="/dashboard/pages/status">
                        <svg class="svg-icon svg-icon-sm svg-icon-heavy">
                            <use xlink:href="#security-shield-1"> </use>
                        </svg><span>Status </span></a></li>
            </ul>
        </nav>
        <div class="page-content">
            <div class="bg-dash-dark-2 py-4">
                <div class="container-fluid">
                    <h2 class="h5 mb-0">Reset HWID</h2>
                </div>
            </div>
            <section>
                <div class="container-fluid">
                    <div class="row gy-4">
                        <div class="col-md-3 col-sm-6">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div class="me-2">
                                            <svg class="svg-icon svg-icon-sm svg-icon-heavy text-gray-600 mb-2">
                                                <use xlink:href="#user-1"> </use>
                                            </svg>
                                            <p class="text-sm text-uppercase text-gray-600 lh-1 mb-0">
                                                <?php echo ucfirst($username); ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 3px">
                                        <div class="progress-bar bg-dash-color-1" role="progressbar" style="width: 100%"
                                            aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div class="me-2">
                                            <svg class="svg-icon svg-icon-sm svg-icon-heavy text-gray-600 mb-2">
                                                <use xlink:href="#stack-1"> </use>
                                            </svg>
                                            <p class="text-sm text-uppercase text-gray-600 lh-1 mb-0">Last Login:
                                                <script>
                                                    var lastLoginTimestamp = <?php echo $lastLogin; ?>;
                                                    var lastLoginDate = new Date(lastLoginTimestamp * 1000);
                                                    var formattedLastLogin = lastLoginDate.toLocaleString('en-US', {
                                                        year: 'numeric',
                                                        month: '2-digit',
                                                        day: '2-digit',
                                                        hour: 'numeric',
                                                        minute: 'numeric',
                                                        hour12: true
                                                    });

                                                    document.write(formattedLastLogin);
                                                </script>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 3px">
                                        <div class="progress-bar bg-dash-color-2" role="progressbar" style="width: 70%"
                                            aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div class="me-2">
                                            <svg class="svg-icon svg-icon-sm svg-icon-heavy text-gray-600 mb-2">
                                                <use xlink:href="#survey-1"> </use>
                                            </svg>
                                            <p class="text-sm text-uppercase text-gray-600 lh-1 mb-0">Expiry:
                                                <script>
                                                    var expiryTimestamp = <?php echo $expiry; ?>;
                                                    var expiryDate = new Date(expiryTimestamp * 1000);
                                                    var formattedExpiry = expiryDate.toLocaleString('en-US', {
                                                        year: 'numeric',
                                                        month: '2-digit',
                                                        day: '2-digit',
                                                        hour: 'numeric',
                                                        minute: 'numeric',
                                                        hour12: true
                                                    });

                                                    document.write(formattedExpiry);
                                                </script>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 3px">
                                        <div class="progress-bar bg-dash-color-3" role="progressbar" style="width: 55%"
                                            aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card mb-0">
                                <div class="card-body">
                                    <div class="d-flex align-items-end justify-content-between mb-2">
                                        <div class="me-2">
                                            <svg class="svg-icon svg-icon-sm svg-icon-heavy text-gray-600 mb-2">
                                                <use xlink:href="#paper-stack-1"> </use>
                                            </svg>
                                            <p class="text-sm text-uppercase text-gray-600 lh-1 mb-0">4.240.30.1002</p>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 3px">
                                        <div class="progress-bar bg-dash-color-4" role="progressbar" style="width: 35%"
                                            aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="pt-0">
                <div class="container-fluid">
                    <div class="row gy-4">
                        <div class="col-lg-6">
                            <div class="card mb-0">
                                <div class="card-header">
                                    <h3 class="h4 mb-0">Reset HWID</h3>
                                </div>

                                <div class="card-body pt-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Function</th>
                                                    <th>Work</th>
                                                    <th>Description</th>
                                                    <th>Reset</th>
                                                    <th id="cooldown-header" style="display:none;">Cooldown</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th scope="row">HWID</th>
                                                    <td>Reset</td>
                                                    <td>Change login Device</td>
                                                    <td>
                                                        <?php
                                                        $un = $_SESSION['un'];
                                                        $url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=userdata&user={$un}";

                                                        $curl = curl_init($url);
                                                        curl_setopt($curl, CURLOPT_URL, $url);
                                                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                                                        $resp = curl_exec($curl);
                                                        $json = json_decode($resp);
                                                        $cooldown = $json->cooldown;
                                                        $token = $json->token;
                                                        $today = time();

                                                        if (is_null($cooldown) || $today > $cooldown) {
                                                            echo '<form method="post">
                                                        <button name="resethwid" class="btn btn-success btn-sm btn-rad btn-lg">
                                                            <i class="fas fa-redo-alt fa-sm text-white-50"></i>&nbsp;&nbsp;Reset HWID
                                                        </button>
                                                </form>';
                                                        } else {
                                                            echo '
                                                    <button disabled="disabled" class="btn btn-danger btn-sm btn-rad btn-lg">
                                                        <i class="fas fa-redo-alt fa-sm text-white-50"></i>&nbsp;&nbsp;Reset HWID
                                                    </button>';
                                                            echo '<script>document.getElementById("cooldown-header").style.display = "table-cell";</script>';
                                                        }
                                                        ?>

                                                        <?php
                                                        if (isset($_POST['resethwid'])) {
                                                            $today = time();
                                                            $cooldown = $today + $appcooldown;
                                                            $un = $_SESSION['un'];
                                                            $url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=resetuser&user={$un}";

                                                            $curl = curl_init($url);
                                                            curl_setopt($curl, CURLOPT_URL, $url);
                                                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                                            curl_exec($curl);

                                                            $url = "https://keyauth.win/api/seller/?sellerkey={$SellerKey}&type=setcooldown&user={$un}&cooldown={$cooldown}";

                                                            $curl = curl_init($url);
                                                            curl_setopt($curl, CURLOPT_URL, $url);
                                                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                                            curl_exec($curl);

                                                            echo '
                                                <script type=\'text/javascript\'>
                                                    const notyf = new Notyf();
                                                    notyf.success({
                                                        message: \'Reset HWID!\',
                                                        duration: 3500,
                                                        dismissible: true
                                                    });                
                                                </script>
                                                ';
                                                            echo "<meta http-equiv='Refresh' Content='2;'>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (!is_null($cooldown) && $today <= $cooldown) {
                                                            echo '<p><script>
                                                    var cooldownTimestamp = ' . $cooldown . ';
                                                    var cooldownDate = new Date(cooldownTimestamp * 1000);
                                                    var formattedCooldown = cooldownDate.toLocaleString("en-US", {
                                                        year: "numeric",
                                                        month: "2-digit",
                                                        day: "2-digit",
                                                        hour: "numeric",
                                                        minute: "numeric",
                                                        hour12: true
                                                    });

                                                    document.write(formattedCooldown);
                                                </script></p>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <footer class="position-absolute bottom-0 bg-dash-dark-2 text-white text-center py-3 w-100 text-xs"
                id="footer">
                <div class="container-fluid text-center">
                    <p class="mb-0 text-dash-gray">2023 &copy; Design by <a
                            href="https://discord.com/users/959935214895890532"> Zarfala</a>.</p>
                </div>
            </footer>
        </div>
    </div>
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/vendor/just-validate/js/just-validate.min.js"></script>
    <script src="../assets/vendor/chart.js/Chart.min.js"></script>
    <script src="../assets/vendor/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="../assets/js/charts-home.js"></script>

    <script>
        var notificaciones = [];

        function agregarNotificacion(titulo, mensaje) {
            var notificationsList = document.querySelector('.dropdown-menu.preview-list');

            var notificacionElement = document.createElement('a');
            notificacionElement.className = 'dropdown-item preview-item';
            notificacionElement.innerHTML = `
            <div class="preview-item-content">
                <p class="preview-subject mb-1">${titulo}</p>
                <p class="text-muted ellipsis mb-0">${mensaje}</p>
            </div>
        `;

            notificationsList.insertBefore(notificacionElement, notificationsList.firstChild);

            notificaciones.push({
                titulo: titulo,
                mensaje: mensaje
            });

            actualizarContador();
        }

        function marcarComoLeido() {
            localStorage.setItem('notificacionesLeidas', 'true');
            actualizarContador();
        }

        function actualizarContador() {
            var badge = document.getElementById('badge');
            var notificacionesLeidas = localStorage.getItem('notificacionesLeidas');
            if (notificacionesLeidas === 'true') {
                badge.textContent = '0';
                badge.style.display = 'none';
            } else {
                badge.textContent = notificaciones.length.toString();
            }
        }

        window.onload = function () {
            actualizarContador();
            agregarNotificacion('New update', 'We are working on it');
            agregarNotificacion('Second update', 'Another update');
            agregarNotificacion('Third update', 'More news');
        };
    </script>

    <script src="../assets/js/front.js"></script>
    <script>
        function injectSvgSprite(path) {

            var ajax = new XMLHttpRequest();
            ajax.open("GET", path, true);
            ajax.send();
            ajax.onload = function (e) {
                var div = document.createElement("div");
                div.className = 'd-none';
                div.innerHTML = ajax.responseText;
                document.body.insertBefore(div, document.body.childNodes[0]);
            }
        }
        injectSvgSprite('https://bootstraptemple.com/files/icons/orion-svg-sprite.svg');
    </script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
        integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <script>
        const avatars = document.querySelectorAll(".avatar");

        avatars.forEach((a) => {
            const charCodeRed = a.dataset.label.charCodeAt(0);
            const charCodeGreen = a.dataset.label.charCodeAt(1) || charCodeRed;

            const red = Math.pow(charCodeRed, 1) % 200;
            const green = Math.pow(charCodeGreen, 100) % 200;
            const blue = (red + green) % 200;

            a.style.background = `rgb(${red}, ${green}, ${blue})`;
        });
    </script>

</body>

</html>