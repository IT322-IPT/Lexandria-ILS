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
$email = $_SESSION["authUser"]["email"] ?? "No Email";
$phone = $_SESSION["authUser"]["phone"] ?? "No Phone Number";
$birthday = $_SESSION["authUser"]["birthday"] ?? "No Birthday";
$dateJoined = $_SESSION["authUser"]["createdAt"] ?? " ";

// Handle form submission for updating email, phone, or password
if (isset($_POST["update"])) {
  $user_id = $_SESSION["authUser"]["userId"];
  $new_email = !empty(trim($_POST["email"])) ? mysqli_real_escape_string($conn, $_POST["email"]) : null;
  $new_phone = !empty(trim($_POST["phone"])) ? mysqli_real_escape_string($conn, $_POST["phone"]) : null;
  $current_password = !empty(trim($_POST["current_password"])) ? $_POST["current_password"] : null;
  $new_password = !empty(trim($_POST["new_password"])) ? $_POST["new_password"] : null;
  $confirm_password = !empty(trim($_POST["confirm_password"])) ? $_POST["confirm_password"] : null;

  // Count how many fields are being updated
  $update_count = 0;
  if ($new_email) $update_count++;
  if ($new_phone) $update_count++;
  if ($new_password) $update_count++;

  if ($update_count === 0) {
      echo "<script>alert('No changes detected. Please modify at least one field.');</script>";
      exit();
  } elseif ($update_count > 1) {
      echo "<script>alert('You can only update one field at a time.');</script>";
      exit();
  }

  // Prepare dynamic update query
  $update_query = "UPDATE users SET ";
  $params = [];
  $param_types = "";

  if ($new_email) {
      $update_query .= "email = ? ";
      $params[] = $new_email;
      $param_types .= "s";
  } elseif ($new_phone) {
      $update_query .= "phoneNumber = ? ";
      $params[] = $new_phone;
      $param_types .= "s";
  } elseif ($new_password) {
      if (!$current_password || $current_password !== $_SESSION["authUser"]["password"]) {
          echo "<script>alert('Incorrect current password.');</script>";
          exit();
      }

      if ($new_password !== $confirm_password) {
          echo "<script>alert('New passwords do not match.');</script>";
          exit();
      }

      $update_query .= "password = ? ";
      $params[] = $new_password;
      $param_types .= "s";
  }

  $update_query .= "WHERE userId = ?";
  $params[] = $user_id;
  $param_types .= "i";

  $stmt = mysqli_prepare($conn, $update_query);
  mysqli_stmt_bind_param($stmt, $param_types, ...$params);

  if (mysqli_stmt_execute($stmt)) {
      // Update session variables if needed
      if ($new_email) $_SESSION["authUser"]["email"] = $new_email;
      if ($new_phone) $_SESSION["authUser"]["phone"] = $new_phone;
      if ($new_password) $_SESSION["authUser"]["password"] = $new_password; // No hashing

      echo "<script>alert('Changes saved successfully!');</script>";
  } else {
      echo "<script>alert('Error updating profile.');</script>";
  }

  mysqli_stmt_close($stmt);
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
  <!-- Dako nga box sa kilid -->
    <!-- <div class="col-xl-4">
      <div class="card">
        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
          <img
            src="../../assets/img/profile-img.jpg"
            alt="Profile"
            class="rounded-circle"
          />
          <h2><//?php echo htmlspecialchars($fullName); ?></h2>           
          <div class="social-links mt-2">
            <a href="#" class="twitter"><i class="bi bi-twitter"></i></a>
            <a href="#" class="facebook"
              ><i class="bi bi-facebook"></i
            ></a>
            <a href="#" class="instagram"
              ><i class="bi bi-instagram"></i
            ></a>
            <a href="#" class="linkedin"
              ><i class="bi bi-linkedin"></i
            ></a>
          </div>
        </div>
      </div>
    </div> -->

  <!--------------------------------------------------------->

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
              <?php echo htmlspecialchars($phone); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-4 label">Email</div>
              <div class="col-lg-9 col-md-8">
              <?php echo htmlspecialchars($email); ?>
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

          <!-- Start Edit Profile -->
          <!-- <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
            <form>
              <div class="row mb-3">
                <label
                  for="fullName"
                  class="col-md-4 col-lg-3 col-form-label"
                  >Full Name</label
                >
                <div class="col-md-8 col-lg-9">
                  <input
                    name="fullName"
                    type="text"
                    class="form-control"
                    id="fullName"
                    value="<?//php echo htmlspecialchars($fullName); ?>"
                  />
                </div>
              </div>

               <div class="row mb-3">
                <label
                  for="company"
                  class="col-md-4 col-lg-3 col-form-label"
                  >Gender</label
                >
                <div class="col-md-8 col-lg-9">
                  <input
                    name="gender"
                    type="text"
                    class="form-control"
                    id="gender"
                    value="<? //php echo htmlspecialchars($gender); ?>"
                  />
                </div>
              </div>

              <div class="row mb-3">
                <label
                  for="Birthday"
                  class="col-md-4 col-lg-3 col-form-label"
                  >Birthday</label
                >
                <div class="col-md-8 col-lg-9">
                  <input
                    name="birthday"
                    type="text"
                    class="form-control"
                    id="Birthday"
                    value="<? //php echo htmlspecialchars($birthday); ?>"
                  />
                </div>
              </div>

              <div class="row mb-3">
                <label
                  for="Phone"
                  class="col-md-4 col-lg-3 col-form-label"
                  >Phone Number</label
                >
                <div class="col-md-8 col-lg-9">
                  <input
                    name="phone"
                    type="text"
                    class="form-control"
                    id="Phone"
                    value="<? //php echo htmlspecialchars($phone); ?>"
                  />
                </div>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary">
                  Save Changes
                </button>
              </div>
            </form>
          </div> -->
          <!-- End Edit Profile -->

          <!-- Start Settings -->
          <!-- <div class="tab-pane fade pt-3" id="profile-settings">
            <form>
              <div class="row mb-3">
                <label
                  for="fullName"
                  class="col-md-4 col-lg-3 col-form-label"
                  >Email Notifications</label
                >
                <div class="col-md-8 col-lg-9">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="changesMade"
                      checked
                    />
                    <label class="form-check-label" for="changesMade">
                      Changes made to your account
                    </label>
                  </div>
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="newProducts"
                      checked
                    />
                    <label class="form-check-label" for="newProducts">
                      Information on new products and services
                    </label>
                  </div>
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="proOffers"
                    />
                    <label class="form-check-label" for="proOffers">
                      Marketing and promo offers
                    </label>
                  </div>
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="securityNotify"
                      checked
                      disabled
                    />
                    <label
                      class="form-check-label"
                      for="securityNotify"
                    >
                      Security alerts
                    </label>
                  </div>
                </div>
              </div>

              <div class="text-center">
                <button type="submit" class="btn btn-primary">
                  Save Changes
                </button>
              </div>
            </form>
          </div> -->
          <!-- End Settings -->

          <!-- Start Edit Account -->
          <div class="tab-pane fade pt-3" id="profile-change-password">
              <form method="POST" action="">

                  <!-- Email Update -->
                  <div class="row mb-3">
                      <label for="email" class="col-md-4 col-lg-3 col-form-label">New Email</label>
                      <div class="col-md-8 col-lg-9">
                          <input name="email" type="email" class="form-control" id="email" />
                      </div>
                  </div>

                  <!-- Phone Update -->
                  <div class="row mb-3">
                      <label for="phone" class="col-md-4 col-lg-3 col-form-label">New Phone Number</label>
                      <div class="col-md-8 col-lg-9">
                          <input name="phone" type="tel" class="form-control" id="phone" pattern="[0-9]{11}" placeholder="09XXXXXXXXX" />
                      </div>
                  </div>

                  <!-- Password Update -->
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

<?php
include("./includes/footer.php");
?>