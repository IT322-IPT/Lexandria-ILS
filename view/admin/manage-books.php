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
        <a class="nav-link collapsed" href="manage-users.php">
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
<!-- End Sidebar-->

<main id="main" class="main">

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Books List</h5>

        <!-- Search Bar -->
        <div class="search-bar">
            <form class="search-form d-flex align-items-center" method="POST" action="">
            <input type="text" name="query" placeholder="Search by ISBN, Title, Author, Genre, or Status" class="form-control w-100">
            <button type="submit" title="Search"><i class="bi bi-search fs-5"></i></button>
            </form>
        </div><!-- End Search Bar -->

        <!-- Table with striped rows -->
        <table class="table table-striped text-center">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Title</th>
                    <th scope="col">Author</th>
                    <th scope="col">Page Count</th>
                    <th scope="col">Genre</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include("../../dB/config.php");

                $searchQuery = "";
                if (isset($_POST['query']) && !empty($_POST['query'])) {
                    $searchQuery = trim($_POST['query']);
                    $query = "SELECT * FROM books WHERE 
                              isbn LIKE '%$searchQuery%' OR 
                              title LIKE '%$searchQuery%' OR 
                              author LIKE '%$searchQuery%' OR 
                              genre LIKE '%$searchQuery%' OR 
                              status LIKE '%$searchQuery%'";
                } else {
                    $query = "SELECT * FROM books";
                }

                $result = mysqli_query($conn, $query);

                if (!$result) {
                    die("Query failed: " . mysqli_error($conn));
                }

                if (mysqli_num_rows($result) > 0) {
                    $count = 1;
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                              <th scope='row'>{$count}</th>
                              <td>{$row['isbn']}</td>
                              <td>{$row['title']}</td>
                              <td>{$row['author']}</td>
                              <td>{$row['page_count']}</td>
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