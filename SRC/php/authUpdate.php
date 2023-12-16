<?php
session_start();
if (!isset($_SESSION['username'])) {
    echo "<script>goTo('/login');</script>";
    exit();
}

include("connection.php");
$uid = $_SESSION['uid'];
$change = $_REQUEST['change'];

if ($change == 'name' || $change == 'email') {
    $update = $_POST[$change];
    $query = "UPDATE users SET $change = '$update' WHERE uid = $uid";
    $data = $conn->query($query);
    if ($data) {
        $_SESSION[$change] = $update;
        // echo "<script>alert('$change updated successfully!'); location.href = '/dashboard.php?menu=profile'</script>";
        header("Location: /dashboard.php?menu=profile");
    } else {
        echo "<script> alert('An internal error occurred!'); location.href = '/dashboard.php?menu=profile'</script>";
    }
} else if ($change == 'username') {
    $new_username = $_POST['username'];
    $query = "SELECT username FROM users WHERE username = BINARY '$new_username'";
    $data = $conn->query($query);
    if ($data) {
        $total = mysqli_num_rows($data);
        if ($total == 0) {
            $query2 = "UPDATE users SET username = '$new_username' WHERE uid = $uid";
            $data2 = $conn->query($query2);
            if ($data2) {
                $_SESSION['username'] = $new_username;
                // echo "<script>alert('Username changed successfully!'); location.href = '/dashboard.php?menu=profile'</script>";
                header("Location: /dashboard.php?menu=profile");
            } else {
                echo "<script> alert('An internal error occurred!'); location.href = '/dashboard.php?menu=profile'</script>";
            }
        } else {
            echo "<script> alert('Username already taken!'); location.href = '/dashboard.php?menu=profile'</script>";
        }
    } else {
        echo "<script> alert('An internal error occurred!'); location.href = '/dashboard.php?menu=profile'</script>";
    }
} else if($change == 'password'){
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $query = "SELECT password FROM users WHERE uid = $uid";
    $data = $conn->query($query);
    if ($data) {
        $password = $data->fetch_assoc()['password'];
        if($password == $current_password){
            $query2 = "UPDATE users SET password = '$new_password' WHERE uid = $uid";
            $data2 = $conn->query($query2);
            if ($data) {
                // echo "<script>alert('Password changed successfully!'); location.href = '/dashboard.php?menu=profile'</script>";
                header("Location: /dashboard.php?menu=profile");
            } else {
                echo "<script> alert('An internal error occurred!'); location.href = '/dashboard.php?menu=profile'</script>";
            }
        } else {
            echo "<script> alert('Incorrect password!'); location.href = '/dashboard.php?menu=profile'</script>";
        }
    } else {
        echo "<script> alert('An internal error occurred!'); location.href = '/dashboard.php?menu=profile'</script>";
    }
}

?>