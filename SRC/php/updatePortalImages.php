<?php
include("connection.php");
session_start();

$portal_id = $_REQUEST['portal_id'];
$host_id = $_REQUEST['host_id'];
$action = $_REQUEST['action'];

if ($host_id != $_SESSION['username']) {
    echo "<script>alert('An internal error occurred!'); location.href = '/manage-portal.php?id=$portal_id&tab=description';</script>";
    exit(0);
}

$target_dir = "../images/";
if ($action == 'add' or $action == 'replace') {
    $images = $_FILES[$action . '_image'];

    $target_file = $target_dir . basename($images["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (isset($_POST["submit"])) {
        $check = getimagesize($images["tmp_name"]);
        if ($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            $error = "File is not an image.";
            $uploadOk = 0;
        }
    }

    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
        $error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 0) {
        echo "<script> alert('$error'); location.href = '/manage-portal.php?id=$portal_id&tab=description'; </script>";
        exit(0);
    }
}

$query = "SELECT portal_id, images FROM portals WHERE portal_id = '$portal_id'";
$data = $conn->query($query);
$total = mysqli_num_rows($data);
if ($total == 1) {
    while ($row = $data->fetch_assoc()) {
        $db_images = json_decode($row['images'], true);
    }
    if ($action == "add") {
        $target_file = $portal_id . date("-Y-m-d-H-i-s") . '.jpg';
        if (move_uploaded_file($images["tmp_name"], $target_dir . $target_file)) {
            array_push($db_images, $target_file);
        } else {
            exit(0);
        }
    }
    if ($action == 'move_up') {
        $index = $_REQUEST['index'];
        $temp = $db_images[$index];
        $db_images[$index] = $db_images[$index - 1];
        $db_images[$index - 1] = $temp;
    }
    if ($action == 'move_down') {
        $index = $_REQUEST['index'];
        $temp = $db_images[$index];
        $db_images[$index] = $db_images[$index + 1];
        $db_images[$index + 1] = $temp;
    }
    if ($action == 'replace') {
        $index = $_REQUEST['index'];
        $target_file = $portal_id . date("-Y-m-d-H-i-s") . '.jpg';
        if (move_uploaded_file($images["tmp_name"], $target_dir . $target_file)) {
            unlink($target_dir . $db_images[$index]);
            $db_images[$index] = $target_file;
        } else {
            exit(0);
        }
    }
    if ($action == 'delete') {
        $index = $_REQUEST['index'];
        if (unlink($target_dir . $db_images[$index])) {
            unset($db_images[$index]);
            $db_images = array_values($db_images);
        } else {
            exit(0);
        }
    }
    $db_images = json_encode($db_images, true);
    $query2 = "UPDATE portals SET images = '$db_images' WHERE portal_id = '$portal_id'";
    $data2 = $conn->query($query2);
    if ($data2) {
        echo "<script>location.href = '/manage-portal.php?id=$portal_id&tab=description';</script>";
    } else {
        echo "<script> alert('An internal error occurred!'); location.href = '/manage-portal.php?id=$portal_id&tab=description';</script>";
    }
} else {
    echo "<script> alert('An internal error occurred!'); location.href = '/manage-portal.php?id=$portal_id&tab=description';</script>";
}

?>