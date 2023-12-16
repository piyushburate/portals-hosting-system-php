<?php
include("connection.php");
include("../includes/util.php");

// echo $_REQUEST['participant_username'] . "<br>";
// echo $_REQUEST['participant_details'] . "<br>";
// echo $_REQUEST['portal_id'] . "<br>";

$participant_username = $_REQUEST['participant_username'];
$participant_details = $_REQUEST['participant_details'];
$portal_id = $_REQUEST['portal_id'];
$status = 1;

$query = "SELECT portal_id FROM portals WHERE portal_id = '$portal_id'";
$data = $conn->query($query);
// Checks if portal exists
if (mysqli_num_rows($data) == 1) {
    $query = "SELECT username FROM users WHERE username = '$participant_username'";
    $data = $conn->query($query);
    // Checks if user exists
    if (mysqli_num_rows($data) == 1) {
        $query = "SELECT username, portal_id FROM participants WHERE username = '$participant_username' and portal_id = '$portal_id'";
        $data = $conn->query($query);
        // Checks if user not already joined the portal
        if (mysqli_num_rows($data) < 1) {
            $query = "INSERT INTO participants (username, portal_id, user_details, status) VALUES ('$participant_username', '$portal_id', '$participant_details', $status)";
            $data = $conn->query($query);
            if ($data) {
                // echo "<script> alert('Portal Joining successful!');</script>";
                echo "<script> history.back();</script>";
                // header("Location: /portal.php?id=$portal_id");
            } else {
                echo "<script> alert('An internal error occurred!'); history.back();</script>";
            }
        } else if (mysqli_num_rows($data) == 1) {
            $query2 = "UPDATE participants SET status = $status, user_details = '$participant_details' WHERE portal_id = '$portal_id' and username = '$participant_username'";
            $data2 = $conn->query($query2);
            if ($data2) {
                // echo "<script> alert('Participant updated successfully!'); history.back();</script>";
                echo "<script> history.back();</script>";
            } else {
                echo "<script> alert('An internal error occurred!'); history.back();</script>";
            }
        } else {
            echo "<script> alert('An internal error occurred!'); history.back();</script>";
        }
    } else {
        echo "<script> alert('User Not Found!'); history.back();</script>";
    }
} else {
    echo "<script> alert('Portal Not Found!'); history.back();</script>";
}

?>