<?php
include("php/connection.php");
session_start();
error_reporting(E_ALL ^ E_WARNING);

include("includes/util.php");

$username = $_SESSION['username'];
$portal_id = $_REQUEST['id'];
if ($portal_id != "") {
    $query = "SELECT portals.*, JSON_EXTRACT(portals.settings, '$.status') AS status, JSON_EXTRACT(portals.settings, '$.participantsTabVisibility') AS participants_tab_visibility, JSON_EXTRACT(portals.settings, '$.commentsTabVisibility') AS comments_tab_visibility, users.name AS host_name FROM portals JOIN users ON users.username = portals.host_id WHERE portal_id = '$portal_id'";
} else {
    header("Location: /");
}
$result = $conn->query($query);
$data = null;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data = $row;
    }
} else {
    header("Location: /");
}

$images = json_decode($data['images'], true);
$list = json_decode($data['list'], true);

$joined = false;
if (isset($_SESSION['username'])) {
    $query = "SELECT username, portal_id, status FROM participants WHERE username = '$username' and portal_id = '$portal_id'";
    $result = $conn->query($query);
    if (mysqli_num_rows($result) == 1) {
        $joined = true;
        $data3 = $result->fetch_assoc();
    }
}

$query = "SELECT participants.*, users.* FROM participants JOIN users ON users.username = participants.username WHERE portal_id = '$portal_id' ORDER BY joined_date DESC";
$data2 = $conn->query($query);
$query = "SELECT * FROM notifications WHERE portal_id = '$portal_id'";
if ($joined == false):
    $query .= " AND visibility = 0 ORDER BY created_date DESC";
else:
    $query .= " ORDER BY created_date DESC";
endif;
$data4 = $conn->query($query);
$query = "SELECT users.name AS name, comments.* FROM comments JOIN users ON users.username = comments.username WHERE portal_id = '$portal_id' ORDER BY created_date DESC";
$data5 = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo $data["name"]; ?> - Hoster.com
    </title>
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/navbar.css">
    <script src="static/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/jquery_3.7.0.min.js"></script>
</head>

