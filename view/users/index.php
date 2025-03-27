<?php
session_start();
if (!isset($_SESSION["authUser"])) {
  header("Location: ../../../IT322/login.php");
  exit();
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
include("./includes/sidebar.php");

$searchQuery = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['query'])) {
    $searchQuery = mysqli_real_escape_string($conn, $_POST['query']);
    $sql = "SELECT * FROM books WHERE title LIKE '%$searchQuery%' OR author LIKE '%$searchQuery%' OR isbn LIKE '%$searchQuery%'" ;
} else {
    $sql = "SELECT * FROM books";
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<div class="container mt-4">
    <div class="search-bar d-flex justify-content-center mb-5">
        <form class="search-form d-flex w-100" method="POST" action="" 
            style="width: 100%; max-width: 100%; background: #f8f9fa; border-radius: 30px; padding: 5px; 
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
            <input type="text" name="query" placeholder="Search books by title, author, or ISBN..." 
                value="<?= htmlspecialchars($searchQuery) ?>" 
                class="form-control" 
                style="border: none; outline: none; background: transparent; padding: 12px 15px; border-radius: 30px; flex: 1; font-size: 16px;">
            <button type="submit" class="btn btn-primary" 
            style="border-radius: 30px; padding: 10px 18px; display: flex; align-items: center; justify-content: center;">
                <i class="bi bi-search"></i>
            </button>
        </form>
    </div>

    <div class="row">
        <?php if (mysqli_num_rows($result) > 0) { while ($row = mysqli_fetch_assoc($result)) { ?>
            <div class="col-lg-3 mb-4">
                <div class="card">
                    <img src="../../assets/img/books/<?= rawurlencode($row['title']) ?>.jpg" class="card-img-top" alt="<?= htmlspecialchars($row['title']) ?>" onerror="this.src='../../assets/img/books/default.jpg'">
                    <div class="card-body">
                        <h5 class="card-title text-center font-weight-bold"><?= htmlspecialchars($row['title']) ?></h5>
                        <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                        <p class="card-text"><strong>Genre:</strong> <?= htmlspecialchars($row['genre']) ?></p>
                        <p class="card-text"><strong>Page Count:</strong> <?= htmlspecialchars($row['page_count']) ?></p>
                        <p class="card-text"><strong>ISBN:</strong> <?= htmlspecialchars($row['isbn']) ?></p>
                    </div>
                </div>
            </div>
        <?php }} else { ?>
            <div class="col-12 text-center mt-4"><h5 style='color: blue;'>Book not found</h5></div>
        <?php } ?>
    </div>

</div>

<?php include("./includes/footer.php"); ?>
