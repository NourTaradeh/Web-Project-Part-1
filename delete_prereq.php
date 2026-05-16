<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

$course_id = (int)$_GET['course_id'];
$prereq_id = (int)$_GET['prereq_id'];

$stmt = $conn->prepare("DELETE FROM course_prereqs WHERE course_id = ? AND prereq_id = ?");
$stmt->bind_param("ii", $course_id, $prereq_id);
$stmt->execute();

header("Location: prereqs.php?course_id=$course_id&msg=تم حذف المتطلب");
exit();
?>
