<?php
session_start();
include("../../dB/config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    $admin_id = $_SESSION["authUser"]["userId"];
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

        $query = "SELECT password FROM admins WHERE adminId = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "i", $admin_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if ($current_password !== $row['password']) {
                $errors[] = "Current password is incorrect.";
            }
        } else {
            $errors[] = "User not found.";
        }
        mysqli_stmt_close($stmt);
    }

    if (empty($errors)) {
        $query = "UPDATE admins SET ";
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

        if (!empty($updates)) {
            $query .= implode(", ", $updates) . " WHERE adminId = ?";
            $params[] = $admin_id;
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
                    $_SESSION["authUser"]["password"] = $new_password;
                }

                $_SESSION['alertMessage'] = "Account updated successfully.";
                $_SESSION['alertType'] = "success";
            } else {
                $_SESSION['alertMessage'] = "Error updating account.";
                $_SESSION['alertType'] = "danger";
            }
            mysqli_stmt_close($stmt);
        }
    } else {
        $_SESSION['alertMessage'] = implode("<br>", $errors);
        $_SESSION['alertType'] = "danger";
    }

    header("Location: admin-profile.php");
    exit();
}
?>
