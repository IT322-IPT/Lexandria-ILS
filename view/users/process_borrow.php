<?php
session_start();
include("../../dB/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userId = isset($_POST['userId']) ? (int) $_POST['userId'] : 0;
    $fullName = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $isbn = $_POST['isbn'] ?? '';
    $borrowDate = $_POST['borrowDate'] ?? date("Y-m-d");
    $returnDate = date("Y-m-d", strtotime($borrowDate . " +14 days"));

    // Debugging: Print all received data
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";

    if ($userId === 0 || empty($fullName) || empty($email) || empty($isbn)) {
        echo "Error: All fields are required.";
        exit;
    }

    $query = "INSERT INTO borrow_requests (user_id, full_name, email, isbn, borrow_date, return_date) 
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die("SQL Error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "isssss", $userId, $fullName, $email, $isbn, $borrowDate, $returnDate);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['show_modal'] = true;
        header("Location: pages-borrow.php");
        exit();
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

    
?>
