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

$query = "SELECT title, author, isbn FROM books";
$result = mysqli_query($conn, $query);
?>

<div class="container mt-4">
    <div class="row">
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            // Encode spaces properly to match filenames
            $imagePath = "../../assets/img/books/" . rawurlencode($row['title']) . ".jpg";
            ?>
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <img src="<?= $imagePath ?>" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>" 
                         onerror="this.src='../../assets/img/books/default.jpg'">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<?php
include("./includes/footer.php");
?>