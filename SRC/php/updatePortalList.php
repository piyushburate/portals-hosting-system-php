<?php
include("connection.php");
session_start();

$portal_id = $_REQUEST['portal_id'];
$host_id = $_REQUEST['host_id'];
$action = $_REQUEST['action'];
if($action== 'add'){
    $txt = $_REQUEST['add_text'];
}

if ($host_id != $_SESSION['username']) {
    if($action == 'delete'){
        echo 'falied';
        exit(0);
    }
    echo "<script>alert('An internal error occurred!'); history.back();</script>";
    exit(0);
}

$query = "SELECT portal_id, list FROM portals WHERE portal_id = '$portal_id'";
$data = $conn->query($query);
$total = mysqli_num_rows($data);
if ($total == 1) {
    while ($row = $data->fetch_assoc()) {
        $db_list = json_decode($row['list'], true);
    }
    if ($action == "add") {
        array_push($db_list, $txt);
    }

    if ($action == "delete") {
        unset($db_list[$_REQUEST['index']]);
        $db_list = array_values($db_list);
    }

    $db_list = json_encode($db_list, true);
    $query2 = "UPDATE portals SET list = '$db_list' WHERE portal_id = '$portal_id'";
    $data2 = $conn->query($query2);
    if ($data2) {
        if ($action == "add") {
            echo "<script>location.href = '/manage-portal.php?id=$portal_id&tab=description';</script>";
        }
        if ($action == "delete") {
            echo "success";
        }
    } else {
        echo "<script> alert('An internal error occurred!'); history.back();</script>";
    }
} else {
    echo "<script> alert('An internal error occurred!'); history.back();</script>";
}

?>