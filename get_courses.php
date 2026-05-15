<?php
session_start();
include("db.php");

$sql = "SELECT c.*, (SELECT COUNT(*) FROM registrations r WHERE r.course_id = c.id) as regs_count FROM courses c";
$result = $conn->query($sql);

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);
?>
