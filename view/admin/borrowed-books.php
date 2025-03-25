<?php
include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
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
        <a class="nav-link collapsed" href="manage-users.html">
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
        <a class="nav-link" href="borrowed-books.php">
          <i class="bi bi-book-half"></i> <!-- originally bi-card-list -->
          <span>Borrowed Books</span>
        </a>
      </li><!-- End Manage All Borrowed Books (previously Register Page) Nav -->

    </ul>

  </aside><!-- End Sidebar-->

<main id="main" class="main">
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Borrowed Books</h5>
        <p>List of books that have been approved and borrowed.</p>

        <table class="table table-sm">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Full Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Borrow Date</th>
                    <th scope="col">Return Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include("../../dB/config.php");
                $query = "SELECT * FROM borrowed_books ORDER BY borrow_date DESC";
                $result = mysqli_query($conn, $query);
                if (mysqli_num_rows($result) > 0) {
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <th scope='row'>{$count}</th>
                                <td>{$row['full_name']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['isbn']}</td>
                                <td>{$row['borrow_date']}</td>
                                <td>{$row['return_date']}</td>
                            </tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='6' class='text-center'>No borrowed books found</td></tr>";
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include("./includes/footer.php");
?>