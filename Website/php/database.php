<?php
$host = "localhost";
$dbname = "chopz";
$username = "root";
$password = "";

$mysqli = new mysqli($host,$username,$password,$dbname);
$conn = mysqli_connect($host,$username,$password,$dbname);

if($mysqli -> connect_error){
    die("Connection Error: " . $mysqli -> connect_error);
}
return $mysqli;
?>