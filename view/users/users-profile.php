<?php
ob_start(); // Start output buffering
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
include("./includes/sidebar-user-profile.php");

// Check if user is logged in
if (isset($_SESSION["authUser"]["userId"])) {
  $user_id = $_SESSION["authUser"]["userId"];

  // Fetch user details
  $query = "SELECT CONCAT(firstName, ' ', lastName) AS fullName, gender, email, phoneNumber AS phone, birthday, createdAt, password 
            FROM users WHERE userId = ?";
  $stmt = mysqli_prepare($conn, $query);
  mysqli_stmt_bind_param($stmt, "i", $user_id);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if ($row = mysqli_fetch_assoc($result)) {
      $_SESSION["authUser"]["fullName"] = $row["fullName"];
      $_SESSION["authUser"]["gender"] = $row["gender"];
      $_SESSION["authUser"]["email"] = $row["email"];
      $_SESSION["authUser"]["phone"] = $row["phone"];
      $_SESSION["authUser"]["birthday"] = $row["birthday"];
      $_SESSION["authUser"]["createdAt"] = $row["createdAt"];
      $_SESSION["authUser"]["password"] = $row["password"]; // Store plaintext password (not recommended)
  } else {
      die("User data not found.");
  }
  mysqli_stmt_close($stmt);
} else {
  die("User not logged in.");
}

// Retrieve details for display
$user_id = $_SESSION["authUser"]["userId"] ?? "Ngano way id ambot";
$fullName = $_SESSION["authUser"]["fullName"] ?? "Guest";
$gender = $_SESSION["authUser"]["gender"] ?? "Not Specified";
$useremail = $_SESSION["authUser"]["email"] ?? "No Email";
$userphone = $_SESSION["authUser"]["phone"] ?? "No Phone Number";
$birthday = $_SESSION["authUser"]["birthday"] ?? "No Birthday";
$dateJoined = $_SESSION["authUser"]["createdAt"] ?? " ";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
  $user_id = $_SESSION["authUser"]["userId"];
  $email = trim($_POST["email"]);
  $phone = trim($_POST["phone"]);
  $current_password = trim($_POST["current_password"]);
  $new_password = trim($_POST["new_password"]);
  $confirm_password = trim($_POST["confirm_password"]);
  
  $errors = [];
  
  if (empty($email) && empty($phone) && empty($new_password)) {
      $errors[] = "At least one field must be filled out to update your account.";
  }
  
  if (!empty($new_password)) {
      if ($new_password !== $confirm_password) {
          $errors[] = "New password and confirmation password do not match.";
      }
      
      $query = "SELECT password FROM users WHERE userId = ?";
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, "i", $user_id);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      
      if ($row = mysqli_fetch_assoc($result)) {
        $query = "SELECT email, phoneNumber AS phone, password FROM users WHERE userId = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            // Update session with the latest user details before making changes
            $_SESSION["authUser"]["email"] = $row["email"];
            $_SESSION["authUser"]["phone"] = $row["phone"];
            $_SESSION["authUser"]["password"] = $row["password"];

            if ($current_password !== $row['password']) {
                $errors[] = "Current password is incorrect.";
            }
        } else {
            $errors[] = "User not found.";
        }
        mysqli_stmt_close($stmt);
                  
          if ($current_password !== $row['password']) {
              $errors[] = "Current password is incorrect.";
          }
      } else {
          $errors[] = "User not found.";
      }
      // mysqli_stmt_close($stmt);
  }
  
  if (empty($errors)) {
      $query = "UPDATE users SET ";
      $updates = [];
      $params = [];
      $types = "";
      
      if (!empty($email)) {
          $updates[] = "email = ?";
          $params[] = $email;
          $types .= "s";
      }
      
      if (!empty($phone)) {
          $updates[] = "phoneNumber = ?";
          $params[] = $phone;
          $types .= "s";
      }
      
      if (!empty($new_password)) {
          $updates[] = "password = ?";
          $params[] = $new_password;
          $types .= "s";
      }
      
      $query .= implode(", ", $updates) . " WHERE userId = ?";
      $params[] = $user_id;
      $types .= "i";
      
      $stmt = mysqli_prepare($conn, $query);
      mysqli_stmt_bind_param($stmt, $types, ...$params);
      
      if (mysqli_stmt_execute($stmt)) {
        if (!empty($email)) {
            $_SESSION["authUser"]["email"] = $email;
        }
        if (!empty($phone)) {
            $_SESSION["authUser"]["phone"] = $phone;
        }
        if (!empty($new_password)) {
            $_SESSION["authUser"]["password"] = $new_password; // Store securely in production
        }
        echo "<div class='alert alert-success'>Account updated successfully.</div>";
    }
     else {
          echo "<div class='alert alert-danger'>Error updating account.</div>";
      }
      mysqli_stmt_close($stmt);
  } else {
      foreach ($errors as $error) {
          echo "<div class='alert alert-danger'>$error</div>";
      }
  }
}


// Fetch books borrowed per month
$lineChartLabels = ['January', 'February', 'March'];
$lineChartData = [0, 0, 0];

