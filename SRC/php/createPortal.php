<?php
include("connection.php");

$portal_name = $_REQUEST['portal_name'];
$host_id = $_REQUEST['host_id'];
$portal_mode = $_REQUEST['portal_mode'];
$max_reach = $_REQUEST['max_reach'];
$portal_type = $_REQUEST['portal_type'];
$start_date = date("Y-m-d H:i", strtotime($_REQUEST['start_date']));
$end_date = date("Y-m-d H:i", strtotime($_REQUEST['end_date']));
$portal_desc = $_REQUEST['portal_desc'];

$portal_id = uniqid();

$query = "SELECT portal_id FROM portals WHERE portal_id = '$portal_id'";
$data = $conn->query($query);
if ($data) {
    $total = mysqli_num_rows($data);
    if ($total == 0) {
        $query2 = "INSERT INTO portals (portal_id, host_id, name, type, mode, start_date, end_date, max_reach, portal_desc, status) VALUES ('$portal_id', '$host_id', '$portal_name', '$portal_type', '$portal_mode', '$start_date', '$end_date', $max_reach, '$portal_desc', 'none')";
        $data2 = $conn->query($query2);
        if ($data2) {
            echo "<script> history.back();</script>";
        } else {
            echo "<script> alert('An internal error occurred!'); history.back();</script>";
        }
    } else {
        echo "<script> alert('An internal error occurred!'); history.back();</script>";
    }
} else {
    echo "<script> alert('An internal error occurred!'); history.back();</script>";
}

?>