<?php
session_start();
include("connection.php");
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];
$query = "SELECT * FROM users WHERE username = BINARY '$username' && password = BINARY '$password'";
$data = $conn->query($query);

if ($data) {
    $total = mysqli_num_rows($data);
    if ($total == 1) {
        $row = $data->fetch_assoc();
        $_SESSION['uid'] = $row['uid'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['email'] = $row['email'];
        // echo "<script> alert('Login Successfull!');</script>";
        echo "<script> history.back();</script>";
    } else {
        echo "<script> alert('Invalid username or password!'); history.back();</script>";
    }
} else {
    echo "<script> alert('An internal error occurred!'); history.back();</script>";
}
?>