<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
</head>
<?php
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
include("./includes/sidebar.php");

function fetchData($conn, $query) {
    $result = mysqli_query($conn, $query) or die("Query Failed: " . mysqli_error($conn));
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    mysqli_free_result($result);
    return $data;
}

// Most Borrowed Books
$borrowedBooksData = fetchData($conn, "SELECT b.title, COUNT(bb.id) AS borrow_count FROM borrowed_books bb JOIN books b ON bb.isbn = b.isbn GROUP BY b.title ORDER BY borrow_count DESC LIMIT 10");
$bookTitles = array_column($borrowedBooksData, 'title');
$borrowCounts = array_column($borrowedBooksData, 'borrow_count');

// Most Borrowed Books by Genre
$genreData = fetchData($conn, "SELECT b.genre, COUNT(bb.id) AS borrow_count FROM borrowed_books bb JOIN books b ON bb.isbn = b.isbn GROUP BY b.genre ORDER BY borrow_count DESC");
$genres = array_column($genreData, 'genre');
$genreCounts = array_column($genreData, 'borrow_count');

// Top Borrowers
$topBorrowersData = fetchData($conn, "SELECT CONCAT(u.firstName, ' ', u.lastName) AS fullName, COUNT(bb.id) AS borrow_count FROM borrowed_books bb JOIN users u ON bb.user_id = u.userId GROUP BY fullName ORDER BY borrow_count DESC LIMIT 10");
$userNames = array_column($topBorrowersData, 'fullName');
$userBorrowCounts = array_column($topBorrowersData, 'borrow_count');

// Most Requested Books
$requestedBooksData = fetchData($conn, "SELECT book_title, COUNT(id) AS request_count FROM book_requests GROUP BY book_title ORDER BY request_count DESC LIMIT 10");
$requestTitles = array_column($requestedBooksData, 'book_title');
$requestCounts = array_column($requestedBooksData, 'request_count');

// Fetch top 3 most requested books
$requestedBooksData = fetchData($conn, "
    SELECT book_title, author, COUNT(id) AS request_count 
    FROM book_requests 
    GROUP BY book_title 
    ORDER BY request_count DESC 
    LIMIT 3
");

// Top 3 Most Frequently Borrowed Books
$query = "
    SELECT b.title, b.author, COUNT(bb.id) AS borrow_count
    FROM borrowed_books bb
    JOIN books b ON bb.isbn = b.isbn
    GROUP BY b.title, b.author
    ORDER BY borrow_count DESC
    LIMIT 3
";
$result = mysqli_query($conn, $query);
$top_books = mysqli_fetch_all($result, MYSQLI_ASSOC); // Store data in an array

?>

<body>
    <section class="section dashboard">

        <!-- Top-most section inside a card -->
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-primary fw-bold mb-4">📚 Top 3 Most Frequently Borrowed Books</h5>
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-light">
                                <tr class="text-center">
                                    <th scope="col">Preview</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Author</th>
                                    <th scope="col">Times Borrowed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_books as $row) { ?>
                                    <tr class="text-center">
                                        <td>
                                            <a href="#">
                                                <img src="../../assets/img/books/<?= rawurlencode($row['title']) ?>.jpg"
                                                    class="img-thumbnail rounded-3 shadow-sm"
                                                    alt="<?= htmlspecialchars($row['title']) ?>"
                                                    onerror="this.src='../../assets/img/books/default.jpg'"
                                                    style="width: 60px; height: 90px; object-fit: cover;">
                                            </a>
                                        </td>
                                        <td>
                                            <a href="#" class="text-decoration-none fw-semibold text-dark"
                                                style="transition: color 0.3s ease-in-out;"
                                                onmouseover="this.style.color='#007bff'"
                                                onmouseout="this.style.color='#000'">
                                                <?= htmlspecialchars($row['title']) ?>
                                            </a>
                                        </td>
                                        <td class="text-muted"><?= htmlspecialchars($row['author']) ?></td>
                                        <td class="fw-bold text-primary fs-5"><?= $row['borrow_count'] ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <?php foreach ($requestedBooksData as $index => $book) : ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="filter">
                            <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                <li class="dropdown-header text-start">
                                    <h6>Filter</h6>
                                </li>
                                <li><a class="dropdown-item" href="#">Today</a></li>
                                <li><a class="dropdown-item" href="#">This Month</a></li>
                                <li><a class="dropdown-item" href="#">This Year</a></li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($book['book_title']); ?> <span>| <?php echo htmlspecialchars($book['author']); ?></span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-book"></i>
                                </div>
                                <div class="ps-3">
                                    <h6><?php echo $book['request_count']; ?> Requests</h6>
                                    <span class="text-muted small pt-2">Top 3 Most Hightly Requested</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="row d-flex justify-content-between">
            <!-- Left side section -->
            <div class="col-lg-7">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Most Borrowed Books</h5>
                                <div id="borrowedBooksChart"></div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        new ApexCharts(document.querySelector("#borrowedBooksChart"), {
                                            series: [{ name: "Times Borrowed", data: <?= json_encode($borrowCounts) ?> }],
                                            chart: { type: 'bar', height: 350 },
                                            xaxis: { categories: <?= json_encode($bookTitles) ?> },
                                            dataLabels: { enabled: false }
                                        }).render();
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Top Users Who Borrowed the Most Books</h5>
                                <div id="topUsersChart"></div>
                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        new ApexCharts(document.querySelector("#topUsersChart"), {
                                            series: [{ name: "Books Borrowed", data: <?= json_encode($userBorrowCounts) ?> }],
                                            chart: { type: 'bar', height: 350 },
                                            xaxis: { categories: <?= json_encode($userNames) ?> },
                                            dataLabels: { enabled: false }
                                        }).render();
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
            </div>
            </div>

            <!-- Right side section -->
            <div class="col-lg-5">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Most Borrowed Books by Genre</h5>
                                <canvas id="genreChart"></canvas>
                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        new Chart(document.querySelector("#genreChart"), {
                                            type: 'pie',
                                            data: {
                                                labels: <?= json_encode($genres) ?>,
                                                datasets: [{ data: <?= json_encode($genreCounts) ?>, backgroundColor: ['red', 'blue', 'yellow', 'green', 'purple', 'orange'] }]
                                            }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Most Requested Books</h5>
                                <canvas id="requestedBooksChart"></canvas>
                                <script>
                                    document.addEventListener("DOMContentLoaded", () => {
                                        new Chart(document.querySelector("#requestedBooksChart"), {
                                            type: 'bar',
                                            data: {
                                                labels: <?= json_encode($requestTitles) ?>,
                                                datasets: [{ data: <?= json_encode($requestCounts) ?>, backgroundColor: 'rgba(54, 162, 235, 0.5)', borderColor: 'rgb(54, 162, 235)', borderWidth: 1 }]
                                            },
                                            options: { responsive: true, scales: { y: { beginAtZero: true } } }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        


    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php include("./includes/footer.php"); 
        if(isset($_SESSION['message']) && $_SESSION['code'] !='') {
            ?>
            <script>
              const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                  toast.onmouseenter = Swal.stopTimer;
                  toast.onmouseleave = Swal.resumeTimer;
                }
              });
              Toast.fire({
                icon: "<?php echo $_SESSION['code']; ?>",
                title: "<?php echo $_SESSION['message']; ?>"
              });
            </script>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['code']);
        }?>
</body>