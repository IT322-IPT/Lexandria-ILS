<?php
include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");

$query = "SELECT * FROM borrow_requests";
$result = mysqli_query($conn, $query);
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
      <a class="nav-link collapsed" href="manage-users.php">
        <i class="bi bi-people"></i> <!-- originally bi-question-circle -->
        <span>Manage Users</span>
      </a>
    </li><!-- End Manage Users (previously F.A.Q Page) Nav -->

    <li class="nav-item">
      <a class="nav-link" href="requests.php">
        <i class="bi bi-envelope"></i>
        <span>Requests</span>
      </a>
    </li><!-- End Requests (previously Contact Page) Nav -->

    <li class="nav-item">
      <a class="nav-link collapsed" href="borrowed-books.php">
        <i class="bi bi-book-half"></i> <!-- originally bi-card-list -->
        <span>Borrowed Books</span>
      </a>
    </li><!-- End Manage All Borrowed Books (previously Register Page) Nav -->

    <li class="nav-item">
        <a class="nav-link collapsed" href="acquisition-requests.php">
          <i class="bi bi-book-half"></i> <!-- originally bi-card-list -->
          <span>Acquisition Requests</span>
        </a>
      </li><!-- End Manage All Borrowed Books (previously Register Page) Nav -->

  </ul>

</aside><!-- End Sidebar-->

<main id="main" class="main">
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Borrow Requests</h5>

    <!-- Dark Table -->
    <table class="table table-dark">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Borrower ID</th>
          <th scope="col">Full Name</th>
          <th scope="col">Email</th>
          <th scope="col">ISBN</th>
          <th scope="col">Borrow Date</th>
          <th scope="col">Return Date</th>
          <th scope="col">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        include("../../dB/config.php");
        $query = "SELECT * FROM borrow_requests ORDER BY request_id DESC";
        $result = mysqli_query($conn, $query);
        $count = 1;

        while ($row = mysqli_fetch_assoc($result)) {
          echo "<tr>
                  <th scope='row'>{$count}</th>
                  <td>{$row['user_id']}</td>                  
                  <td>{$row['full_name']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['isbn']}</td>
                  <td>{$row['borrow_date']}</td>
                  <td>{$row['return_date']}</td>
                  <td>
                    <form method='POST' action='process-borrow-action.php' style='display:inline;'>
                      <input type='hidden' name='request_id' value='{$row['request_id']}'>
                      <button type='submit' name='approve' class='btn btn-success btn-sm'>Approve</button>
                    </form>
                    <form method='POST' action='process-borrow-action.php' style='display:inline;'>
                      <input type='hidden' name='request_id' value='{$row['request_id']}'>
                      <button type='submit' name='reject' class='btn btn-danger btn-sm'>Reject</button>
                    </form>
                  </td>
                </tr>";
          $count++;
        }
        ?>
      </tbody>
    </table>
    <!-- End Dark Table -->
  </div>
</div>

<?php
include("./includes/footer.php");
?>