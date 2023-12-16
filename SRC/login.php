<?php
session_start();
if (isset($_SESSION['username'])) {
    echo "<script> history.back();</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Hoster.com</title>
    <meta name="description" content="URL Shortner Website">
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/signin.css">
    <script src="static/js/jquery_3.7.0.min.js"></script>
</head>

<body>
    <div id="app">
        <div class="box">
            <div class="title">Login Form</div>
            <form class="form" action="php/authLogin.php" method="post" name="login_form" autocomplete="on">
                <div class="input username">
                    <label for="form_username">Username</label>
                    <input type="text" name="username" id="form_username" minlength="3" maxlength="20" required
                        autocomplete="on">
                </div>
                <div class="input password">
                    <label for="form_password">Password</label>
                    <input type="password" name="password" id="form_password" minlength="8" maxlength="15" required
                        autocomplete="on">
                    <a href="#">Forgot password?</a>
                </div>
                <div class="submit">
                    <button type="submit" name="submit" id="form_submit">Submit</button>
                </div>
            </form>
            <div class="extra">
                Not a user? <a href="signup.php" data-link>Signup now</a>
            </div>
        </div>
    </div>
    <script src="static/js/main.js"></script>
    <script>
        $(".form").on("submit", async (e) => {
            btnLoad($("#form_submit"), true)
        })

    </script>
</body>

</html>