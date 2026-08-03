<?php
include "config.php";

if(isset($_POST['register'])){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users(fullname,email,password)
            VALUES('$fullname','$email','$password')";

    if($conn->query($sql)){
        echo "Registered Successfully!";
    }else{
        echo "Error!";
    }

}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
</head>
<body>

<h2>Register</h2>

<form method="POST">

<input type="text" name="fullname" placeholder="Full Name" required>

<br><br>

<input type="email" name="email" placeholder="Email" required>

<br><br>

<input type="password" name="password" placeholder="Password" required>

<br><br>

<button name="register">Register</button>

</form>

</body>
</html>