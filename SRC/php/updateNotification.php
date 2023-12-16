<?php
include("connection.php");
// error_reporting(0);
session_start();

$method = $_REQUEST['method'];
$id = $_REQUEST['id'];
$host_id = $_REQUEST['host_id'];
$action = $_REQUEST['action'];
if($action == 'update'){
    $key = $_REQUEST['key'];
    $value = $_REQUEST['value'];
}

$success_msg = "success";
$error_msg = "failed";
if ($method != "api") {
    $success_msg = "<script> history.back();</script>";
    $error_msg = "<script> alert('An internal error occurred!'); history.back();</script>";
}

$query = "SELECT id FROM notifications WHERE id = $id";
$data = $conn->query($query);
$total = mysqli_num_rows($data);
if ($total == 1) {
    $row = $data->fetch_assoc();
    if ($host_id == $_SESSION['username']) {
        if ($action == 'update') {
            $query2 = "UPDATE notifications SET $key = '$value' WHERE id = $id";
        } else if ($action == 'delete') {
            $query2 = "DELETE FROM notifications WHERE id = $id";
        }
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