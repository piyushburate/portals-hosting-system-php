<?php
include("connection.php");

$portal_id = $_REQUEST['portal_id'];
$username = $_REQUEST['username'];
$msg = $_REQUEST['message'];

$query = "INSERT INTO comments (portal_id, username, msg) VALUES ('$portal_id', '$username', '$msg')";
$data = $conn->query($query);
if ($data) {
    echo "<script> history.back();</script>";
} else {
    echo "<script> alert('An internal error occurred!'); history.back();</script>";
}

?>