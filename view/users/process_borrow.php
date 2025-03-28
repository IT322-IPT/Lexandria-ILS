<?php
session_start();
include("../../dB/config.php");
if (!isset($_SESSION["authUser"])) {
    header("Location: ../../../IT322/login.php");
    exit();
  }
  // Prevent browser caching
  header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");

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

        //     $query = "INSERT INTO borrow_requests (user_id, full_name, email, isbn, borrow_date, return_date) 
        //             VALUES (?, ?, ?, ?, ?, ?)";

        //     $stmt = mysqli_prepare($conn, $query);

        //     if (!$stmt) {
        //         die("SQL Error: " . mysqli_error($conn));
        //     }

        //     mysqli_stmt_bind_param($stmt, "isssss", $userId, $fullName, $email, $isbn, $borrowDate, $returnDate);

        //     if (mysqli_stmt_execute($stmt)) {
        //         $_SESSION['show_modal'] = true;
        //         header("Location: pages-borrow.php");
        //         exit();
        //     }
            
        //     mysqli_stmt_close($stmt);
        //     mysqli_close($conn);
        // }

        // Insert into borrow_requests table
        $query1 = "INSERT INTO borrow_requests (user_id, full_name, email, isbn, borrow_date, return_date) 
        VALUES (?, ?, ?, ?, ?, ?)";

        $stmt1 = mysqli_prepare($conn, $query1);

        if (!$stmt1) {
        die("SQL Error: " . mysqli_error($conn));
        }

        mysqli_stmt_bind_param($stmt1, "isssss", $userId, $fullName, $email, $isbn, $borrowDate, $returnDate);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);

        // Insert into user_borrow_requests table
        $query2 = "INSERT INTO user_borrow_requests (user_id, ISBN, request_date, due_date, status) VALUES (?, ?, ?, ?, 'Pending')";


        $stmt2 = mysqli_prepare($conn, $query2);

        if (!$stmt2) {
            die("SQL Error: " . mysqli_error($conn));
        }
    
        mysqli_stmt_bind_param($stmt2, "isss", $userId, $isbn, $borrowDate, $returnDate);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    
        mysqli_close($conn);
    
        // Redirect after successful request
        $_SESSION['show_success'] = true;
        header("Location: pages-borrow.php");
        exit();
}

?>
