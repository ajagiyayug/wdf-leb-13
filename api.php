<?php
header('Content-Type: application/json');
include 'db.php';

$method = $_SERVER['REQUEST_METHOD'];

switch($method) {
    case 'GET':
        // Fetch all students or single student
        if (isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $result = mysqli_query($conn, "SELECT * FROM students WHERE id = $id");
            $data = mysqli_fetch_assoc($result);
            echo json_encode($data ? $data : ["status" => false, "message" => "Student not found"]);
        } else {
            $result = mysqli_query($conn, "SELECT * FROM students ORDER BY id DESC");
            $students = [];
            while($row = mysqli_fetch_assoc($result)) {
                $students[] = $row;
            }
            echo json_encode($students);
        }
        break;

    case 'POST':
        // Add new student
        $data = json_decode(file_get_contents("php://input"), true);
        $name = mysqli_real_escape_string($conn, $data['name'] ?? '');
        $email = mysqli_real_escape_string($conn, $data['email'] ?? '');
        $course = mysqli_real_escape_string($conn, $data['course'] ?? '');

        if (!empty($name) && !empty($email) && !empty($course)) {
            $query = "INSERT INTO students (name, email, course) VALUES ('$name', '$email', '$course')";
            if (mysqli_query($conn, $query)) {
                echo json_encode(["status" => true, "message" => "Student added successfully!"]);
            } else {
                echo json_encode(["status" => false, "message" => "Failed to add student"]);
            }
        } else {
            echo json_encode(["status" => false, "message" => "All fields are required!"]);
        }
        break;

    case 'PUT':
        // Update student
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id'] ?? 0);
        $name = mysqli_real_escape_string($conn, $data['name'] ?? '');
        $email = mysqli_real_escape_string($conn, $data['email'] ?? '');
        $course = mysqli_real_escape_string($conn, $data['course'] ?? '');

        if ($id > 0 && !empty($name) && !empty($email) && !empty($course)) {
            $query = "UPDATE students SET name='$name', email='$email', course='$course' WHERE id=$id";
            if (mysqli_query($conn, $query)) {
                echo json_encode(["status" => true, "message" => "Student updated successfully!"]);
            } else {
                echo json_encode(["status" => false, "message" => "Failed to update student"]);
            }
        } else {
            echo json_encode(["status" => false, "message" => "Invalid input data!"]);
        }
        break;

    case 'DELETE':
        // Delete student
        $data = json_decode(file_get_contents("php://input"), true);
        $id = intval($data['id'] ?? 0);

        if ($id > 0) {
            $query = "DELETE FROM students WHERE id = $id";
            if (mysqli_query($conn, $query)) {
                echo json_encode(["status" => true, "message" => "Student deleted successfully!"]);
            } else {
                echo json_encode(["status" => false, "message" => "Failed to delete student"]);
            }
        } else {
            echo json_encode(["status" => false, "message" => "Invalid student ID!"]);
        }
        break;

    default:
        echo json_encode(["status" => false, "message" => "Invalid Request Method"]);
        break;
}
?>