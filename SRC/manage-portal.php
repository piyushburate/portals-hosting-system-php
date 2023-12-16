<?php
include("php/connection.php");
session_start();
error_reporting(E_ALL ^ E_WARNING);

include("includes/util.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}
$username = $_SESSION['username'];
$portal_id = $_REQUEST['id'];
if ($portal_id != "") {
    $query = "SELECT portals.*, JSON_EXTRACT(portals.settings, '$.status') AS status, JSON_EXTRACT(portals.settings, '$.participantsTabVisibility') AS participants_tab_visibility, JSON_EXTRACT(portals.settings, '$.commentsTabVisibility') AS comments_tab_visibility, users.name AS host_name FROM portals JOIN users ON users.username = portals.host_id WHERE portal_id = '$portal_id'"; // ORDER BY datetime DESC";
} else {
    header("Location: /");
}
$result = $conn->query($query);
$data = null;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data = $row;
    }
    if ($data['host_id'] != $_SESSION['username']) {
        header("Location: /");
    }
} else {
    header("Location: /");
}

$images = json_decode($data['images'], true);
$list = json_decode($data['list'], true);

$joined = false;
if (isset($_SESSION['username'])) {
    $query = "SELECT username, portal_id FROM participants WHERE username = '$username' and portal_id = '$portal_id'";
    $result = $conn->query($query);
    if (mysqli_num_rows($result) != 0) {
        $joined = true;
    }
}

$query = "SELECT participants.*, users.* FROM participants JOIN users ON users.username = participants.username WHERE portal_id = '$portal_id' ORDER BY joined_date DESC";
$data2 = $conn->query($query);
$query = "SELECT * FROM notifications WHERE portal_id = '$portal_id' ORDER BY created_date DESC";
$data3 = $conn->query($query);
$query = "SELECT users.name AS name, comments.* FROM comments JOIN users ON users.username = comments.username WHERE portal_id = '$portal_id' ORDER BY created_date DESC";
$data4 = $conn->query($query);

// $data - Portal details
// $data2 - Participants list
// $data3 - Notification list
// $data4 - Comments list
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $data['name']; ?> - Manage Portal - Hoster.com
    </title>
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/navbar.css">
    <script src="static/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/jquery_3.7.0.min.js"></script>
    <link rel="stylesheet" href="static/css/manage-portal.css">
</head>

