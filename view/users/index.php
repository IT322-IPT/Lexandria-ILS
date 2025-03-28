<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
</head>
<?php
session_start();
// $_SESSION['message'] = "Successfully logged in";
// $_SESSION['code'] = "success";

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
    $sql = "SELECT * FROM books WHERE title LIKE '%$searchQuery%' OR author LIKE '%$searchQuery%' OR isbn LIKE '%$searchQuery%' OR genre LIKE '%$searchQuery%'" ;
} else {
    $sql = "SELECT * FROM books";
}

$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>
<div style="text-align: center; margin-bottom: 70px; top: 0;">
    <img src="../../assets/img/lexandria-dako-transparent.png" alt="Site Logo" style="max-width: 7%">
</div>
<body>

    <div class="container mt-4">

        <div class="search-bar d-flex justify-content-center mb-5">
            <form class="search-form d-flex w-100" method="POST" action="" 
                style="width: 100%; max-width: 100%; background: #f8f9fa; border-radius: 30px; padding: 5px; 
                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);">
                <input type="text" name="query" placeholder="Search books by title, author, genre, or ISBN..." 
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
                        <div class="card-body text-center">
                            <h5 class="card-title font-weight-bold"><?= htmlspecialchars($row['title']) ?></h5>
                            <p class="card-text"><strong>Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                            <p class="card-text"><strong>Genre:</strong> <?= htmlspecialchars($row['genre']) ?></p>
                            <p class="card-text"><strong>Page Count:</strong> <?= htmlspecialchars($row['page_count']) ?></p>
                            <p class="card-text"><strong>ISBN:</strong> <?= htmlspecialchars($row['isbn']) ?></p>
                            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#modal<?= $row['isbn'] ?>">
                                View Details
                            </button>
                        </div>
                    </div>
                </div>
            <!-- Modal -->
            <div class="modal fade" id="modal<?= $row['isbn'] ?>" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= htmlspecialchars($row['title']) ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Author:</strong> <?= htmlspecialchars($row['author']) ?></p>
                            <p><strong>Genre:</strong> <?= htmlspecialchars($row['genre']) ?></p>
                            <p><strong>Page Count:</strong> <?= htmlspecialchars($row['page_count']) ?></p>
                            <p><strong>ISBN:</strong> <?= htmlspecialchars($row['isbn']) ?></p>
                            <p><strong>Description:</strong> <?= htmlspecialchars($row['synopsis']) ?></p>
                            <p><strong>Status:</strong> 
                                <?php if ($row['status'] === 'Checked Out') : ?>
                                    <span class="badge bg-light text-dark">Checked Out</span>
                                <?php else : ?>
                                    <span class="badge bg-primary text-white">Available</span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <?php if ($row['status'] !== 'Checked Out') : ?>
                                <a href="pages-borrow.php?isbn=<?= urlencode($row['isbn']) ?>" class="btn btn-primary">Borrow</a>
                            <?php endif; ?>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php }} else { ?>
                <div class="col-12 text-center mt-4"><h5 style='color: blue;'>No book found</h5></div>
            <?php } ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php 
    include("./includes/footer.php");
    if(isset($_SESSION['message']) && $_SESSION['code'] !='') {
        ?>
        <script>
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
              toast.onmouseenter = Swal.stopTimer;
              toast.onmouseleave = Swal.resumeTimer;
            }
          });
          Toast.fire({
            icon: "<?php echo $_SESSION['code']; ?>",
            title: "<?php echo $_SESSION['message']; ?>"
          });
        </script>
        <?php
        unset($_SESSION['message']);
        unset($_SESSION['code']);
    }
    ?>
</body>