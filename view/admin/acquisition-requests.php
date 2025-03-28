<?php
session_start();
if (!isset($_SESSION["authUser"])) {
  header("Location: ../../../IT322/login.php");
  exit();
}

// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
include("./includes/sidebar.php");

// Fetch book requests from the database
$query = "SELECT * FROM book_requests ORDER BY request_date DESC";
$result = mysqli_query($conn, $query);
?>

<aside id="sidebar" class="sidebar">
  <ul class="sidebar-nav" id="sidebar-nav">
    <li class="nav-item">
      <a class="nav-link collapsed" href="index.php">
        <i class="bi bi-grid"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="manage-books.php">
        <i class="bi bi-journals"></i>
        <span>Manage Books</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="manage-users.php">
        <i class="bi bi-people"></i>
        <span>Manage Users</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="requests.php">
        <i class="bi bi-envelope"></i>
        <span>Requests</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link collapsed" href="borrowed-books.php">
        <i class="bi bi-book-half"></i>
        <span>Books on Loan</span>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="acquisition-requests.php">
      <i class="bi bi-box-arrow-in-right"></i>
      <span>Acquisition Requests</span>
      </a>
    </li>
  </ul>
</aside>

<div class="card">
  <div class="card-body">
    <h5 class="card-title">Book Acquisition Requests</h5>

    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Book Title</th>
          <th scope="col">Author</th>
          <th scope="col">ISBN</th>
          <th scope="col">Request Date</th>
          <th scope="col">Requester Name</th>
          <th scope="col">Email</th>
          <th scope="col">Reason</th>
        </tr>
      </thead>
      <tbody>
        <?php if (mysqli_num_rows($result) > 0) {
          $count = 1;
          while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
              <th scope="row"><?= $count++; ?></th>
              <td><?= htmlspecialchars($row['book_title']); ?></td>
              <td><?= htmlspecialchars($row['author']); ?></td>
              <td><?= htmlspecialchars($row['isbn'] ?? 'N/A'); ?></td>
              <td><?= htmlspecialchars(date("F j, Y, g:i A", strtotime($row['request_date']))); ?></td>
              <td><?= htmlspecialchars($row['name']); ?></td>
              <td><?= htmlspecialchars($row['email']); ?></td>
              <td><?= htmlspecialchars($row['reason']); ?></td>
            </tr>
          <?php } 
        } else { ?>
          <tr>
            <td colspan="8" class="text-center">No book acquisition requests.</td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
</div>

<?php
include("./includes/footer.php");
?>
