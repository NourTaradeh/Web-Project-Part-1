<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

$course_id = $_GET['course_id'];
$prereq_id = $_GET['prereq_id'];

$conn->query("DELETE FROM course_prereqs WHERE course_id = $course_id AND prereq_id = $prereq_id");

header("Location: prereqs.php?course_id=$course_id&msg=تم حذف المتطلب");
exit();
?>
