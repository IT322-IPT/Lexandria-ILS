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
      <a class="nav-link collapsed" href="manage-books.php">
        <i class="bi bi-journals"></i>  <!-- originally bi-person -->
        <span>Manage Books</span>
      </a>
    </li><!-- End Manage Books (previously Profile Page) Nav -->

    <li class="nav-item">
      <a class="nav-link" href="manage-users.php">
        <i class="bi bi-people"></i> <!-- originally bi-question-circle -->
        <span>Manage Users</span>
      </a>
    </li><!-- End Manage Users (previously F.A.Q Page) Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="requests.php">
        <i class="bi bi-envelope"></i>
        <span>Requests</span>
      </a>
    </li><!-- End Requests (previously Contact Page) Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="borrowed-books.php">
        <i class="bi bi-book-half"></i> <!-- originally bi-card-list -->
        <span>Books on Loan</span>
      </a>
    </li><!-- End Manage All Borrowed Books (previously Register Page) Nav -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="acquisition-requests.php">
        <i class="bi bi-box-arrow-in-right"></i> <!-- originally bi-card-list -->
        <span>Acquisition Requests</span>
        </a>
      </li><!-- End Manage All Borrowed Books (previously Register Page) Nav -->
  </ul>
</aside>
<!-- End Sidebar -->
   
  <section class="section">
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Users List</h5>
          <p>This table displays all registered users.</p>

          <!-- Table with stripped rows -->
          <table class="table datatable" text-center>
            <thead>
              <tr>
                <th>#</th>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Gender</th>
                <th>Birthday</th>
                <th>Account Created</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $query = "SELECT userId, firstName, lastName, email, phoneNumber, gender, birthday, createdAt FROM users ORDER BY createdAt DESC";
              if ($result = mysqli_query($conn, $query)) {
                  $count = 1;
                  while ($row = mysqli_fetch_assoc($result)) {
                      echo "<tr>";
                      echo "<td>" . $count . "</td>";
                      echo "<td>" . htmlspecialchars($row['userId']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['firstName']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['lastName']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['email']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['phoneNumber']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['gender']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['birthday']) . "</td>";
                      echo "<td>" . htmlspecialchars($row['createdAt']) . "</td>";
                      echo "</tr>";
                      $count++;
                  }
              } else {
                  echo "<tr><td colspan='8' class='text-center text-danger'>Error fetching users data.</td></tr>";
              }
              mysqli_close($conn);
              ?>
            </tbody>
          </table>
          <!-- End Table with stripped rows -->
        </div>
      </div>
    </div>
  </div>
</section>

<?php include("./includes/footer.php"); ?>
