<?php
include("../dB/config.php");
session_start();

if(isset($_POST['registration'])){
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPass = $_POST['confirmPass'];
    $phoneNumber = $_POST['phoneNumber'];
    $gender = $_POST['gender'];
    $birthday = $_POST['birthday'];
    $role = $_POST['role'];


    $_SESSION['form_data'] = $_POST;

// Validate if confirm password and password match
if($password != $confirmPass){
    $_SESSION['message'] = "Password and Confirm Password do not match";
    $_SESSION['code'] = "error";
    header("Location: ../registration.php");
    exit(0);
}


// Validate if email already exists

// $query = "SELECT * FROM `users` WHERE `email` = '$email'";
$query = "SELECT email FROM users WHERE email = '$email' 
          UNION 
          SELECT email FROM admins WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if(mysqli_num_rows($result) > 0){
    $_SESSION['message'] = "Email already exists";
    $_SESSION['code'] = "error";
    header("Location: ../registration.php");
    exit(0);
}

// Insert our data into the database
$query = "INSERT INTO `users` (`firstName`, `lastName`, `email`, `password`, `phoneNumber`, `gender`, `birthday`, `role`) 
VALUES ('$firstName', '$lastName', '$email','$password','$phoneNumber','$gender','$birthday', 'user')";

if (mysqli_query($conn, $query)){
    $_SESSION['message'] = "lets go";
    $_SESSION['code'] = "success";
    header("Location: ../login.php");
    exit(0);
}else{
    echo "Error:" . mysqli_error($conn);
}

mysqli_close($conn);
}
?>