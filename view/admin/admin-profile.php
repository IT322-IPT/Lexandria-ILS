<?php
ob_start(); // Start output buffering
session_start();
if (!isset($_SESSION["authUser"])) {
    header("Location: ../../../IT322/login.php");
    exit();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
include("./includes/sidebar-admin-profile.php");


// Check if user is logged in
if (isset($_SESSION["authUser"]["userId"])) {
    $admin_id = $_SESSION["authUser"]["userId"];
  
    // Fetch user details
    $query = "SELECT CONCAT(firstName, ' ', lastName) AS fullName, gender, email, phoneNumber AS phone, birthday, createdAt, password 
              FROM admins WHERE adminId = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $admin_id);
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
  $admin_id = $_SESSION["authUser"]["userId"] ?? "Ngano way id ambot";
  $fullName = $_SESSION["authUser"]["fullName"] ?? "Guest";
  $gender = $_SESSION["authUser"]["gender"] ?? "Not Specified";
  $admin_email = $_SESSION["authUser"]["email"] ?? "No Email";
  $admin_phone = $_SESSION["authUser"]["phone"] ?? "No Phone Number";
  $birthday = $_SESSION["authUser"]["birthday"] ?? "No Birthday";
  $dateJoined = $_SESSION["authUser"]["createdAt"] ?? " ";

ob_end_flush(); // Send the output at the end
?>

<div class="pagetitle">
<?php if (isset($_SESSION['alertMessage'])): ?>
    <div class="alert alert-<?php echo $_SESSION['alertType']; ?> alert-dismissible fade show d-flex align-items-center" role="alert">
        <?php if ($_SESSION['alertType'] === "success"): ?>
            <i class="bi bi-check-circle-fill me-2"></i>
        <?php else: ?>
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <?php endif; ?>
        <?php echo $_SESSION['alertMessage']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['alertMessage']); unset($_SESSION['alertType']); ?>
<?php endif; ?>
  <h1>My Account</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="../../index.html">Home</a></li>
      <li class="breadcrumb-item">Admin</li>
      <li class="breadcrumb-item active">Account</li>
    </ol>
  </nav>
</div>

<section class="section profile">
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
            <h5 class="card-title">Account Details</h5>

            <div class="row">
              <div class="col-lg-3 col-md-4 label">ID</div>
              <div class="col-lg-9 col-md-8"><?php echo htmlspecialchars($admin_id); ?></div>
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
              <?php echo htmlspecialchars($admin_phone); ?>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-3 col-md-4 label">Email</div>
              <div class="col-lg-9 col-md-8">
              <?php echo htmlspecialchars($admin_email); ?>
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

          <!-- Start Edit Account -->
          <div class="tab-pane fade pt-3" id="profile-change-password">
              <form method="POST" action="edit-account.php">

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

<?php include("./includes/footer.php"); ?>