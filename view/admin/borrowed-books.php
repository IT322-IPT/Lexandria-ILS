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
        <a class="nav-link" href="borrowed-books.php">
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

  </aside><!-- End Sidebar-->

<main id="main" class="main">
<div class="card">
    <div class="card-body">
        <h5 class="card-title">Checked out books</h5>
        <p>List of books that have been approved and are currently checked out.</p>

        <!-- Filter Dropdown -->
        <form method="GET" class="mb-3">
            <label for="filter" class="form-label">Filter by Status:</label>
            <select name="filter" class="form-select" onchange="this.form.submit()">
                <option value="">All</option>
                <option value="Borrowed" <?php if(isset($_GET['filter']) && $_GET['filter'] == 'Borrowed') echo 'selected'; ?>>Borrowed</option>
                <option value="Returned" <?php if(isset($_GET['filter']) && $_GET['filter'] == 'Returned') echo 'selected'; ?>>Returned</option>
            </select>
        </form>

        <table class="table table-sm center-text text-center">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">User ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">ISBN</th>
                    <th scope="col">Borrow Date</th>
                    <th scope="col">Due Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                include("../../dB/config.php");

                // Base query
                $query = "SELECT * FROM borrowed_books";

                // Apply filter if selected
                if (isset($_GET['filter']) && $_GET['filter'] != '') {
                    $filter = $_GET['filter'];
                    $query .= " WHERE status = '$filter'";
                }

                $query .= " ORDER BY borrow_date DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
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
                                <td>{$row['status']}</td>
                                <td>";
                        
                        if ($row['status'] === 'Borrowed') {
                            echo "<form method='POST' action=''>
                                    <input type='hidden' name='borrow_id' value='{$row['id']}'>
                                    <button type='submit' name='mark_returned' class='btn btn-success btn-sm'>Mark as Returned</button>
                                  </form>";
                        } else {
                            echo "<span class='badge bg-secondary'>Returned</span>";
                        }

                        echo "</td></tr>";
                        $count++;
                    }
                } else {
                    echo "<tr><td colspan='8' class='text-center'>No borrowed books found</td></tr>";
                }
                mysqli_close($conn);
                ?>
            </tbody>
        </table>
    </div>
</div>


<?php
if (isset($_POST['mark_returned'])) {
    include("../../dB/config.php");

    $borrow_id = $_POST['borrow_id'];

    // Get the ISBN of the returned book
    $isbnQuery = "SELECT isbn FROM borrowed_books WHERE id = ?";
    $stmtIsbn = mysqli_prepare($conn, $isbnQuery);
    mysqli_stmt_bind_param($stmtIsbn, "i", $borrow_id);
    mysqli_stmt_execute($stmtIsbn);
    mysqli_stmt_bind_result($stmtIsbn, $isbn);
    mysqli_stmt_fetch($stmtIsbn);
    mysqli_stmt_close($stmtIsbn);

    if ($isbn) {
        // Update the borrowed_books table
        $updateQuery = "UPDATE borrowed_books SET status = 'Returned' WHERE id = ?";
        $stmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($stmt, "i", $borrow_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // Update the books table to set status to 'Available'
        $updateBookQuery = "UPDATE books SET status = 'Available' WHERE isbn = ?";
        $stmtBook = mysqli_prepare($conn, $updateBookQuery);
        mysqli_stmt_bind_param($stmtBook, "s", $isbn);
        mysqli_stmt_execute($stmtBook);
        mysqli_stmt_close($stmtBook);

        // Update the user_borrow_requests table to set status to 'Returned'
        $userQuery = "SELECT user_id FROM borrowed_books WHERE id = ?";
        $stmtUser = mysqli_prepare($conn, $userQuery);
        mysqli_stmt_bind_param($stmtUser, "i", $borrow_id);
        mysqli_stmt_execute($stmtUser);
        mysqli_stmt_bind_result($stmtUser, $user_id);
        mysqli_stmt_fetch($stmtUser);
        mysqli_stmt_close($stmtUser);
        
        $updateUserRequestQuery = "UPDATE user_borrow_requests SET status = 'Returned' WHERE user_id = ? AND ISBN = ? AND status = 'Approved'";
        $stmtUserRequest = mysqli_prepare($conn, $updateUserRequestQuery);
        mysqli_stmt_bind_param($stmtUserRequest, "is", $user_id, $isbn);
        mysqli_stmt_execute($stmtUserRequest);
        mysqli_stmt_close($stmtUserRequest);

        echo "<script>alert('Book marked as returned!'); window.location.href='borrowed-books.php';</script>";
    } else {
        echo "Error: ISBN not found.";
    }

    mysqli_close($conn);
}
?>


<?php
include("./includes/footer.php");
?>