$query = "SELECT MONTHNAME(request_date) AS month, COUNT(*) AS count 
          FROM user_borrow_requests 
          WHERE user_id = $user_id 
          GROUP BY month 
          ORDER BY request_date ASC";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $index = array_search($row["month"], $lineChartLabels);
    if ($index !== false) {
        $lineChartData[$index] = (int)$row["count"];
    }
}

// Fetch most borrowed genres
$pieChartData = [];
$query = "SELECT b.genre, COUNT(*) AS count 
          FROM user_borrow_requests ubr 
          JOIN books b ON ubr.ISBN = b.isbn 
          WHERE ubr.user_id = $user_id 
          GROUP BY b.genre 
          ORDER BY count DESC";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $pieChartData[] = ["value" => (int)$row["count"], "name" => $row["genre"]];
}

// Fetch most read authors
$donutChartData = [];
$donutChartLabels = [];
$query = "SELECT b.author, COUNT(*) AS count 
          FROM user_borrow_requests ubr 
          JOIN books b ON ubr.ISBN = b.isbn 
          WHERE ubr.user_id = $user_id 
          GROUP BY b.author 
          ORDER BY count DESC";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $donutChartLabels[] = $row["author"];
    $donutChartData[] = (int)$row["count"];
}

mysqli_close($conn);
ob_end_flush(); // Send the output at the end

?>

<div class="pagetitle">
  <h1>Profile</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../../index.html">Home</a></li>
      <li class="breadcrumb-item">Users</li>
      <li class="breadcrumb-item active">Profile</li>
    </ol>
  </nav>
</div>

