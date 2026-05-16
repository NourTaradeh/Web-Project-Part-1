<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

$course_id = (int)$_GET['course_id'];
$prereq_id = (int)$_GET['prereq_id'];

if ($prereq_id == 0) {
    header("Location: prereqs.php?course_id=$course_id");
    exit();
}

$check = $conn->prepare("SELECT id FROM course_prereqs WHERE course_id = ? AND prereq_id = ?");
$check->bind_param("ii", $course_id, $prereq_id);
$check->execute();
$check->store_result();

if ($check->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO course_prereqs (course_id, prereq_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $course_id, $prereq_id);
    $stmt->execute();
}

header("Location: prereqs.php?course_id=$course_id&msg=تم إضافة المتطلب بنجاح");
exit();
?>
