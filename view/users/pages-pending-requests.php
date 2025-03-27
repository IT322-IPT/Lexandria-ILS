<?php
session_start();
if (!isset($_SESSION["authUser"])) {
    header("Location: ../../../IT322/login.php");
    exit();
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
include("./includes/sidebar-pages-pending-requests.php");

$FullName = $_SESSION['authUser']['fullName']; 
$Email = $_SESSION['authUser']['email'];
$user_id = $_SESSION['authUser']['userId']; // Ensure this is the correct session key for the user ID
?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">My Sent Borrow Requests</h5>

        <!-- Filter Dropdown -->
        <div class="mb-3">
            <label for="statusFilter" class="form-label">Filter by status:</label>
            <select id="statusFilter" class="form-select">
                <option value="All">All</option>
                <option value="Pending">Pending</option>
                <option value="Approved">Approved</option>
                <option value="Rejected">Rejected</option>
                <option value="Returned">Returned</option>
            </select>
        </div>

        <!-- Table -->
        <table class="table table-hover">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Book Title</th>
                    <th scope="col" class="text-center">Book ISBN</th>
                    <th scope="col" class="text-center">Request Date</th>
                    <th scope="col" class="text-center">Due Date</th>
                    <th scope="col" class="text-center">Status</th>
                </tr>
            </thead>
            <tbody id="borrowRequestsTable">
                <?php
                include("../../dB/config.php");

                $query = "SELECT ubr.id, ubr.user_id, b.title, ubr.ISBN, DATE(ubr.request_date) AS request_date, ubr.due_date, ubr.status 
                          FROM user_borrow_requests ubr
                          JOIN books b ON ubr.ISBN = b.isbn
                          WHERE ubr.user_id = ?
                          ORDER BY ubr.request_date DESC";

                if ($stmt = mysqli_prepare($conn, $query)) {
                    mysqli_stmt_bind_param($stmt, "i", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (mysqli_num_rows($result) > 0) {
                        $count = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr data-status='" . htmlspecialchars($row['status']) . "'>";
                            echo "<th scope='row'>" . $count . "</th>";
                            echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['ISBN']) . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['request_date']) . "</td>";
                            echo "<td class='text-center'>" . htmlspecialchars($row['due_date']) . "</td>";
                            echo "<td class='text-center'>";

                            // Apply Bootstrap badge styles based on status
                            if ($row['status'] === 'Approved') {
                                echo "<span class='badge border-primary border-1 text-primary'>Approved</span>";
                            } elseif ($row['status'] === 'Pending') {
                                echo "<span class='badge border-secondary border-1 text-secondary'>Pending</span>";
                            } elseif ($row['status'] === 'Rejected') {
                                echo "<span class='badge border-danger border-1 text-danger'>Rejected</span>";
                            } elseif ($row['status'] === 'Returned') {
                                echo "<span class='badge border-success border-1 text-success'>Returned</span>";
                            } else {
                                echo htmlspecialchars($row['status']);
                            }

                            echo "</td>";
                            echo "</tr>";
                            $count++;
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No records found.</td></tr>";
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    echo "<tr><td colspan='6' class='text-center text-danger'>Error fetching data. Please try again later.</td></tr>";
                }

                mysqli_close($conn);
                ?>
            </tbody>
        </table>

        <script>
            document.getElementById("statusFilter").addEventListener("change", function() {
                var selectedStatus = this.value;
                var rows = document.querySelectorAll("#borrowRequestsTable tr");

                rows.forEach(row => {
                    var status = row.getAttribute("data-status");
                    if (selectedStatus === "All" || status === selectedStatus) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                });
            });
        </script>

        <?php include("./includes/footer.php"); ?>
