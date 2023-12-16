<?php
include("php/connection.php");
session_start();
error_reporting(E_ALL ^ E_WARNING);

include("includes/util.php");

$query = "SELECT portals.*, JSON_EXTRACT(portals.settings, '$.status') AS status, JSON_EXTRACT(portals.settings, '$.participantsTabVisibility') AS participants_tab_visibility, JSON_EXTRACT(portals.settings, '$.commentsTabVisibility') AS comments_tab_visibility, users.name AS host_name FROM portals JOIN users ON users.username = portals.host_id";
if ($_REQUEST['search'] != "") {
    $query .= " WHERE portals.name like '%" . $_REQUEST['search'] . "%' or users.name like '%" . $_REQUEST['search'] . "%'";
}
if ($_REQUEST['type'] != "") {
    if ($_REQUEST['search'] != "") {
        $query .= " and ";
    } else {
        $query .= " WHERE ";
    }
    $query .= "portals.type = '" . $_REQUEST['type'] . "'";
}
if ($_REQUEST['mode'] != "") {
    if ($_REQUEST['search'] != "" or $_REQUEST['type'] != "") {
        $query .= " and ";
    } else {
        $query .= " WHERE ";
    }
    $query .= "portals.mode = '" . $_REQUEST['mode'] . "'";
}

// echo $query;
$query .= " ORDER BY created_date DESC";
$data = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Hoster.com</title>
    <link rel="stylesheet" href="static/css/bootstrap.min.css">
    <link rel="stylesheet" href="static/css/style.css">
    <link rel="stylesheet" href="static/css/navbar.css">
    <script src="static/js/bootstrap.bundle.min.js"></script>
    <script src="static/js/jquery_3.7.0.min.js"></script>
</head>

<body>
    <?php include("includes/navbar.php") ?>
    <div class="col-12 bg-dark text-white py-5">
        <div class="container d-flex flex-wrap py-4">
            <div class="col-12 col-md-6 d-flex flex-column justify-content-center align-items-center">
                <div class="text-info fs-1 text-center ">Find & Join <span class="text-warning ">Portals</span></div>
                <div class="fs-5 text-center">
                    Join a Contest, Tournament,<br>Participate in an Event or<br>Apply for a Job.
                </div>
                <div class="badge bg-white fs-4 text-dark my-3">Choice is yours!</div>
            </div>
            <div
                class="col-12 col-md-6 d-flex flex-column justify-content-center align-items-center border-star border-light">
                <div class="text-primary fs-1 text-center ">Host or Create <span class="text-danger ">Portals</span>
                </div>
                <div class="fs-5 text-center">
                    Create a Contest, Tournament,<br>Host an Event or<br>Create a Job Post.
                </div>
                <div class="badge bg-white fs-4 text-dark my-3">Create or Host Portals!</div>
            </div>
        </div>
    </div>
    <div class="col-12 py-5">
        <div class="container">
            <div class="col-12 d-flex overflow-auto row flex-row h-100 py-3 justify-content-center p-md-4 m-0">
                <div class="fs-1 fw-bold mb-3 text-center">Join Portals</div>
                <form action="/#searchForm" method="get" id="searchForm"
                    class="fw-bold m-0 mb-5 row gap-2 justify-content-center">
                    <div class="form-control p-0 ps-2" style="width:fit-content;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" height="1.6em" class="text-secondary">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                        <input type="search" name="search" class="px-2 bg-transparent" placeholder="Search Portals..."
                            value="<?php echo $_REQUEST['search']; ?>">
                    </div>
                    <select name="type" class="form-select shadow-none text-capitalize" style="width:fit-content;">
                        <option value="" selected>Type</option>
                        <?php
                        foreach ($portals as $key => $value) {
                            echo '<option value="' . $key . '">' . $value['name'] . '</option>';
                        }
                        ?>
                    </select>
                    <?php if ($_REQUEST['type'] != ""):
                        echo "<script>$('#searchForm [name=type] option[value=" . $_REQUEST['type'] . "]').attr('selected', 'selected');</script>"; endif; ?>
                    <select name="mode" class="form-select shadow-none" style="width:fit-content;">
                        <option value="" selected>Mode</option>
                        <option value="online">Online</option>
                        <option value="offline">Offline</option>
                    </select>
                    <?php if ($_REQUEST['mode'] != ""):
                        echo "<script>$('#searchForm [name=mode] option[value=" . $_REQUEST['mode'] . "]').attr('selected', 'selected');</script>"; endif; ?>
                    <button type="submit" class="btn btn-primary" style="width:fit-content;">Search</button>
                </form>
                <?php if ($data->num_rows > 0):
                    while ($row = $data->fetch_assoc()): ?>
                        <div class="col-lg-6 col-xl-4 col-12 mb-3 mb-md-4">
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
                                        <span
                                            class="alert alert-info p-1 mb-0 d-inline-block border-2 shadow-none text-capitalize">
                                            <span class="badge bg-info m-0">STATUS</span>
                                            <?php if ($row['status'] == '"0"'):
                                                echo 'Open';
                                            else:
                                                echo 'Closed';
                                            endif; ?>
                                        </span>
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
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-muted fs-2 text-center">Portals Not Found!</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="static/js/main.js"></script>
    <script>
        var copyPortalLink = (btn, portalId) => {
            var link = location.origin;
            link += (location.pathname).replace(location.pathname.split("/").at(-1), ("/portal.php?id=" + portalId));
            link = link.slice(0, -1);
            // console.log(link);
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