<section class="section profile">
  <div class="col-xl-12">
    <div class="card">
      <div class="card-body pt-3">

        <ul class="nav nav-tabs nav-tabs-bordered">

          <!-- Overview Tab -->
          <li class="nav-item">
            <button
              class="nav-link active"
              data-bs-toggle="tab"
              data-bs-target="#profile-overview"
            >
              Overview
            </button>
          </li>
          <!-- Stats Tab -->
          <li class="nav-item">
            <button
              class="nav-link"
              data-bs-toggle="tab"
              data-bs-target="#profile-stats"
            >
              Stats
            </button>
          </li>
          <li class="nav-item">
            <button
              class="nav-link"
              data-bs-toggle="tab"
              data-bs-target="#profile-change-password"
            >
              Edit account
            </button>
          </li>
        </ul>

        <!--------------------------------------------------------->

        <div class="tab-content pt-3">
          <!-- Start Overview -->
          <div class="tab-pane fade show active profile-overview"id="profile-overview">
            <h5 class="card-title">Profile Details</h5>

            <div class="row">
              <div class="col-lg-3 col-md-4 label">ID</div>
              <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($user_id); ?></div>
            </div> 
            <div class="row">
              <div class="col-lg-3 col-md-4 label">Full Name</div>
              <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($fullName); ?></div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-4 label">Gender</div>
              <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($gender); ?></div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-4 label">Birthday</div>
              <div class="col-lg-9 col-md-8">
                <?php 
                    if ($birthday !== "No Birthday") {
                        echo date("F j, Y", strtotime($birthday)); // Example: March 25, 2025
                    } else {
                        echo "No Birthday";
                    }
                ?>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-4 label">Phone Number</div>
              <div class="col-lg-9 col-md-8">
              <?php echo htmlspecialchars($userphone); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-4 label">Email</div>
              <div class="col-lg-9 col-md-8">
              <?php echo htmlspecialchars($useremail); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-4 label">Date Joined</div>
              <div class="col-lg-9 col-md-8">
                  <?php echo date("F j, Y", strtotime($dateJoined)); ?>
              </div>
            </div>
          </div>
          <!-- End Overview -->

          <!-- Start Stats -->
          <div class="tab-pane fade profile-stats" id="profile-stats">
              <div class="col-lg-10 mx-auto">
                  <div class="card">
                      <div class="card-body text-center">
                          <h5 class="card-title">Number of Books Borrowed</h5>
                          <p class="card-text">For current year - 2025</p>
                        
                          <!-- Line Chart -->
                          <div class="d-flex justify-content-center">
                            <canvas id="lineChart" style="max-height: 400px;"></canvas>
                          </div>
                          <script>
                              document.addEventListener("DOMContentLoaded", () => {
                                  new Chart(document.querySelector('#lineChart'), {
                                      type: 'line',
                                      data: {
                                          labels: <?= json_encode($lineChartLabels); ?>,
                                          datasets: [{
                                              label: 'Books borrowed',
                                              data: <?= json_encode($lineChartData); ?>,
                                              fill: false,
                                              borderColor: 'rgb(75, 192, 192)',
                                              tension: 0.1
                                          }]
                                      },
                                      options: {
                                          scales: {
                                              y: {
                                                  beginAtZero: true
                                              }
                                          }
                                      }
                                  });
                              });
                          </script>
                          <!-- End Line Chart -->
                      </div>
                  </div>
              </div>

              <div class="col-lg-10 mx-auto">
                  <div class="card">
                      <div class="card-body text-center">
                          <h5 class="card-title">Most Borrowed Genres</h5>

                          <!-- Pie Chart -->
                          <!-- <div class="d-flex justify-content-center"> -->
                            <div id="pieChart" style="min-height: 400px;" class="echart"></div>
                          <!-- </div> -->
                          <script>
                              document.addEventListener("DOMContentLoaded", () => {
                                  echarts.init(document.querySelector("#pieChart")).setOption({
                                      title: {
                                          text: 'My Top Genres',
                                          subtext: '2025',
                                          left: 'center'
                                      },
                                      tooltip: {
                                          trigger: 'item'
                                      },
                                      legend: {
                                          orient: 'vertical',
                                          left: 'left'
                                      },
                                      series: [{
                                          type: 'pie',
                                          radius: '50%',
                                          data: <?= json_encode($pieChartData); ?>,
                                          emphasis: {
                                              itemStyle: {
                                                  shadowBlur: 10,
                                                  shadowOffsetX: 0,
                                                  shadowColor: 'rgba(0, 0, 0, 0.5)'
                                              }
                                          }
                                      }]
                                  });
                              });
                          </script>
                          <!-- End Pie Chart -->
                      </div>
                  </div>
              </div>

              <div class="col-lg-10 mx-auto">
                  <div class="card">
                      <div class="card-body text-center">
                          <h5 class="card-title">Most Read Authors</h5>

                          <!-- Donut Chart -->
                          <!-- <div class="d-flex justify-content-center"> -->
                            <div id="donutChart"></div>
                          <!-- </div> -->
                          <script>
                              document.addEventListener("DOMContentLoaded", () => {
                                  new ApexCharts(document.querySelector("#donutChart"), {
                                      series: <?= json_encode($donutChartData); ?>,
                                      chart: {
                                          height: 350,
                                          type: 'donut',
                                          toolbar: {
                                              show: true
                                          }
                                      },
                                      labels: <?= json_encode($donutChartLabels); ?>
                                  }).render();
                              });
                          </script>
                          <!-- End Donut Chart -->
                      </div>
                  </div>
              </div>
          </div>
          <!-- End Stats -->

          <!-- Start Edit Account -->
          <div class="tab-pane fade pt-3" id="profile-change-password">
          <?php
            if (isset($_SESSION['alertMessage']) && isset($_SESSION['alertType'])) {
              $alertMessage = $_SESSION['alertMessage'];
              $alertType = $_SESSION['alertType']; // success, danger, info, etc.
              ?>
              <div class="alert alert-<?php echo $alertType;?> alert-dismissible fade show" role="alert">
                  <i class="bi bi-check-circle me-1"></i>
                  <?php echo $alertMessage; ?>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <?php
              // Clear the session alert after it's displayed
              unset($_SESSION['alertMessage']);
              unset($_SESSION['alertType']);
            }
          ?>
          <form method="POST" action="" id="editAccountForm">
              <div class="row mb-3">
                  <label for="email" class="col-md-4 col-lg-3 col-form-label">New Email</label>
                  <div class="col-md-8 col-lg-9">
                      <input name="email" type="email" class="form-control" id="email" />
                  </div>
              </div>
              <div class="row mb-3">
                  <label for="phone" class="col-md-4 col-lg-3 col-form-label">New Phone Number</label>
                  <div class="col-md-8 col-lg-9">
                      <input name="phone" type="tel" class="form-control" id="phone" pattern="[0-9]{11}" placeholder="09XXXXXXXXX" />
                  </div>
              </div>
              <div class="row mb-3">
                  <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                  <div class="col-md-8 col-lg-9">
                      <input name="current_password" type="password" class="form-control" id="currentPassword" />
                  </div>
              </div>
              <div class="row mb-3">
                  <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                  <div class="col-md-8 col-lg-9">
                      <input name="new_password" type="password" class="form-control" id="newPassword" />
                  </div>
              </div>
              <div class="row mb-3">
                  <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                  <div class="col-md-8 col-lg-9">
                      <input name="confirm_password" type="password" class="form-control" id="renewPassword" />
                  </div>
              </div>
              <div class="text-center">
                  <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
              </div>
          </form>

          </div>
          <!-- End Edit Account -->
        </div>
      </div>
    </div>
  </div>
</section>

<script>
document.getElementById("editAccountForm").addEventListener("submit", function(event) {
    let email = document.getElementById("email").value.trim();
    let phone = document.getElementById("phone").value.trim();
    let newPassword = document.getElementById("newPassword").value.trim();
    let currentPassword = document.getElementById("currentPassword").value.trim();
    let confirmPassword = document.getElementById("renewPassword").value.trim();

    if (!email && !phone && !newPassword) {
        event.preventDefault();
        alert("At least one field must be filled out to update your account.");
    }
    if (newPassword && newPassword !== confirmPassword) {
        event.preventDefault();
        alert("New password and confirmation password do not match.");
    }
    if (newPassword && !currentPassword) {
        event.preventDefault();
        alert("Current password is required to change your password.");
    }
});
</script>
<?php
include("./includes/footer.php");
?>