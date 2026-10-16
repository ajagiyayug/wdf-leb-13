<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "api_student_db";

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(["status" => false, "message" => "Database Connection Failed"]);
    exit();
}
?>