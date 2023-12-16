<?php
include("connection.php");
session_start();

$portal_id = $_REQUEST['portal_id'];
$host_id = $_REQUEST['host_id'];
$title = $_REQUEST['title'];
$msg = $_REQUEST['message'];
$visibility = $_REQUEST['visibility'];

if($_SESSION['username'] != $host_id){
    echo "<script> history.back();</script>";
    exit(0);
}

$query = "INSERT INTO notifications (portal_id, host_id, title, msg, visibility) VALUES ('$portal_id', '$host_id', '$title', '$msg', $visibility)";
$data = $conn->query($query);
if ($data) {
    echo "<script> history.back();</script>";
} else {
    echo "<script> alert('An internal error occurred!'); history.back();</script>";
}

?>