<body>
    <div id="app">

        <?php include("includes/navbar.php"); ?>

        <div class="bg-white h-100 w-100">
            <div class="header row m-0 col-12 bg-dark text-light d-flex pt-5 pb-4 px-1 px-md-3 pb-md-0">
                <div class="col-12 col-md-6 mb-4">
                    <div class="title h3 fw-bolder text-light">
                        <?php echo $data["name"]; ?>
                    </div>
                    <div class="d-flex flex-wrap mb-1">
                        <span class="badge bg-secondary mb-2 me-2 fs-6 fw-normal">Hosted By
                            <?php echo $data["host_name"]; ?>
                        </span>
                        <span class="badge bg-primary text-capitalize me-2 mb-2 fs-6 fw-normal">
                            <?php echo $portals[$data['type']]['name']; ?> Portal
                        </span>
                        <span class="badge bg-info text-capitalize me-2 mb-2 fs-6 fw-normal">
                            <?php echo $data['mode']; ?> Mode
                        </span>
                    </div>
                    <div class=" d-flex flex-wrap">
                        <span class="alert alert-warning p-1 mb-2 text-uppercase fw-bold me-2">
                            <div class="badge bg-warning">From</div>
                            <?php echo date("d M, Y (h:i a)", strtotime($data['start_date'])); ?>
                        </span>
                        <span class="alert alert-danger p-1 mb-2 text-uppercase fw-bold">
                            <div class="badge bg-danger">&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;</div>
                            <?php echo date("d M, Y (h:i a)", strtotime($data['end_date'])); ?>
                        </span>
                    </div>
                    <?php if ($joined): ?>

                        <?php if ($data3["status"] == "0"): ?>
                            <button id="joinPortalButton"
                                class="btn btn-outline-light border-2 my-2 shadow-none fw-bold text-capitalize"
                                data-bs-toggle="modal" data-bs-target="#joinPortalDialog">
                                <?php echo $portals[$data["type"]]['joining_txt']; ?>
                            </button>
                        <?php endif; ?>
                        <button id="joinPortalButton"
                            class="btn btn-outline-light border-2 my-2 shadow-none fw-bold text-capitalize bg-<?php echo userStatusColor($data['type'], $data3['status']); ?>">
                            <?php echo userStatus($data["type"], $data3["status"]); ?>
                        </button>
                        <?php if ($data3["status"] != "2" and $data3["status"] != "0"): ?>
                            <button class="btn btn-outline-danger border-2 fw-bold shadow-none"
                                onclick="leftPortal(this)">Exit</button>
                        <?php endif; ?>
                    <?php elseif ($data["status"] == '1'): ?>
                        <button class="btn btn-outline-light border-2 my-2 shadow-none fw-bold text-capitalize">
                            Portal Closed
                        </button>
                    <?php elseif (isset($_SESSION["username"])): ?>
                        <button id="joinPortalButton"
                            class="btn btn-outline-light border-2 my-2 shadow-none fw-bold text-capitalize"
                            data-bs-toggle="modal" data-bs-target="#joinPortalDialog">
                            <?php echo $portals[$data["type"]]['joining_txt']; ?>
                        </button>
                    <?php else: ?>
                        <button class="btn btn-outline-light border-2 my-2 shadow-none fw-bold text-capitalize"
                            onclick="goTo('login.php')">
                            <?php echo $portals[$data["type"]]['joining_txt']; ?>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-md-6">
                    <div class="title h5 text-warning">More Details</div>
                    <div class="col-auto">
                        <div class="alert alert-warning d-inline-block p-1 my-1 text-uppercase fw-bold">
                            <div class="badge bg-warning fs-6 m-0">
                                <?php echo mysqli_num_rows($data2); ?>
                            </div>
                            participants joined
                        </div><br>
                        <div class="alert alert-success d-inline-block p-1 my-1 text-uppercase fw-bold">
                            <div class="badge bg-success">Status</div>
                            <?php if ($data['status'] == '0'):
                                echo 'Open';
                            else:
                                echo 'Closed';
                            endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <style>
                .nav-link {
                    color: white;
                }

                .nav-link.active {
                    color: #212529;
                }

                .nav-link:hover {
                    color: white;
                }
            </style>
            <ul id="page-tabs" class="nav nav-tabs pt-3 border-bottom-0 px-4 bg-dark flex-nowrap"
                style="overflow-x: auto;overflow-y: hidden;" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="descriptionTab" data-bs-toggle="tab"
                        data-bs-target="#descriptionSection" type="button" role="tab" aria-controls="descriptionSection"
                        aria-selected="true">Description</button>
                </li>
                <?php if ($data['participants_tab_visibility'] != '2'): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="participantsTab" data-bs-toggle="tab"
                            data-bs-target="#participantsSection" type="button" role="tab"
                            aria-controls="participantsSection" aria-selected="false">Participants</button>
                    </li>
                <?php endif; ?>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="notificationsTab" data-bs-toggle="tab"
                        data-bs-target="#notificationsSection" type="button" role="tab"
                        aria-controls="notificationsSection" aria-selected="false">Notifications</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="commentsTab" data-bs-toggle="tab" data-bs-target="#commentsSection"
                        type="button" role="tab" aria-controls="commentsSection" aria-selected="false">Comments</button>
                </li>
            </ul>

            <div class="tab-content" id="tabSections">
                <div id="descriptionSection" class="tab-pane fade show active" role="tabpanel"
                    aria-labelledby="descriptionTab">
                    <div class="row col-12 px-1 px-md-3 py-4 m-0">

                        <div class="col-12 col-md-6">
                            <div class="">
                                <div class="title h3 mb-2">About</div>
                                <div class="text-justify">
                                    <?php echo nl2br(htmlentities($data["portal_desc"], ENT_QUOTES, 'UTF-8')); ?>
                                </div>
                            </div>
                            <div class="mt-4 mb-3">
                                <?php if(sizeof($list) > 0): ?>
                                <div class="title h3 my-2">
                                    <?php if ($data['type'] == 0 or $data['type'] == 1):
                                        echo 'Requirements'; else:
                                        echo 'Rules'; endif; ?>
                                </div>
                                <?php endif; ?>
                                <ol class="list-group list-group-numbered">
                                    <?php foreach ($list as $item): ?>
                                        <li class="list-group-item">
                                            <?php echo nl2br(htmlentities($item)); ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>

                            </div>
                        </div>
                        <div class="col-12 col-md-6 px-4">
                            <div id="carouselExampleControls"
                                class="carousel slide mb-3 rounded-3 ratio-16x9 overflow-hidden"
                                data-bs-ride="carousel">
                                <div class="carousel-inner">
                                    <?php foreach ($images as $image): ?>
                                        <div class="carousel-item ratio ratio-16x9 <?php if (array_search($image, $images) == 0):
                                            echo 'active';
                                        endif; ?>">
                                            <img src="images/<?php echo $image; ?>" class="d-block w-100"
                                                style="object-fit:cover;" loading="lazy">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button class="carousel-control-prev" type="button"
                                    data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button"
                                    data-bs-target="#carouselExampleControls" data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <?php if ($data['participants_tab_visibility'] != '2'): ?>
                    <div id="participantsSection" class="tab-pane fade" role="tabpanel" aria-labelledby="participantsTab">
                        <?php if ($data['participants_tab_visibility'] == '1' and $joined == false): ?>
                            <div class="fs-5 px-1 px-md-3 py-4 m-3">Only Participants can view.</div>
                        <?php else: ?>
                            <div class="row m-0 col-12 px-1 px-md-3 py-4 overflow-auto">
                                <div class="col-12 col-md-4">
                                    <div class="py-2 text-muted">Participants Evaluation</div>
                                    <div class="alert alert-light border border-2 p-3 rounded-2 col-12 mb-3">
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
                                    <?php foreach ($portals[$data['type']]["user_status"] as $key => $value): ?>
                                        <?php
                                        $evquery = "SELECT COUNT(status) AS count FROM `participants` WHERE portal_id = '$portal_id' and status = '$key';";
                                        $evresult = mysqli_query($conn, $evquery);
                                        $evdata = $evresult->fetch_assoc();
                                        if ($evdata['count'] == 0):
                                            continue;
                                        endif;
                                        ?>
                                        <div
                                            class="alert alert-<?php echo userStatusColor($data['type'], $key); ?> p-3 rounded-2 col-12 mb-3">
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
                                    <?php endforeach; ?>
                                </div>
                                <div class="col-12 col-md-8" style="overflow-x:auto;">
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
                                                        <td class="text-capitalize">
                                                            <span
                                                                class="badge bg-<?php echo userStatusColor($data["type"], $row["status"]); ?> fs-6">
                                                                <?php echo userStatus($data["type"], $row['status']); ?>
                                                            </span>
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
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div id="notificationsSection" class="tab-pane fade" role="tabpanel" aria-labelledby="notificationsTab">
                    <div class="row m-0 col-12 col-md-6 px-3 px-md-4 py-4">
                        <div class="notifications">
                            <?php
                            if ($data4->num_rows > 0): ?>
                                <div class="mb-2 text-muted">Notifications</div>
                                <?php while ($row = $data4->fetch_assoc()): ?>
                                    <div class="alert alert-light shadow border text-dark text-justify" role="alert"
                                        data-notification-id="<?php echo $row['id'] ?>">
                                        <div class="mt-1 h6 fw-bold">
                                            <?php echo htmlentities($row['title']); ?>
                                        </div>
                                        <div class="mt-2 h6 text-secondary four-line-text">
                                            <?php echo htmlentities($row['msg']); ?>
                                        </div>
                                        <div class="badge text-muted fw-bolder p-0">
                                            <?php echo getAvgElapsedTime($row['created_date']); ?>
                                        </div>
                                    </div>
                                    <?php
                                endwhile;
                            else:
                                echo '<div class="h3 text-muted my-4">Notifications Not Found!</div>';
                            endif;
                            ?>
                        </div>
                    </div>
                </div>
                <div id="commentsSection" class="tab-pane fade" role="tabpanel" aria-labelledby="commentsTab">
                    <div class="row m-0 col-12 col-lg-10 px-1 px-md-3 py-4">
                        <?php if ($data['comments_tab_visibility'] == '1' and $joined == false): ?>
                            <div class="fs-5 mx-1 mb-3">Only Participants can comment.</div>
                        <?php elseif (isset($_SESSION['username'])): ?>
                            <div class="post_comment col-12 col-md-5 px-3 d-flex">
                                <form action="php/postComment.php" method="post"
                                    class="row d-flex align-items-start alert alert-light col-12 shadow border text-dark"
                                    style="height:fit-content;">
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
                                        <div class="mb-1 fs-5 fw-bold">Comment as
                                            <?php echo $_SESSION['name']; ?>
                                        </div>
                                        <textarea name="message" id="" class="form-control shadow-none" rows="4"
                                            maxlength="500" required></textarea>
                                        <input type="hidden" name="portal_id" value="<?php echo $_REQUEST['id']; ?>">
                                        <input type="hidden" name="username" value="<?php echo $_SESSION['username']; ?>">
                                        <button type="submit" class="btn btn-primary mt-2 shadow-none">Post Comment</button>
                                        <button type="reset" class="btn btn-secondary mt-2 shadow-none">Reset</button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="fs-5 mx-1 mb-3"><a href="login.php" class="fw-bold text-primary">Log in</a> to post
                                a comment. </div>
                        <?php endif; ?>
                        <div class="comments col-12 col-md-7 p-0 px-3">
                            <?php
                            if ($data5->num_rows > 0): ?>
                                <?php while ($row = $data5->fetch_assoc()): ?>
                                    <div class="alert alert-light shadow border text-dark text-justify" role="alert">
                                        <div class="mt-1 h6 fw-bold d-flex align-items-center">
                                            <button type="button"
                                                class="btn btn-secondary rounded-circle d-flex justify-content-center align-items-center fs-4"
                                                style="width:50px;height:50px;">
                                                <?php
                                                $name_split = explode(' ', $row['name'], 2);
                                                foreach ($name_split as $value) {
                                                    echo substr($value, 0, 1);
                                                }
                                                ?>
                                            </button>
                                            <span class="d-flex flex-column mx-3">
                                                <span class="my-1 fw-medium">
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
            </div>
        </div>

    </div>

    <form id="joinPortalForm" action="php/joinPortal.php" method="post">
        <div class="modal fade" id="joinPortalDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Join Portal</h5>
                        <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="participant_name" class="form-label">Participant Name</label>
                                <input type="text" class="form-control shadow-none" id="participant_name"
                                    value="<?php echo $_SESSION['name'] ?>" readonly required>
                            </div>
                            <div class="col-12">
                                <label for="participant_username" class="form-label">Participant Username</label>
                                <input type="text" name="participant_username" class="form-control shadow-none"
                                    id="participant_username" value="<?php echo $_SESSION['username'] ?>" readonly
                                    required>
                            </div>
                            <div class="col-12">
                                <label for="participant_email" class="form-label">Participant Email</label>
                                <input type="text" class="form-control shadow-none" id="participant_email"
                                    value="<?php echo $_SESSION['email'] ?>" readonly required>
                            </div>
                            <div class="col-12">
                                <label for="participant_details" class="form-label">Participant Details (Add Extra
                                    Deatils)</label>
                                <textarea name="participant_details" class="form-control shadow-none"
                                    id="participant_details" rows="5"
                                    placeholder="Max Character Length is 1000."></textarea>
                            </div>
                            <input type="hidden" name="portal_id" value="<?php echo $_REQUEST['id']; ?>">
                            <input type="hidden" name="portal_type" value="<?php echo $data['type']; ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary shadow-none"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="joinPortalSubmit" class="btn btn-primary shadow-none">Create
                            Portal</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <script src="static/js/main.js"></script>
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
                pushLink("portal.php?id=<?php echo $_REQUEST['id']; ?>&tab=" + tabName)
            });
        });

        $("#joinPortalDialog .modal-title").text($("#joinPortalButton").text())
        $("#joinPortalSubmit").text($("#joinPortalButton").text())

        var leftPortal = async (btn) => {
            var url = "php/updateParticipant.php";
            url += "?username=<?php echo $_SESSION['username']; ?>";
            url += "&portal_id=<?php echo $_REQUEST['id']; ?>";
            url += "&host_id=<?php echo $data['host_id']; ?>";
            url += "&key=status";
            url += "&value=0";
            url += "&method=api";
            var response = await makeRequest(url);
            if (response == "success") {
                location.reload();
            } else {
                console.log(response);
                $(btn).text("Error");
                $(btn).removeAttr("onclick");
            }
        }
    </script>
</body>

</html>