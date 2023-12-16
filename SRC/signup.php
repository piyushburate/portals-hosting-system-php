<?php
session_start();
if(isset($_SESSION['username'])){
    echo "<script> history.back();</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup - Hoster.com</title>
    <meta name="description" content="URL Shortner Website">
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/signin.css">
    <script src="static/js/jquery_3.7.0.min.js"></script>
</head>

<body>
    <div id="app">
        <div class="box">
            <div class="title">Signup Form</div>
            <form class="form" action="php/authSignup.php" method="post" name="signup_form" autocomplete="on">
                <div class="input name">
                    <label for="form_name">Name</label>
                    <input type="text" name="name" id="form_name" required autocomplete="on">
                </div>
                <div class="input email">
                    <label for="form_email">Email</label>
                    <input type="email" name="email" id="form_email" required autocomplete="on">
                </div>
                <div class="input username">
                    <label for="form_username">Username</label>
                    <input type="text" name="username" id="form_username" minlength="3" maxlength="20" required
                        autocomplete="on">
                </div>
                <div class="input password">
                    <label for="form_password">Password</label>
                    <input type="password" name="password" id="form_password" minlength="8" maxlength="15" required
                        autocomplete="on">
                </div>
                <div class="submit">
                    <button type="submit" id="form_submit">Submit</button>
                </div>
            </form>
            <div class="extra">
                Already a user? <a href="login.php" data-link>Login now</a>
            </div>
        </div>
    </div>
    <script src="static/js/main.js"></script>
    <script>
        $(".form").on("submit", e => {
            // e.preventDefault()
            btnLoad($("#form_submit"), true)
        })

    </script>
</body>

</html>