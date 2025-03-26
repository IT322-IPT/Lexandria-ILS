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
include("../../dB/config.php"); // Adjust the path to your database config file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $book_title = $_POST['book_title'] ?? '';
    $author = $_POST['author'] ?? '';
    $isbn = $_POST['isbn'] ?? NULL;
    $reason = $_POST['reason'] ?? '';

    // Validate required fields
    if (empty($name) || empty($email) || empty($book_title) || empty($author) || empty($reason)) {
        echo "Error: All fields except ISBN are required.";
        exit;
    }
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    // Insert into database
    $query = "INSERT INTO book_requests (name, email, book_title, author, isbn, reason) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $query);

    if (!$stmt) {
        die("SQL Error: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt, "ssssss", $name, $email, $book_title, $author, $isbn, $reason);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success_message'] = "Your request has been submitted!";
        header("Location: pages-request.php");
        exit();
    } else {
        echo "Error submitting request: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
