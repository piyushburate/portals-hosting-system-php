<?php
include("connection.php");
$name = $_POST['name'];
$email = $_POST['email'];
$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT username FROM users WHERE username = BINARY '$username'";
$data = $conn->query($query);
if ($data) {
    $total = mysqli_num_rows($data);
    if ($total == 0) {
        $query2 = "INSERT INTO users (name, email, username, password) VALUES ('$name', '$email', '$username', '$password')";
        $data2 = $conn->query($query2);
        if ($data2) {
            // echo "<script> alert('Signup successful!');</script>";
            header("Location: /php/authLogin.php?username=$username&password=$password");
        } else {
            echo "<script> alert('An internal error occurred!'); history.back();</script>";
        }
    } else {
        echo "<script> alert('Username already taken!'); history.back();</script>";
    }
} else {
    echo "<script> alert('An internal error occurred!'); history.back();</script>";
}

?>