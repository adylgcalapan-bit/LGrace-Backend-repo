<?php

session_start();
include "config.php";

if(isset($_POST['login'])){

$email=$_POST['email'];
$password=$_POST['password'];

$sql="SELECT * FROM users WHERE email='$email'";

$result=$conn->query($sql);

if($result->num_rows>0){

$user=$result->fetch_assoc();

if(password_verify($password,$user['password'])){

$_SESSION['user_id']=$user['id'];
$_SESSION['fullname']=$user['fullname'];

header("Location: dashboard.php");
exit();

}else{

echo "Wrong Password";

}

}else{

echo "User not found";

}

}

?>

<!DOCTYPE html>

<html>

<head>

<title>Login</title>

</head>

<body>

<h2>Login</h2>

<form method="POST">

Email

<input type="email" name="email" required>

<br><br>

Password

<input type="password" name="password" required>

<br><br>

<input type="submit" name="login" value="Login">

</form>

<br>

<a href="register.php">Create Account</a>

</body>

</html>