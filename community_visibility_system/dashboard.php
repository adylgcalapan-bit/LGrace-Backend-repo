<?php

session_start();

if(!isset($_SESSION['user_id'])){
header("Location: login.php");
exit();
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Dashboard</title>

</head>

<body>

<h2>Welcome,
<?php echo $_SESSION['fullname']; ?>
</h2>

<button onclick="getLocation()">
Save My Location
</button>

<br><br>

<a href="logout.php">
Logout
</a>

<script>

function getLocation(){

if(navigator.geolocation){

navigator.geolocation.getCurrentPosition(showPosition);

}else{

alert("Geolocation not supported");

}

}

function showPosition(position){

var latitude=position.coords.latitude;
var longitude=position.coords.longitude;

var xhr=new XMLHttpRequest();

xhr.open("POST","save_location.php",true);

xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded");

xhr.send("latitude="+latitude+"&longitude="+longitude);

alert("Location Saved!");

}

</script>

</body>

</html>