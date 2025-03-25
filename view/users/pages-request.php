<?php
session_start();
include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
include("./includes/sidebar.php");

if (isset($_SESSION['success_message'])) {
  echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
  unset($_SESSION['success_message']);
}

// $UserID = $_SESSION['authUser']['userId'];
$FullName = $_SESSION['authUser']['fullName']; 
$Email = $_SESSION['authUser']['email'];
?>

<!-- ======= Sidebar ======= -->
<aside id="sidebar" class="sidebar">

  <ul class="sidebar-nav" id="sidebar-nav">

    <li class="nav-item">
      <a class="nav-link collapsed" href="index.php">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li><!-- End Dashboard Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="pages-borrow.php">
        <i class="bi bi-book"></i> <!-- originally bi bi-question-circle -->
        <span>Borrow</span>
      </a>
    </li><!-- End Borrow (previously F.A.Q) Page Nav -->

    <!-- <li class="nav-item">
      <a class="nav-link collapsed" href="pages-return.html">
        <i class="bi bi-box-arrow-in-left"></i> originally bi bi-envelope
        <span>Return</span>
      </a>
    </li>-->
    <!-- End Return (previously Contact) Page Nav -->

    <li class="nav-item">
      <a class="nav-link" href="pages-request.php">
        <i class="bi bi-patch-question-fill"></i> <!-- originally bi bi-person -->
        <span>Missing a book?</span>
      </a>
    </li><!-- End Request (previously Profile Page) Nav -->

  </ul>

</aside><!-- End Sidebar-->


<h1>Can't find a book you're looking for?</h1>
<h2>Let us know!</h2>

<div class="card">
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
