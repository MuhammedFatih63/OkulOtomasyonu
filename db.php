
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "okul_otomasyon";

$conn = mysqli_connect("localhost", "root","","okul_otomasyon");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
