<?php
session_start();
include("../dB/config.php");

if(isset($_POST["login"])) {

    $email = $_POST["email"];
    $password = $_POST["password"];

    // $query = "SELECT `userId`, `firstName`, `lastName`, `email`, `password`, `phoneNumber`, `gender`, `birthday`, `verification`, `role` 
    // FROM `users` WHERE email = '$email' AND password = '$password' LIMIT 1;";

    $query = "SELECT `userId`, `firstName`, `lastName`, `email`, `password`, `phoneNumber`, `gender`, `birthday`, `verification`, `role` 
          FROM `users` 
          WHERE email = '$email' AND password = '$password' 
          
          UNION 

          SELECT `adminId` AS userId, `firstName`, `lastName`, `email`, `password`, `phoneNumber`, `gender`, `birthday`, 'verified' AS verification, 'admin' AS role
          FROM `admins`
          WHERE email = '$email' AND password = '$password' 
          
          LIMIT 1;";
          
    $query_run = mysqli_query($conn, $query);

    if($query_run) {
        if(mysqli_num_rows($query_run) > 0) {
            $data = mysqli_fetch_assoc($query_run);

            $userID = $data["userId"];
            $fullname = $data["firstName"]." ".$data["lastName"];
            $email = $data["email"];
            $userRole = $data["role"];

            $_SESSION["auth"] = true;
            $_SESSION["role"] = $userRole;
            $_SESSION["authUser"] = [
                'userId' => $userID,
                'fullName' => $fullname,
                'email' => $email,
            ];

            $_SESSION['message'] = "Successfully logged in";
            $_SESSION['code'] = "success";

            if($userRole == 'admin'){
                header("Location: ../view/admin/index.php");
            } else if ($userRole == 'user'){
                // header("Location: ../login.php");
                header("Location: ../view/users/index.php");
            } else {
                $_SESSION['message'] = "No role found";
                $_SESSION['code'] = "error";
                header("Location: ../login.php");
            }
            exit();
        } else {
            $_SESSION['message'] = "sum ting wong";
            $_SESSION['code'] = "error";
            header("Location: ../login.php");
            exit();
        }
    } else {
        $_SESSION['message'] = "Please try again";
        $_SESSION['code'] = "error";
        header("Location: ../login.php");
        exit();
    } 
}
?>