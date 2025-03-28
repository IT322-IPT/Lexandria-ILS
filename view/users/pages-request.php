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
include("./includes/sidebar-pages-request.php");

if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
            ' . $_SESSION['success_message'] . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['success_message']);
  }

// $UserID = $_SESSION['authUser']['userId'];
$FullName = $_SESSION['authUser']['fullName']; 
$Email = $_SESSION['authUser']['email'];
?>
<div style="align-items: center; display: flex; justify-content: center; margin-top: 10px;">
    <h1>Can't find a book you're looking for? Let us know!</h1>
</div>

<div class="card" style="margin-top: 40px;">
    <div class="card-body">
        <h5 class="card-title">Suggest a book</h5>
        <!-- Book Request Form -->
        <form class="row g-3" method="POST" action="add_book_request.php">
        <div class="col-md-12">
                <input type="text" class="form-control" name="fullName" placeholder="Your Name" required>
            </div>
            <div class="col-md-6">
                <input type="email" class="form-control" name="email" placeholder="Your Email" required>
            </div>
            <div class="col-12">
                <input type="text" class="form-control" name="book_title" placeholder="Book Title" required>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="author" placeholder="Author" required>
            </div>
            <div class="col-md-6">
                <input type="text" class="form-control" name="isbn" placeholder="ISBN (if available)">
            </div>
            <div class="col-12">
                <textarea class="form-control" name="reason" rows="3" placeholder="Why do you need this book?" required></textarea>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit Request</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
        <!-- End Book Request Form -->

    </div>
</div>

<?php
include("./includes/footer.php");
?>