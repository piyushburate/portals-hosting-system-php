<?php
include("php/connection.php");
session_start();
error_reporting(E_ALL ^ E_WARNING);

include("includes/util.php");

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
}

$username = $_SESSION['username'];
$host = false;
switch ($_REQUEST["menu"]) {
    case 'created':
        $query = "SELECT portals.*, users.name AS host_name FROM portals JOIN users ON users.username = portals.host_id WHERE host_id = '$username' ORDER BY created_date DESC";
        $result = $conn->query($query);
        break;
    case 'joined':
        $query = "SELECT portals.*, users.name AS host_name, participants.status AS participant_status FROM participants JOIN portals ON portals.portal_id = participants.portal_id JOIN users ON users.username = portals.host_id WHERE participants.username = '$username' ORDER BY participants.joined_date DESC";
        $result = $conn->query($query);
        break;
    case 'profile':
        $result = null;
        break;
    default:
        header("Location: dashboard.php?menu=created");
        break;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Hoster.com</title>
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/navbar.css">
    <link rel="stylesheet" href="static/css/profile.css">
    <script src="static/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/jquery_3.7.0.min.js"></script>
    <link rel="stylesheet" href="static/css/dashboard.css">
</head>

<body>

    <div id="cancel_bg"></div>
    <div id="app">
        <div class="dashboard">
            <?php include("includes/navbar.php") ?>

            <div class="sidebar">
                <div class="create_new">
                    <button id="create_new_btn" data-bs-toggle="modal" data-bs-target="#createNewDialog">Create
                        Portal</button>
                </div>
                <div class="menu">
                    <div class="menuitem home" onclick="goTo('/')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>

                        <span class="title">Home</span>
                    </div>
                    <div class="menuitem created" onclick="goTo('dashboard.php?menu=created')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>

                        <span class="title">Created Portals</span>
                    </div>
                    <div class="menuitem joined" onclick="goTo('dashboard.php?menu=joined')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 3.75H6.912a2.25 2.25 0 00-2.15 1.588L2.35 13.177a2.25 2.25 0 00-.1.661V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 00-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 012.012 1.244l.256.512a2.25 2.25 0 002.013 1.244h3.218a2.25 2.25 0 002.013-1.244l.256-.512a2.25 2.25 0 012.013-1.244h3.859M12 3v8.25m0 0l-3-3m3 3l3-3" />
                        </svg>
                        <span class="title">Joined Portals</span>
                    </div>
                    <div class="menuitem profile" onclick="goTo('dashboard.php?menu=profile')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="icon">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="title">Profile</span>
                    </div>
                </div>
            </div>
            <div id="main" class="main">
                <div class="portals row h-100 p-3 p-md-4" style="overflow-y:auto;">
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            if ($_SESSION['username'] == $row["host_id"]) {
                                $host = true;
                            }
                            ?>
                            <div class="col-lg-6 col-xl-4 col-12 mb-3 mb-md-4" style="height: fit-content;">
                                <div class="card bg-light w-100 h-100 border border-2">
                                    <div class="card-header pb-0">
                                        <h5 class="card-title single-line-text fw-bold mb-2">
                                            <?php echo $row['name']; ?>
                                        </h5>
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="badge bg-primary text-capitalize me-2 mb-2">
                                                <?php echo $portals[$row['type']]['name']; ?>
                                            </span>
                                            <span class="badge bg-info text-capitalize me-2 mb-2 ">
                                                <?php echo $row['mode']; ?>
                                            </span>
                                            <span class="badge bg-secondary mb-2">Hosted By
                                                <?php echo $row['host_name']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <p class="card-text text-justify three-line-text mb-2 flex-fill">
                                            <?php echo $row['portal_desc']; ?>
                                        </p>
                                        <div class=" d-flex flex-wrap">
                                            <span class="alert alert-warning p-1 mb-2 text-uppercase fw-bold me-2">
                                                <div class="badge bg-warning">From</div>
                                                <?php echo date("d M, Y (h:i a)", strtotime($row['start_date'])); ?>
                                            </span>
                                            <span class="alert alert-danger p-1 mb-2 text-uppercase fw-bold">
                                                <div class="badge bg-danger">&nbsp;&nbsp;&nbsp;To&nbsp;&nbsp;</div>
                                                <?php echo date("d M, Y (h:i a)", strtotime($row['end_date'])); ?>
                                            </span>
                                        </div>
                                        <div class="">
                                            <?php if ($_REQUEST['menu'] == "joined"): ?>
                                                <span
                                                    class="alert alert-<?php echo userStatusColor($row['type'], $row['participant_status']); ?> p-1 mb-0 d-inline-block border-2 shadow-none text-capitalize">
                                                    <span
                                                        class="badge bg-<?php echo userStatusColor($row['type'], $row['participant_status']); ?> m-0">STATUS</span>
                                                    <?php echo userStatus($row['type'], $row['participant_status']); ?>
                                                </span>
                                            <?php endif; ?>
                                            <?php if ($_REQUEST['menu'] == "created"): ?>
                                                <span
                                                    class="alert alert-info p-1 mb-0 d-inline-block border-2 shadow-none text-capitalize">
                                                    <span class="badge bg-info m-0">STATUS</span>
                                                    <?php echo $row['status']; ?>
                                                </span>
                                            <?php endif; ?>
                                            <a class="btn btn-sm btn-outline-secondary border-2 shadow-none text-capitalize"
                                                href="portal.php?id=<?php echo $row['portal_id'] ?>" data-bs-toggle="tooltip"
                                                title="View Portal">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                    width="1.2em">
                                                    <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                                    <path fill-rule="evenodd"
                                                        d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 010-1.113zM17.25 12a5.25 5.25 0 11-10.5 0 5.25 5.25 0 0110.5 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            <a class="btn btn-sm btn-outline-success border-2 shadow-none text-capitalize <?php if (!$host) {
                                                echo 'd-none';
                                            } ?>" href="manage-portal.php?id=<?php echo $row['portal_id'] ?>"
                                                data-bs-toggle="tooltip" title="Manage Portal">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                    width="1.2em">
                                                    <path
                                                        d="M21.731 2.269a2.625 2.625 0 00-3.712 0l-1.157 1.157 3.712 3.712 1.157-1.157a2.625 2.625 0 000-3.712zM19.513 8.199l-3.712-3.712-12.15 12.15a5.25 5.25 0 00-1.32 2.214l-.8 2.685a.75.75 0 00.933.933l2.685-.8a5.25 5.25 0 002.214-1.32L19.513 8.2z" />
                                                </svg>
                                            </a>
                                            <button class="btn btn-sm btn-outline-primary border-2 shadow-none text-capitalize"
                                                onclick="copyPortalLink(this,'<?php echo $row['portal_id']; ?>')"
                                                data-bs-toggle="tooltip" title="Copy Portal Link">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                    width="1.2em">
                                                    <path fill-rule="evenodd"
                                                        d="M15.75 4.5a3 3 0 11.825 2.066l-8.421 4.679a3.002 3.002 0 010 1.51l8.421 4.679a3 3 0 11-.729 1.31l-8.421-4.678a3 3 0 110-4.132l8.421-4.679a3 3 0 01-.096-.755z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <h2 class="col-12 text-muted d-flex justify-content-center align-items-center">
                            Portals Not Found!
                        </h2>
                        <?php
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>

    <form id="createNewForm" action="php/createPortal.php" method="post">
        <div class="modal fade" id="createNewDialog" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="staticBackdropLabel">Create New Portal</h5>
                        <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12 order-first">
                                <label for="portal_name" class="form-label">Portal Name</label>
                                <input type="text" name="portal_name" class="form-control shadow-none" id="portal_name"
                                    required>
                            </div>
                            <div class="col-md-6 order-0">
                                <label for="host_id" class="form-label">Hosted By</label>
                                <input type="text" class="form-control shadow-none" id="host_id"
                                    value="<?php echo $_SESSION['name']; ?>" readonly required>
                                <input type="hidden" name="host_id" value="<?php echo $_SESSION['username']; ?>"
                                    required>
                            </div>
                            <div class="col-md-6 order-3 order-lg-1">
                                <label for="max_reach" class="form-label">Max Reach</label>
                                <input type="number" name="max_reach" class="form-control shadow-none" id="max_reach"
                                    min="1" required>
                            </div>
                            <div class="col-md-7 order-4 order-lg-2">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="datetime-local" name="start_date" class="form-control shadow-none"
                                    id="start_date" required>
                            </div>
                            <div class="col-md-5 order-1 order-lg-3">
                                <label for="portal_mode" class="form-label">Mode</label>
                                <select id="portal_mode" name="portal_mode" class="form-select shadow-none"
                                    aria-label="Default select example" required>
                                    <option value="" selected>Select</option>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                </select>
                            </div>
                            <div class="col-md-7 order-5 order-lg-4">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="datetime-local" name="end_date" class="form-control shadow-none"
                                    id="end_date" required>
                            </div>
                            <div class="col-md-5 order-2 order-lg-5">
                                <label for="portal_type" class="form-label">Portal Type</label>
                                <select id="portal_type" name="portal_type"
                                    class="form-select shadow-none text-capitalize" aria-label="Default select example"
                                    required>
                                    <option value="" selected>Select</option>
                                    <?php
                                    foreach ($portals as $key => $value) {
                                        echo '<option value="' . $key . '">' . $value['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 order-last">
                                <label for="portal_desc" class="form-label">About / Description</label>
                                <textarea name="portal_desc" class="form-control shadow-none" id="portal_desc" rows="5"
                                    placeholder="Max Character Length is 1000." required></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn btn-secondary shadow-none"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="create_new_submit" class="btn btn-primary shadow-none">Create
                            Portal</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

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

        var activeMenu = "<?php echo $_REQUEST["menu"] ?>";
        $('.menuitem.' + activeMenu).addClass("active");

        if (activeMenu == "profile") {
            $("#main").load("includes/profile.php")
        }
        $("#createNewForm").on("submit", (e) => {
            btnLoad($("#create_new_submit"), true)
        });

        var copyPortalLink = (btn, portalId) => {
            var link = location.origin;
            link += (location.pathname).replace(location.pathname.split("/").at(-1), ("portal.php?id=" + portalId));
            copyText(link);
            var temp = $(btn).html();
            $(btn).text("Link Copied!");
            setTimeout(() => {
                $(btn).html(temp);
            }, 3000)
        }
    </script>
</body>

</html>