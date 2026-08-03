<?php
session_start();
include "config.php";

// Check if user is logged in
if(!isset($_SESSION['user_id'])){
    echo "User not logged in";
    exit();
}

// Check if latitude and longitude are sent
if(isset($_POST['latitude']) && isset($_POST['longitude'])){

    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $user_id = $_SESSION['user_id'];

    // Update user's location
    $sql = "UPDATE users
            SET latitude='$latitude',
                longitude='$longitude'
            WHERE id='$user_id'";

    if($conn->query($sql)){
        echo "Location saved successfully!";
    }else{
        echo "Error: ".$conn->error;
    }

}else{
    echo "No location received.";
}
?>