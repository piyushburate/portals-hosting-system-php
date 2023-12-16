<?php
include("connection.php");
session_start();

$portal_name = $_REQUEST['portal_name'];
$portal_id = $_REQUEST['portal_id'];
$host_id = $_REQUEST['host_id'];
$max_reach = $_REQUEST['max_reach'];
$start_date = date("Y-m-d H:i", strtotime($_REQUEST['start_date']));
$end_date = date("Y-m-d H:i", strtotime($_REQUEST['end_date']));
$portal_desc = $_REQUEST['portal_desc'];

if($host_id != $_SESSION['username']){
    echo "<script> alert('An internal error occurred!'); history.back();</script>";
    exit(0);
}

$query = "SELECT portal_id FROM portals WHERE portal_id = '$portal_id'";
$data = $conn->query($query);
$total = mysqli_num_rows($data);
if ($total == 1) {
    $query2 = "UPDATE portals SET name = '$portal_name', max_reach = $max_reach, start_date = '$start_date', end_date = '$end_date', portal_desc = '$portal_desc' WHERE portal_id = '$portal_id'";
    $data2 = $conn->query($query2);
    if ($data2) {
        // echo "<script> alert('Portal updated successfully!'); history.back();</script>";
        echo "<script>history.back();</script>";
        // header("Location: manage-portal.php?id=$portal_id");
    } else {
        echo "<script> alert('An internal error occurred!'); history.back();</script>";
    }
} else {
    echo "<script> alert('An internal error occurred!'); history.back();</script>";
}

?>