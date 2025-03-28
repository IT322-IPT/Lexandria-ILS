<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
</head>

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

$UserID = $_SESSION['authUser']['userId'];
$FullName = $_SESSION['authUser']['fullName']; 
$Email = $_SESSION['authUser']['email'];

?>
<body>
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
          <a class="nav-link" href="pages-borrow.php">
            <i class="bi bi-book"></i> <!-- originally bi bi-question-circle -->
            <span>Borrow</span>
          </a>
        </li><!-- End Borrow (previously F.A.Q) Page Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="pages-pending-requests.php">
            <i class="bi bi-arrow-return-right"></i> <!-- originally bi bi-person -->
            <span>Pending Requests</span>
          </a>
        </li><!-- End Pending Requests (previously Profile Page) Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="pages-my-books.php">
            <i class="ri ri-book-fill"></i> <!-- originally bi bi-person -->
            <span>My Books</span>
          </a>
        </li><!-- End My Books (previously Profile Page) Nav -->

        <li class="nav-item">
          <a class="nav-link collapsed" href="pages-request.php">
            <i class="bi bi-patch-question-fill"></i> <!-- originally bi bi-person -->
            <span>Missing a book?</span>
          </a>
        </li><!-- End Request (previously Profile Page) Nav -->
      </ul>

    </aside>
  <!-- End Sidebar-->

  <main id="main" class="main">
  <?php if (isset($_SESSION['show_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-1"></i>
      Borrow request submitted successfully!
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['show_success']); ?>
  <?php endif; ?>

  <div class="card">
    <div class="card-body">
        <h5 class="card-title">Borrow a Book</h5>
        <form class="row g-3" method="POST" action="process_borrow.php">
      <input type="hidden" name="userId" value="<?php echo $UserID; ?>">
      <input type="hidden" name="isbn" value="<?= isset($_GET['isbn']) ? htmlspecialchars($_GET['isbn']) : '' ?>"> <!-- Add this line -->

      <div class="col-12">
          <label class="form-label">Full Name</label>
          <input type="text" class="form-control" name="fullName" value="<?php echo $FullName; ?>" readonly>
      </div>
      <div class="col-12">
          <label for="Email" class="form-label">Email</label>
          <input type="email" class="form-control" id="Email" name="email" value="<?php echo $Email; ?>" readonly>
      </div>
      <div class="col-12">
          <label for="ISBN" class="form-label">Select Book</label>
          <select class="form-control" id="ISBN" name="isbn" required>
              <option value="">Select a book</option>
              <?php
              include("../../dB/config.php");
              $query = "SELECT isbn, title FROM books";
              $result = mysqli_query($conn, $query);
              while ($row = mysqli_fetch_assoc($result)) {
                  $selected = (isset($_GET['isbn']) && $_GET['isbn'] == $row['isbn']) ? 'selected' : '';
                  echo "<option value='{$row['isbn']}' $selected>{$row['title']} ({$row['isbn']})</option>";
              }
              ?>
          </select>
      </div>

            <div class="col-6">
              <label for="BorrowDate" class="form-label">Borrow Date</label>
              <input type="date" class="form-control" id="BorrowDate" name="borrowDate" required>
            </div>
            <div class="col-6">
              <label for="ReturnDate" class="form-label">Return Date</label>
              <input type="date" class="form-control" id="ReturnDate" name="returnDate" readonly>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="termsCheckbox">
                <label class="form-check-label" for="termsCheckbox">
                  Agree to terms and conditions
                </label>
              </div>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Submit</button>
              <button type="reset" class="btn btn-secondary">Reset</button>
            </div>

      </form>
    </div>
  </div>

  <script>
    document.getElementById("termsCheckbox").addEventListener("change", function() {
    document.getElementById("submitBtn").disabled = !this.checked;
    });
    document.getElementById("BorrowDate").addEventListener("change", function() {
        let borrowDate = new Date(this.value);
        if (!isNaN(borrowDate.getTime())) {
            let returnDate = new Date(borrowDate);
            returnDate.setDate(borrowDate.getDate() + 14); // Add 14 days
            document.getElementById("ReturnDate").value = returnDate.toISOString().split('T')[0];
        }
    });
  </script>
  <?php
  include("./includes/footer.php");
  ?>
</body>