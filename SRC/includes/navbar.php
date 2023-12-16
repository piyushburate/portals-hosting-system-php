<!-- <link rel="stylesheet" href="/static/css/navbar.css"> -->
<div class="navbar position-sticky top-0">
    <div class="sidebar_toggle">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12H12m-8.25 5.25h16.5" />
        </svg>
    </div>
    <div class="brand">
        <div class="navbar-brand cursor-pointer p-0 m-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" width="1.3em">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5M9 11.25v1.5M12 9v3.75m3-6v6" />
            </svg>
            <span class="fw-bold text-uppercase h6">Hoster.com</span>
        </div>
    </div>
    <div class="search_box">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
            class="w-6 h-6 search_icon">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <form action="/#searchForm" method="get" class="input" tabindex="99">
            <input type="search" name="search" id="search_box" placeholder="Search Portal..." autocomplete="off">
        </form>
    </div>
    <div class="profile <?php if (!isset($_SESSION['username'])) {
        echo "d-none";
    } ?>">
        <div class="profile_label">
            <span class="icon">
                <?php echo substr($_SESSION['name'], 0, 1); ?>
            </span>
            <span class="name">
                <?php echo $_SESSION['name']; ?>
            </span>
        </div>
        <div class="profile_popup" tabindex="99">
            <div class="section_one">
                <span class="icon">
                    <?php
                    $name_split = explode(' ', $_SESSION['name'], 2);
                    foreach ($name_split as $value) {
                        echo substr($value, 0, 1);
                    }
                    ?>
                </span>
                <span class="name single-line-text">
                    <?php echo $_SESSION['name']; ?>
                </span>
                <span class="email single-line-text">
                    <?php echo $_SESSION['email']; ?>
                </span>
            </div>
            <div class="section_two">
                <span class="label">Username</span>
                <span class="username">
                    <?php echo $_SESSION['username']; ?>
                </span>
                <span class="btn_edit" onclick="location.href = 'dashboard.php?menu=profile'">Edit</span>
            </div>
            <div class="section_three">
                <span class="btn_dashboard" onclick="location.href = 'dashboard.php'">Dashboard</span>
                <span class="btn_signout" onclick="location.href = 'php/authSignout.php'">Sign out</span>
            </div>
        </div>
    </div>

    <a href="login.php" class="btn btn-primary shadow-none mx-3 <?php if (isset($_SESSION['username'])) {
        echo "d-none";
    } ?>">Login</a>
</div>

<script>
    $(".navbar .search_box .search_icon").on("click", () => {
        $('.navbar .profile').removeClass('active');
        $('.navbar .search_box').toggleClass('active')
        $('.navbar .search_box .input input').trigger('focus');
    })
    $(".navbar .search_box .input input").on("blur", () => {
        $(".navbar .search_box").removeClass("active");
    })
    $(".navbar .profile .profile_label").on("click", () => {
        $('.navbar .search_box').removeClass('active')
        $('.navbar .profile').toggleClass('active');
        $('.navbar .profile .profile_popup').trigger('focus');
    })
    $(".navbar .profile .profile_popup").on("blur", () => {
        $(".navbar .profile").removeClass("active");
    })
</script>