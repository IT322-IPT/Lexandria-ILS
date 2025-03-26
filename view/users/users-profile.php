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

  // Corrected query to match schema
  $query = "SELECT CONCAT(firstName, ' ', lastName) AS fullName, gender, email, phoneNumber AS phone, birthday 
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
  <div class="row">
  <!-- Dako nga box sa kilid -->
    <div class="col-xl-4">
      <div class="card">
        <div class="card-body profile-card pt-4 d-flex flex-column align-items-center">
          <img
            src="../../assets/img/profile-img.jpg"
            alt="Profile"
            class="rounded-circle"
          />
          <h2><?php echo htmlspecialchars($fullName); ?></h2>
          <!-- <h3>Web Designer</h3> -->
           
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
    </div>

  <!--------------------------------------------------------->

    <!-- Kilid na box -->
    <div class="col-xl-8">
      <div class="card">
        <div class="card-body pt-3">

          <!-- Bordered Tabs -->
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
            <!-- Edit Profile Tab -->
            <li class="nav-item">
              <button
                class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#profile-edit"
              >
                Edit Profile
              </button>
            </li>
            <!-- Settings Tab -->
            <li class="nav-item">
              <button
                class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#profile-settings"
              >
                Settings
              </button>
            </li>
            <!-- Change Password Tab -->
            <li class="nav-item">
              <button
                class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#profile-change-password"
              >
                Change Password
              </button>
            </li>
          </ul>

  <!--------------------------------------------------------->

          <div class="tab-content pt-2">
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
            </div>
            <!-- End Overview -->

            <!-- Start Stats -->
            <div class="tab-pane fade profile-stats" id="profile-stats">
              <div class="col-lg-10">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Number of Books Borrowed</h5>

                    <!-- Line Chart -->
                    <canvas id="lineChart" style="max-height: 400px;"></canvas>
                    <script>
                      document.addEventListener("DOMContentLoaded", () => {
                        new Chart(document.querySelector('#lineChart'), {
                          type: 'line',
                          data: {
                            labels: ['January', 'February', 'March'],
                            datasets: [{
                              label: 'Books borrowed',
                              data: [5, 4, 3],
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

              <div class="col-lg-10">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Most Borrowed Genres</h5>
                    <!-- Pie Chart -->
                    <div id="pieChart" style="min-height: 400px;" class="echart"></div>
                    <script>
                      document.addEventListener("DOMContentLoaded", () => {
                        echarts.init(document.querySelector("#pieChart")).setOption({
                          title: {
                            text: 'My Top Genres',
                            subtext: '2023-2025',
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
                            // name: 'Access From',
                            type: 'pie',
                            radius: '50%',
                            data: [{
                                value: 8,
                                name: 'Mystery'
                              },
                              {
                                value: 1,
                                name: 'Contemporary Romance'
                              },

                              {
                                value: 7,
                                name: 'Dark Academia'
                              },
                              {
                                value: 10,
                                name: 'Classics'
                              },
                              {
                                value: 2,
                                name: 'Dystopian'
                              },
                              {
                                value: 7,
                                name: 'Fantasy'
                              },
                            ],
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
              
              <div class="col-lg-10 ">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title">Most Read Authors</h5>

                    <!-- Donut Chart -->
                    <div id="donutChart"></div>

                    <script>
                      document.addEventListener("DOMContentLoaded", () => {
                        new ApexCharts(document.querySelector("#donutChart"), {
                          series: [44, 55, 13, 43, 22],
                          chart: {
                            height: 350,
                            type: 'donut',
                            toolbar: {
                              show: true
                            }
                          },
                          labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
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
            <div class="tab-pane fade profile-edit pt-3" id="profile-edit">
              <!-- Profile Edit Form -->
              <form>
                <div class="row mb-3">
                  <label
                    for="profileImage"
                    class="col-md-4 col-lg-3 col-form-label"
                    >Profile Image</label
                  >
                  <div class="col-md-8 col-lg-9">
                    <img
                      src="../../assets/img/profile-img.jpg"
                      alt="Profile"
                    />
                    <div class="pt-2">
                      <a
                        href="#"
                        class="btn btn-primary btn-sm"
                        title="Upload new profile image"
                        ><i class="bi bi-upload"></i
                      ></a>
                      <a
                        href="#"
                        class="btn btn-danger btn-sm"
                        title="Remove my profile image"
                        ><i class="bi bi-trash"></i
                      ></a>
                    </div>
                  </div>
                </div>

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
                      value="<?php echo htmlspecialchars($fullName); ?>"
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
                      value="<?php echo htmlspecialchars($gender); ?>"
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
                      value="<?php echo htmlspecialchars($birthday); ?>"
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
                      value="<?php echo htmlspecialchars($phone); ?>"
                    />
                  </div>
                </div>

                <div class="row mb-3">
                  <label
                    for="Email"
                    class="col-md-4 col-lg-3 col-form-label"
                    >Email</label
                  >
                  <div class="col-md-8 col-lg-9">
                    <input
                      name="email"
                      type="text"
                      class="form-control"
                      id="Email"
                      value="<?php echo htmlspecialchars($email); ?>"
                    />
                  </div>
                </div>

                <div class="row mb-3">
                  <label
                    for="Twitter"
                    class="col-md-4 col-lg-3 col-form-label"
                    >Twitter Profile</label
                  >
                  <div class="col-md-8 col-lg-9">
                    <input
                      name="twitter"
                      type="text"
                      class="form-control"
                      id="Twitter"
                      value="https://twitter.com/#"
                    />
                  </div>
                </div>

                <div class="row mb-3">
                  <label
                    for="Facebook"
                    class="col-md-4 col-lg-3 col-form-label"
                    >Facebook Profile</label
                  >
                  <div class="col-md-8 col-lg-9">
                    <input
                      name="facebook"
                      type="text"
                      class="form-control"
                      id="Facebook"
                      value="https://facebook.com/#"
                    />
                  </div>
                </div>

                <div class="row mb-3">
                  <label
                    for="Instagram"
                    class="col-md-4 col-lg-3 col-form-label"
                    >Instagram Profile</label
                  >
                  <div class="col-md-8 col-lg-9">
                    <input
                      name="instagram"
                      type="text"
                      class="form-control"
                      id="Instagram"
                      value="https://instagram.com/#"
                    />
                  </div>
                </div>

                <div class="row mb-3">
                  <label
                    for="Linkedin"
                    class="col-md-4 col-lg-3 col-form-label"
                    >Linkedin Profile</label
                  >
                  <div class="col-md-8 col-lg-9">
                    <input
                      name="linkedin"
                      type="text"
                      class="form-control"
                      id="Linkedin"
                      value="https://linkedin.com/#"
                    />
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-primary">
                    Save Changes
                  </button>
                </div>
              </form>
              <!-- End Profile Edit Form -->
            </div>
            <!-- End Edit Profile -->

            <!-- Start Settings -->
            <div class="tab-pane fade pt-3" id="profile-settings">
              <!-- Settings Form -->
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
              <!-- End settings Form -->
            </div>
            <!-- End Settings -->

            <!-- Start Change Password -->
            <div class="tab-pane fade pt-3" id="profile-change-password">
              <!-- Change Password Form -->
              <form>
                <div class="row mb-3">
                  <label
                    for="currentPassword"
                    class="col-md-4 col-lg-3 col-form-label"
                    >Current Password</label
                  >
                  <div class="col-md-8 col-lg-9">
                    <input
                      name="password"
                      type="password"
                      class="form-control"
                      id="currentPassword"
                    />
                  </div>
                </div>

                <div class="row mb-3">
                  <label
                    for="newPassword"
                    class="col-md-4 col-lg-3 col-form-label"
                    >New Password</label
                  >
                  <div class="col-md-8 col-lg-9">
                    <input
                      name="newpassword"
                      type="password"
                      class="form-control"
                      id="newPassword"
                    />
                  </div>
                </div>

                <div class="row mb-3">
                  <label
                    for="renewPassword"
                    class="col-md-4 col-lg-3 col-form-label"
                    >Re-enter New Password</label
                  >
                  <div class="col-md-8 col-lg-9">
                    <input
                      name="renewpassword"
                      type="password"
                      class="form-control"
                      id="renewPassword"
                    />
                  </div>
                </div>

                <div class="text-center">
                  <button type="submit" class="btn btn-primary">
                    Change Password
                  </button>
                </div>
              </form>
              <!-- End Change Password Form -->
            </div>
            <!-- End Change Password -->

          </div>
          <!-- End Bordered Tabs -->
        </div>
      </div>
    </div>
  </div>
</section>

<?php
include("./includes/footer.php");
?>