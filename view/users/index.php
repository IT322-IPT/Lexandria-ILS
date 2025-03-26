<?php
session_start();
if (!isset($_SESSION["authUser"])) {
  header("Location: ../../../IT322/login.php");
  exit();
}
// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
include("./includes/sidebar.php");
?>

<?php
include("./includes/footer.php");
?>