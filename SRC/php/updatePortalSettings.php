<?php
include("connection.php");
// error_reporting(0);
session_start();

$method = $_REQUEST['method'];
$portal_id = $_REQUEST['portal_id'];
$host_id = $_REQUEST['host_id'];
$key = $_REQUEST['key'];
$value = $_REQUEST['value'];

$success_msg = "success";
$error_msg = "failed";
if($method != "api"){
    $success_msg = "<script> history.back();</script>";
    $error_msg = "<script> alert('An internal error occurred!'); history.back();</script>";
}

$query = "SELECT portal_id, host_id FROM portals WHERE portal_id = '$portal_id' and host_id = '$host_id'";
$data = $conn->query($query);
$total = mysqli_num_rows($data);
if ($total == 1) {
    $row = $data->fetch_assoc();
    if($host_id == $_SESSION['username']){
        $query2 = "UPDATE portals SET settings = JSON_REPLACE(settings, '$.$key', $value) WHERE portal_id = '$portal_id' and host_id = '$host_id'";
        $data2 = $conn->query($query2);
        if ($data2) {
            echo $success_msg;
        } else {
            echo $error_msg;
        }
    } else {
        exit(0);
    }
} else {
    echo $error_msg;
}

?>