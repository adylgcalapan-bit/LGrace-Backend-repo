<?php

$conn = new mysqli("localhost","root","","community_visibility_system");

if($conn->connect_error){
    die("Connection Failed: ".$conn->connect_error);
}

?>