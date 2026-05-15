<?php
session_start();
include("db.php");

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

if ($search != '') {
    $sql = "SELECT r.*, u.name as student_name, u.student_num, c.code as course_code, c.name_ar as course_name
            FROM registrations r
            JOIN users u ON r.student_id = u.id
            JOIN courses c ON r.course_id = c.id
            WHERE u.name LIKE '%$search%' OR u.student_num LIKE '%$search%' OR c.code LIKE '%$search%' OR c.name_ar LIKE '%$search%'";
} else {
    $sql = "SELECT r.*, u.name as student_name, u.student_num, c.code as course_code, c.name_ar as course_name
            FROM registrations r
            JOIN users u ON r.student_id = u.id
            JOIN courses c ON r.course_id = c.id";
}

$result = $conn->query($sql);
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}

echo json_encode($rows);
?>
