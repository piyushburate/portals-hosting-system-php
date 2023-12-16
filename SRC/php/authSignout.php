<?php
session_start();
session_destroy();
// header("Location: /");
echo "<script>history.back();</script>";
?>