<?php

$hostName="localhost";
$dbUser="root";
$dbPassword="";
$dbName="doctor";
$conn=mysqli_connect($hostName,$dbUser,$dbPassword,$dbName);

if(!$conn){
     die("Failed to Connect");
}
?>