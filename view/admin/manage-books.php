<?php
include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
?>
<!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed " href="index.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->

      <li class="nav-item">
        <a class="nav-link" href="manage-books.php">
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
        <a class="nav-link collapsed" href="requests.html">
          <i class="bi bi-envelope"></i>
          <span>Requests</span>
        </a>
      </li><!-- End Requests (previously Contact Page) Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="borrowed-books.html">
          <i class="bi bi-book-half"></i> <!-- originally bi-card-list -->
          <span>Borrowed Books</span>
        </a>
      </li><!-- End Manage All Borrowed Books (previously Register Page) Nav -->
    </ul>
  </aside>
<!-- End Sidebar-->

<main id="main" class="main">

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Books List</h5>

        <!-- Table with striped rows -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Author</th>
                    <th scope="col">Page Count</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Genre</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include("../../dB/config.php"); 

                $query = "SELECT * FROM books"; 
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    die("Query failed: " . mysqli_error($conn)); 
                }

                if (mysqli_num_rows($result) > 0) {
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <th scope='row'>{$count}</th>
                                <td>{$row['title']}</td>
                                <td>{$row['author']}</td>
                                <td>{$row['page_count']}</td>
                                <td>{$row['isbn']}</td>
                                <td>{$row['genre']}</td>
                                <td>{$row['status']}</td>
                            </tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>No books found</td></tr>";
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