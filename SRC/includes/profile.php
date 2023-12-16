<?php session_start(); ?>
<div class="profile_container">
    <div class="title">Edit Profile</div>
    <div class="profile_view">
        <div class="input display_name">
            <form action="/php/authUpdate.php?change=name" method="post">
                <label for="form_name">Display Name</label>
                <input type="text" name="name" id="form_name" value="<?php echo $_SESSION['name']; ?>" required>
                <button type="submit">Save</button>
            </form>
        </div>
        <div class="input email">
            <form action="/php/authUpdate.php?change=email" method="post">
                <label for="form_email">Email Address</label>
                <input type="email" name="email" id="form_email" value="<?php echo $_SESSION['email']; ?>" required>
                <button type="submit">Update</button>
            </form>
        </div>
        <div class="input username">
            <form action="/php/authUpdate.php?change=username" method="post">
                <label for="form_email">Change Username</label>
                <input type="text" name="username" id="form_username" value="<?php echo $_SESSION['username']; ?>"
                    required>
                <button type="submit">Set</button>
            </form>
        </div>
        <div class="input password">
            <form action="/php/authUpdate.php?change=password" method="post">
                <label for="form_current_password">Change Password</label>
                <input type="password" name="current_password" id="form_current_password" placeholder="Current Password"
                    required>
                <input type="password" name="new_password" id="form_new_password" placeholder="New Password" required>
                <button type="submit">Change</button>
            </form>
        </div>
    </div>
</div>

<script>
    $('.profile_container .profile_view form').on("submit", (e) => {
        // e.preventDefault()
        $(e.target).children("button[type=submit]").addClass('loading-btn')
    })
</script>