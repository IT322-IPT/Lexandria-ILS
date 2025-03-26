<?php
session_start();
session_unset();
session_destroy();

// Prevent browser from using cached session
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: ../../../IT322/login.php");
exit();
?>
<script>
  window.history.pushState(null, "", window.location.href);
  window.onpopstate = function() {
    window.location.replace("login.php");
  };
</script>