<body>

    <div id="cancel_bg"></div>
    <div id="app">
        <div class="dashboard">
            <?php include("includes/navbar.php") ?>

            <div class="sidebar">
                <form id="updatePortalForm" action="php/updatePortal.php" method="post">
                    <div class="form col-12 p-2 pb-4 overflow-x-hidden overflow-y-auto">
                        <div class="row m-0 gy-3">
                            <div class="col-12 order-first">
                                <label for="portal_name" class="form-label">Portal Name</label>
                                <input type="text" name="portal_name" class="form-control shadow-none" id="portal_name"
                                    value="<?php echo $data['name']; ?>" required>
                                <input type="hidden" name="portal_id" value="<?php echo $_REQUEST['id']; ?>">
                                <input type="hidden" name="host_id" value="<?php echo $_SESSION['username']; ?>">
                            </div>
                            <div class="col-md-6 order-0">
                                <label for="host_id" class="form-label">Hosted By</label>
                                <input type="text" class="form-control shadow-none" id="host_id"
                                    value="<?php echo $_SESSION['name']; ?>" readonly required>
                            </div>
                            <div class="col-md-6 order-3 order-lg-1">
                                <label for="max_reach" class="form-label">Max Reach</label>
                                <input type="number" name="max_reach" class="form-control shadow-none" id="max_reach"
                                    min="1" value="<?php echo $data['max_reach']; ?>" required>
                            </div>
                            <div class="col-md-7 order-4 order-lg-2">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="datetime-local" name="start_date" class="form-control shadow-none"
                                    id="start_date" value="<?php echo $data['start_date']; ?>" required>
                            </div>
                            <div class="col-md-5 order-1 order-lg-3">
                                <label for="portal_mode" class="form-label">Mode</label>
                                <select id="portal_mode" class="form-select shadow-none"
                                    aria-label="Default select example" disabled required>
                                    <option value="" selected>Select</option>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                </select>
                                <?php echo "<script>$('#portal_mode option[value=" . $data['mode'] . "]').attr('selected', 'selected');</script>"; ?>
                            </div>
                            <div class="col-md-7 order-5 order-lg-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="datetime-local" name="end_date" class="form-control shadow-none"
                                    id="end_date" value="<?php echo $data['end_date']; ?>" required>
                            </div>
                            <div class="col-md-5 order-2 order-lg-5">
                                <label for="portal_type" class="form-label">Portal Type</label>
                                <select id="portal_type" class="form-select shadow-none text-capitalize"
                                    aria-label="Default select example" disabled required>
                                    <option value="" selected>Select</option>
                                    <?php
                                    foreach ($portals as $key => $value) {
                                        echo '<option value="' . $key . '">' . $value['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                                <?php echo "<script>$('#portal_type option[value=" . $data['type'] . "]').attr('selected', 'selected');</script>"; ?>
                            </div>
                            <div class="col-12 order-last">
                                <label for="portal_desc" class="form-label">About / Description</label>
                                <textarea name="portal_desc" class="form-control shadow-none text-justify"
                                    id="portal_desc" rows="8" placeholder="Max Character Length is 1000."
                                    required><?php echo $data['portal_desc']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="save_changes">
                        <button type="submit" id="save_btn">Save Changes</button>
                    </div>
                </form>
            </div>
            <div id="main" class="main">
                <style>
                    .nav-link,
                    .nav-link:hover,
                    .nav-link.active {
                        color: #212529;
                    }
                </style>
                <div class="overflow-auto">
                    <ul id="page-tabs" class="nav nav-tabs pt-3 px-4 flex-nowrap"
                        style="width:100%;min-width: max-content;" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="descriptionTab" data-bs-toggle="tab"
                                data-bs-target="#descriptionSection" type="button" role="tab"
                                aria-controls="descriptionSection" aria-selected="true">Description</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="participantsTab" data-bs-toggle="tab"
                                data-bs-target="#participantsSection" type="button" role="tab"
                                aria-controls="participantsSection" aria-selected="false">Participants</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="notificationsTab" data-bs-toggle="tab"
                                data-bs-target="#notificationsSection" type="button" role="tab"
                                aria-controls="notificationsSection" aria-selected="false">Notifications</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="commentsTab" data-bs-toggle="tab"
                                data-bs-target="#commentsSection" type="button" role="tab"
                                aria-controls="commentsSection" aria-selected="false">Comments</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="settingsTab" data-bs-toggle="tab"
                                data-bs-target="#settingsSection" type="button" role="tab"
                                aria-controls="settingsSection" aria-selected="false">Settings</button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content d-flex flex-column" id="tabSections">
                    <div id="descriptionSection" class="tab-pane fade show active" role="tabpanel"
                        aria-labelledby="descriptionTab">
                        <div class="row col-12 px-1 px-md-3 py-4 m-0">

                            <div class="col-12 col-md-6">
                                <div class="col-12">
                                    <div class="title h3 mb-2">About</div>
                                    <div class="text-justify">
                                        <?php echo nl2br(htmlentities($data["portal_desc"])); ?>
                                    </div>
                                </div>
                                <div class="mt-4 mb-3">
                                    <div class="title h3 my-2">
                                        <?php if($data['type'] == 0 or $data['type'] == 1):echo 'Requirements'; else: echo 'Rules'; endif; ?>
                                    </div>
                                    <form id="listUploadForm" action="php/updatePortalList.php" method="post"
                                        class="d-flex gap-2 p-0 mb-3">
                                        <input type="text" name="add_text" class="form-control shadow-none"
                                            placeholder="Type..." required>
                                        <input type="hidden" name="portal_id" value="<?php echo $portal_id; ?>"
                                            required>
                                        <input type="hidden" name="host_id" value="<?php echo $data['host_id']; ?>"
                                            required>
                                        <input type="hidden" name="action" value="add" required>
                                        <input type="submit" value="Add" class="form-control shadow-none col">
                                    </form>

                                    <ol class="list-group list-group-numbered">
                                    <?php foreach ($list as $item): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-">
                                            <div class="text-justify col px-2"><?php echo nl2br(htmlentities($item)); ?></div>
                                            <span class="btn btn-sm btn-light border"
                                                style="height: fit-content;" onclick="deleteListItem(this, <?php echo array_search($item, $list); ?>)">&#x2715;</span>
                                        </li>
                                    <?php endforeach; ?>
                                    </ol>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 px-4">
                                <div id="imageSlider" class="carousel slide mb-3 rounded-3 ratio-16x9 overflow-hidden"
                                    data-bs-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php foreach ($images as $image): ?>
                                            <div class="carousel-item ratio ratio-16x9 <?php if (array_search($image, $images) == 0):
                                                echo 'active';
                                            endif; ?>">
                                                <img src="images/<?php echo $image; ?>" class="d-block w-100"
                                                    style="object-fit: cover;" loading="lazy">
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <button class="carousel-control-prev" type="button" data-bs-target="#imageSlider"
                                        data-bs-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Previous</span>
                                    </button>
                                    <button class="carousel-control-next" type="button" data-bs-target="#imageSlider"
                                        data-bs-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="visually-hidden">Next</span>
                                    </button>
                                </div>

                                <label for="imagesUploadForm" class="mb-2 fw-bold">Add Images To Slider</label>
                                <form id="imagesUploadForm" action="php/updatePortalImages.php" method="post"
                                    enctype="multipart/form-data" class="d-flex gap-2 p-0 mb-1">
                                    <input type="file" name="add_image" accept="image/jpg,image/jpeg,image/png"
                                        class="form-control shadow-none">
                                    <input type="file" name="replace_image" accept="image/jpg,image/jpeg,image/png"
                                        class="d-none">
                                    <input type="hidden" name="portal_id" value="<?php echo $portal_id; ?>" required>
                                    <input type="hidden" name="host_id" value="<?php echo $data['host_id']; ?>"
                                        required>
                                    <input type="hidden" name="action" value="add" required>
                                    <input type="submit" value="Add" class="form-control shadow-none col">
                                </form>
                                <span class="text-danger fs-6">* Only JPG, JPEG & PNG image types are allowed.</span>

                                <div class="row gap-2 m-0 mt-3">

                                    <?php foreach ($images as $image): ?>
                                        <div class="col-12 form-control d-flex align-items-center">
                                            <img src="images/<?php echo $image; ?>" alt=""
                                                class="rounded border shadow-sm ratio ratio-16x9"
                                                style="width:160px;height:90px;object-fit:cover;" loading="lazy">
                                            <div class="flex-fill"></div>
                                            <?php if (array_search($image, $images) != 0): ?>
                                                <span height="2em" class="btn btn-outline-success ms-2"
                                                    onclick="moveUp(<?php echo array_search($image, $images); ?>)">&#129033;</span>
                                            <?php endif; ?>
                                            <?php if (array_search($image, $images) != sizeof($images) - 1): ?>
                                                <span height="2em" class="btn btn-outline-success ms-2"
                                                    onclick="moveDown(<?php echo array_search($image, $images); ?>)">&#129035;</span>
                                            <?php endif; ?>
                                            <span height="2em" class="btn btn-outline-primary ms-2 fw-bolder"
                                                onclick="replaceImage(<?php echo array_search($image, $images); ?>)">&#11119;</span>
                                            <span height="2em" class="btn btn-outline-danger ms-2 fw-bolder"
                                                onclick="deleteImage(<?php echo array_search($image, $images); ?>)">&#x2715;</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>


                            </div>
                        </div>

                    </div>
                    <div id="participantsSection" class="tab-pane fade" role="tabpanel"
                        aria-labelledby="participantsTab">
                        <div class="row flex-column m-0 col-12 px-1 px-md-3 py-4">

                            <div class="py-2 text-muted">Participants Evaluation</div>
                            <div class="col-12 row gx-3">
                                <div class="col-12 col-sm-6 col-md-4">
                                    <div class="alert alert-light border border-2 p-3 rounded-2 col-12">
                                        <span class="h5 fw-bold">Joined To Portals
                                        </span>
                                        <div class="mb-1">
                                            <?php echo mysqli_num_rows($data2) . " out of " . $data['max_reach']; ?>
                                        </div>
                                        <div class="progress bg-lightgrey col-12" style="height:10px;">
                                            <div class="progress-bar bg-secondary" role="progressbar"
                                                style="width: <?php echo mysqli_num_rows($data2) * 100 / intval($data['max_reach']) . '%'; ?>;"
                                                aria-valuenow="<?php echo mysqli_num_rows($data2); ?>" aria-valuemin="0"
                                                aria-valuemax="<?php echo $data["max_reach"]; ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php foreach ($portals[$data['type']]["user_status"] as $key => $value): ?>
                                    <?php
                                    $evquery = "SELECT COUNT(status) AS count FROM `participants` WHERE portal_id = '$portal_id' and status = '$key';";
                                    $evresult = mysqli_query($conn, $evquery);
                                    $evdata = $evresult->fetch_assoc();
                                    if ($evdata['count'] == 0):
                                        continue;
                                    endif;
                                    ?>
                                    <div class="col-12 col-sm-6 col-md-4">
                                        <div
                                            class="alert alert-<?php echo userStatusColor($data['type'], $key); ?> p-3 rounded-2 col-12">
                                            <span class="h5 fw-bold text-capitalize">Participants
                                                <?php echo $value[0]; ?>
                                            </span>
                                            <div class="mb-1">
                                                <?php echo $evdata['count'] . " out of " . $data2->num_rows; ?>
                                            </div>
                                            <div class="progress bg-lightgrey col-12" style="height:10px;">
                                                <div class="progress-bar bg-<?php echo userStatusColor($data['type'], $key); ?>"
                                                    role="progressbar"
                                                    style="width: <?php echo $evdata['count'] * 100 / intval($data2->num_rows) . '%'; ?>;"
                                                    aria-valuenow="<?php echo $evdata['count']; ?>" aria-valuemin="0"
                                                    aria-valuemax="<?php echo $data["max_reach"]; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="col-12">
                                <table class="table table-hover border border-secondary caption-top">
                                    <caption>Participants List</caption>
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th scope="col" class="text-center px-3">#</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Username</th>
                                            <th scope="col">Joined Datetime</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $n = mysqli_num_rows($data2);
                                        if ($n > 0) {
                                            while ($row = $data2->fetch_assoc()) {
                                                ?>
                                                <tr class="<?php if ($username == $row['username']) {
                                                    echo 'table-active';
                                                } ?>">
                                                    <th scope="row" class="text-center">
                                                        <?php echo $n--; ?>
                                                    </th>
                                                    <td class="text-capitalize text-nowrap">
                                                        <?php echo $row['name'] ?>
                                                    </td>
                                                    <td>
                                                        <?php echo $row['username'] ?>
                                                    </td>
                                                    <td class="text-uppercase text-nowrap">
                                                        <?php echo date("d M, Y (h:i a)", strtotime($row['joined_date'])); ?>
                                                    </td>
                                                    <td>
                                                        <div class="dropdown" data-status="<?php echo $row['username']; ?>">
                                                            <?php if ($row['status'] != "0"): ?>
                                                                <button
                                                                    class="badge bg-<?php echo userStatusColor($data["type"], $row["status"]); ?> fs-6 dropdown-toggle text-capitalize"
                                                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                    <?php echo userStatus($data["type"], $row['status']); ?>
                                                                </button>
                                                                <ul class="dropdown-menu p-0" style="min-width: fit-content;">
                                                                    <?php
                                                                    foreach ($portals[$data['type']]['user_status'] as $key => $value) {
                                                                        if ($key == $row['status'] or $key == 0) {
                                                                            continue;
                                                                        }
                                                                        ?>
                                                                        <li>
                                                                            <span
                                                                                class="dropdown-item border-bottom text-capitalize text-center cursor-pointer"
                                                                                onclick="changeParticipantStatus('<?php echo $row['username']; ?>', '<?php echo $key; ?>')">
                                                                                <?php echo $value[0]; ?>
                                                                            </span>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                            <?php else: ?>
                                                                <button class="badge bg-danger fs-6 text-capitalize" type="button">
                                                                    <?php echo userStatus($data["type"], $row['status']); ?>
                                                                </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                    <div id="notificationsSection" class="tab-pane fade" role="tabpanel"
                        aria-labelledby="notificationsTab">
                        <div class="row m-0 col-12 px-1 px-md-3 py-4">
                            <div class="col-12 col-md-5">
                                <form id="sendNotificationForm" action="php/sendNotification.php" method="post">
                                    <div class="mb-3">
                                        <label for="notification_msg" class="form-label text-muted shadow-none">New
                                            Notification</label>
                                        <input class="form-control text-justify shadow-none" id="notification_title"
                                            name="title" placeholder="Title" required />
                                        <input type="hidden" name="portal_id" value="<?php echo $portal_id; ?>">
                                        <input type="hidden" name="host_id" value="<?php echo $data['host_id']; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <textarea class="form-control text-justify shadow-none" id="notification_msg"
                                            name="message" rows="6" placeholder="Write Message..." required></textarea>
                                    </div>
                                    <div class="mb-3 d-flex align-items-center justify-content-center">
                                        <select class="form-select shadow-none" name="visibility" placeholder="sladmas"
                                            required>
                                            <option selected value="">Select Visibility</option>
                                            <option value="0">Public</option>
                                            <option value="1">Participants Only</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary shadow-none col-12">Send
                                            Notification</button>
                                    </div>
                                </form>
                            </div>
                            <div class="col-12 col-md-7">
                                <div class="notifications">
                                    <?php
                                    if ($data3->num_rows > 0): ?>
                                        <div class="mb-2">Notifications</div>
                                        <?php while ($row = $data3->fetch_assoc()): ?>
                                            <div class="alert alert-light shadow border text-dark text-justify" role="alert"
                                                data-notification-id="<?php echo $row['id'] ?>">
                                                <div class="d-flex justify-content-end align-items-center">
                                                    <select class="form-select shadow-none w-auto resizable-select py-0 me-2"
                                                        onchange="updateNotification(this, <?php echo $row['id']; ?>)"
                                                        name="visibility" style="font-size:.875rem;" required>
                                                        <option value="0">Public</option>
                                                        <option value="1">Participants Only</option>
                                                    </select>
                                                    <?php echo "<script>$('[data-notification-id=" . $row['id'] . "] [name=visibility] option[value=" . $row['visibility'] . "]').attr('selected', 'selected');</script>"; ?>
                                                    <span class="btn btn-sm btn-outline-danger py-0"
                                                        onclick="deleteNotification(this, <?php echo $row['id']; ?>)">Delete</span>
                                                </div>
                                                <div class="mt-1 h6 fw-bold">
                                                    <?php echo htmlentities($row['title']); ?>
                                                </div>
                                                <div class="mt-2 h6 text-secondary">
                                                    <?php echo nl2br(htmlentities($row['msg'])); ?>
                                                </div>
                                                <div class="badge text-muted fw-bolder p-0">
                                                    <?php echo getAvgElapsedTime($row['created_date']); ?>
                                                </div>
                                            </div>
                                            <?php
                                        endwhile;
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="commentsSection" class="tab-pane fade" role="tabpanel" aria-labelledby="commentsTab">
                        <div class="row m-0 col-12 col-md-8 px-3 px-md-4 py-4">
                            <div class="make_comment">
                                <form action="php/postComment.php" method="post"
                                    class="row d-flex align-items-start alert alert-light shadow border text-dark">
                                    <button type="button" class="btn btn-secondary col-auto rounded-circle fs-3 p-0"
                                        style="width:50px;height:50px;">
                                        <?php
                                        $name_split = explode(' ', $_SESSION['name'], 2);
                                        foreach ($name_split as $value) {
                                            echo substr($value, 0, 1);
                                        }
                                        ?>
                                    </button>
                                    <div class="col mx-2">
                                        <div class="mb-1 fs-5 fw-normal">Comment as
                                            <?php echo $_SESSION['name']; ?>
                                        </div>
                                        <textarea name="message" id="" class="form-control shadow-none" rows="4"
                                            maxlength="500" required></textarea>
                                        <input type="hidden" name="portal_id" value="<?php echo $_REQUEST['id']; ?>">
                                        <input type="hidden" name="username"
                                            value="<?php echo $_SESSION['username']; ?>">
                                        <button type="submit" class="btn btn-primary mt-2 shadow-none">Post
                                            Comment</button>
                                        <button type="reset" class="btn btn-secondary mt-2 shadow-none">Reset</button>
                                    </div>
                                </form>
                            </div>
                            <div class="comments p-0">
                                <?php
                                if ($data4->num_rows > 0): ?>
                                    <?php while ($row = $data4->fetch_assoc()): ?>
                                        <div class="alert alert-light shadow border text-dark text-justify" role="alert"
                                            data-comment-id="<?php echo $row['id'] ?>">
                                            <div class="d-flex justify-content-end">
                                                <span class="btn btn-sm btn-outline-danger py-0"
                                                    onclick="deleteComment(this, <?php echo $row['id']; ?>)">Delete</span>
                                            </div>
                                            <div class="mt-1 h6 fw-bold d-flex align-items-center">
                                                <button type="button"
                                                    class="btn btn-secondary rounded-circle d-flex justify-content-center align-items-center fs-4"
                                                    style="width:50px;height:50px;">
                                                    <?php
                                                    $name_split = explode(' ', $_SESSION['name'], 2);
                                                    foreach ($name_split as $value) {
                                                        echo substr($value, 0, 1);
                                                    }
                                                    ?>
                                                </button>
                                                <span class="d-flex flex-column mx-3">
                                                    <span class="my-1 fw-normal">
                                                        Comment By
                                                        <?php echo $row['name']; ?>
                                                    </span>
                                                    <span class="badge text-muted fw-bolder text-start p-0">
                                                        <?php echo getAvgElapsedTime($row['created_date']); ?>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="mt-2 h6 text-secondary">
                                                <?php echo $row['msg']; ?>
                                            </div>
                                        </div>
                                        <?php
                                    endwhile;
                                endif;
                                ?>
                            </div>
                        </div>
                    </div>
                    <div id="settingsSection" class="tab-pane fade" role="tabpanel" aria-labelledby="settingsTab">
                        <div class="row m-0 col-12 px-1 px-md-3 py-4">
                            <div class="row col-12 col-md-7 col-lg-12 col-xl-7">
                                <div class="d-flex align-items-center justify-content-between col-12 mb-3">
                                    <div class="me-2 text-nowrap">Portal Status</div>
                                    <select name="portal_status" class="form-select shadow-none"
                                        aria-label="Default select example" style="width:fit-content;"
                                        onchange="updatePortalSettings('status', this.value)" required>
                                        <option value="0">Open</option>
                                        <option value="1">Closed</option>
                                    </select>
                                    <?php echo "<script>$('[name=portal_status] option[value=" . $data['status'] . "]').attr('selected', 'selected');</script>"; ?>
                                </div>
                                <div class="d-flex align-items-center justify-content-between col-12 mb-3">
                                    <div class="me-2 text-nowrap">Participants Tab Visibility</div>
                                    <select name="participants_tab_visibility" class="form-select shadow-none"
                                        aria-label="Default select example" style="width:fit-content;"
                                        onchange="updatePortalSettings('participantsTabVisibility', this.value)"
                                        required>
                                        <option value="0">Public</option>
                                        <option value="1">Participants Only</option>
                                        <option value="2">Host Only</option>
                                    </select>
                                    <?php echo "<script>$('[name=participants_tab_visibility] option[value=" . $data['participants_tab_visibility'] . "]').attr('selected', 'selected');</script>"; ?>
                                </div>
                                <div class="d-flex align-items-center justify-content-between col-12 mb-3">
                                    <div class="me-2 text-nowrap">Comments Tab Visibility</div>
                                    <select name="comments_tab_visibility" class="form-select shadow-none"
                                        aria-label="Default select example" style="width:fit-content;"
                                        onchange="updatePortalSettings('commentsTabVisibility', this.value)" required>
                                        <option value="0">Public</option>
                                        <option value="1">Participants Only</option>
                                    </select>
                                    <?php echo "<script>$('[name=comments_tab_visibility] option[value=" . $data['comments_tab_visibility'] . "]').attr('selected', 'selected');</script>"; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="static/js/main.js"></script>
    <script>
        $(".sidebar_toggle").click(() => {
            $(".sidebar").addClass("show")
            $("#cancel_bg").css("display", "block")
            $("#cancel_bg").click(() => {
                $(".sidebar").removeClass("show")
                $("#cancel_bg").css("display", "none")
                $("#cancel_bg").click(null)
            })
        })

        $("#updatePortalForm").on("submit", async (e) => {
            btnLoad($("#save_btn"), true);
        })
    </script>
    <?php
    if ($_REQUEST['tab'] != "") {
        echo "<script>$('#" . $_REQUEST['tab'] . "Tab').trigger('click');</script>";
    }
    ?>
    <script>
        var tabList = document.querySelectorAll("#page-tabs .nav-link");
        tabList.forEach(tab => {
            $(tab).on("click", () => {
                var tabName = ($(tab).attr("id")).replace("Tab", "");
                pushLink("manage-portal.php?id=<?php echo $_REQUEST['id']; ?>&tab=" + tabName)
            });
        });

        var portal_type = '<?php echo $data['type']; ?>';
        var changeParticipantStatus = async (username, status) => {
            var url = "php/updateParticipant.php";
            url += "?username=" + username;
            url += "&portal_id=<?php echo $_REQUEST['id']; ?>";
            url += "&host_id=<?php echo $data['host_id']; ?>";
            url += "&key=status";
            url += "&value=" + status;
            url += "&method=api";

            var response = await makeRequest(url);
            console.log(response);
            if (response == "success") {
                $(".dropdown[data-status=" + username + "]").find("button").html(userStatus(portal_type, status));
                $(".dropdown[data-status=" + username + "]").find("button").attr("class", "badge fs-6 dropdown-toggle text-capitalize");
                $(".dropdown[data-status=" + username + "]").find("button").addClass("bg-" + userStatusColor(portal_type, status));
                var temp = $(".dropdown[data-status=" + username + "]").find("ul li").get(0).cloneNode(true);
                $(".dropdown[data-status=" + username + "]").find("ul").html("");
                Object.entries(portals[<?php echo $data['type'] ?>]['user_status']).forEach(([key, value]) => {
                    // console.log(value[0]);
                    if (key == 0 || key == status) { return }
                    var li = $(temp).clone(true);
                    $(li).find("span").text(value[0]);
                    $(li).find("span").attr("onclick", "changeParticipantStatus('" + username + "', '" + key + "')")
                    $(".dropdown[data-status=" + username + "]").find("ul").append(li);
                });
                $(".dropdown[data-status=" + username + "]").find("ul");
            } else {
                $(".dropdown[data-status=" + username + "]").find("button").html("Error");
                $(".dropdown[data-status=" + username + "]").find("button").attr("class", "badge fs-6 text-capitalize");
                $(".dropdown[data-status=" + username + "]").find("button").addClass("bg-danger");
                $(".dropdown[data-status=" + username + "]").find("ul").html("");
            }
        }

        var deleteNotification = async (btn, id) => {
            if (confirm("Delete Notification ?") == false) { return; }
            var url = "php/updateNotification.php";
            url += "?id=" + id;
            url += "&host_id=<?php echo $data['host_id']; ?>";
            url += "&method=api";
            url += "&action=delete";

            var response = await makeRequest(url);
            if (response == 'success') {
                $('[data-notification-id=' + id + ']').hide();
            } else {
                $(this).text("Error");
                console.log(response);
            }
        }
        var updateNotification = async (btn, id) => {
            if (confirm("Update Notification ?") == false) { return; }
            var url = "php/updateNotification.php";
            url += "?id=" + id;
            url += "&host_id=<?php echo $data['host_id']; ?>";
            url += "&key=visibility";
            url += "&value=" + $(btn).val();
            url += "&method=api";
            url += "&action=update";

            var response = await makeRequest(url);
            if (response == 'success') {
            } else {
                $(this).text("Error");
                console.log(response);
            }
        }
        var deleteComment = async (btn, id) => {
            if (confirm("Delete Comment ?") == false) { return; }
            var url = "php/updateComment.php";
            url += "?id=" + id;
            url += "&username=<?php echo $data['username']; ?>";
            url += "&host_id=<?php echo $_SESSION['username']; ?>";
            url += "&method=api";
            url += "&action=delete";

            var response = await makeRequest(url);
            if (response == 'success') {
                $('[data-comment-id=' + id + ']').hide();
            } else {
                $(this).text("Error");
                console.log(response);
            }
        }

        var updatePortalSettings = async (key, value) => {
            if (confirm("Update Portal Setting ?") == false) { return; }
            var url = "php/updatePortalSettings.php";
            url += "?portal_id=<?php echo $portal_id; ?>";
            url += "&host_id=<?php echo $_SESSION['username']; ?>";
            url += "&key=" + key;
            url += "&value=" + value;
            url += "&method=api";

            var response = await makeRequest(url);
            if (response == 'success') {
            } else {
                location.reload()
                console.log(response);
            }
        }

        var replaceImage = (n) => {
            $('#imagesUploadForm [name=replace_image]').trigger("click");
            document.querySelector('#imagesUploadForm [name=replace_image]').onchange = (e) => {
                if (e.target.files.length <= 1 && $('#imagesUploadForm [name=action]')) {
                    $('#imagesUploadForm [name=action]').val("replace");
                    var indexInput = $("<input>", { "type": "hidden", "name": "index", "value": n });
                    $('#imagesUploadForm').prepend(indexInput);
                    $('#imagesUploadForm').trigger("submit");
                }
                document.querySelector('#imagesUploadForm [name=replace_image]').onchange = null;
            };
        }

        var deleteImage = (n) => {
            if (confirm("Delete Slider Image ?") == true) {
                $('#imagesUploadForm [name=action]').val("delete");
                var indexInput = $("<input>", { "type": "hidden", "name": "index", "value": n });
                $('#imagesUploadForm').prepend(indexInput);
                $('#imagesUploadForm').trigger("submit");
            } else {
                return;
            }
        }
        var moveUp = (n) => {
            $('#imagesUploadForm [name=action]').val("move_up");
            var indexInput = $("<input>", { "type": "hidden", "name": "index", "value": n });
            $('#imagesUploadForm').prepend(indexInput);
            $('#imagesUploadForm').trigger("submit");
        }
        var moveDown = (n) => {
            $('#imagesUploadForm [name=action]').val("move_down");
            var indexInput = $("<input>", { "type": "hidden", "name": "index", "value": n });
            $('#imagesUploadForm').prepend(indexInput);
            $('#imagesUploadForm').trigger("submit");
        }

        var deleteListItem = async (btn, n) => {
            if (confirm("Delete List Item ?") == false) { return; }
            var url = "php/updatePortalList.php";
            url += "?portal_id=<?php echo $portal_id; ?>";
            url += "&host_id=<?php echo $data['host_id']; ?>";
            url += "&action=delete";
            url += "&index=" + n;

            var response = await makeRequest(url);
            if (response == 'success') {
                location.reload();
            } else {
                console.log(response);
            }
        }

        </script>
</body>

</html>