<?php
session_start();

// Ensure the user is authenticated
if (!isset($_SESSION["authUser"]) || empty($_SESSION["authUser"])) {
    header("Location: ../../../IT322/login.php");
    exit();
}

// Extract user ID correctly
$user_id = $_SESSION['authUser']['userId']; // Ensure this is the correct session key for the user ID

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
include("./includes/sidebar-pages-my-books.php");
?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">My Borrowed Books</h5>
      <!-- Primary Color Bordered Table -->
      <table class="table table-bordered border-primary">
          <thead>
              <tr>
                  <th scope="col">#</th>
                  <th scope="col">Book Title</th>
                  <th scope="col" class="text-center">ISBN</th>
                  <th scope="col" class="text-center">Date Borrowed</th>
                  <th scope="col" class="text-center">Due Date</th>
              </tr>
          </thead>
          <tbody>
              <?php
              $query = "SELECT ubr.id, ubr.user_id, b.title, ubr.ISBN, DATE(ubr.request_date) AS date_borrowed, ubr.due_date
                        FROM user_borrow_requests ubr
                        JOIN books b ON ubr.ISBN = b.isbn
                        WHERE ubr.user_id = ? AND ubr.Status = 'Approved'
                        ORDER BY ubr.request_date DESC";

              if ($stmt = mysqli_prepare($conn, $query)) {
                  mysqli_stmt_bind_param($stmt, "i", $user_id);
                  mysqli_stmt_execute($stmt);
                  $result = mysqli_stmt_get_result($stmt);

                  if (mysqli_num_rows($result) > 0) {
                      $count = 1;
                      while ($row = mysqli_fetch_assoc($result)) {
                          echo "<tr>";
                          echo "<th scope='row'>" . $count . "</th>";
                          echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                          echo "<td class='text-center'>" . htmlspecialchars($row['ISBN']) . "</td>";
                          echo "<td class='text-center'>" . htmlspecialchars($row['date_borrowed']) . "</td>";
                          echo "<td class='text-center'>" . htmlspecialchars($row['due_date']) . "</td>";
                          echo "</tr>";
                          $count++;
                      }
                  } else {
                      echo "<tr><td colspan='5' class='text-center'>No borrowed books currently.</td></tr>";
                  }

                  mysqli_stmt_close($stmt);
              } else {
                  echo "<tr><td colspan='5' class='text-center text-danger'>Error fetching data. Please try again later.</td></tr>";
              }

              mysqli_close($conn);
              ?>
          </tbody>
      </table>
    </div>
</div>

<?php include("./includes/footer.php"); ?>
