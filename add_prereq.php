<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

$course_id = $_GET['course_id'];
$prereq_id = $_GET['prereq_id'];

if ($prereq_id == '') {
    header("Location: prereqs.php?course_id=$course_id");
    exit();
}

// تحقق ما اضيف مسبقا
$check = $conn->query("SELECT id FROM course_prereqs WHERE course_id = $course_id AND prereq_id = $prereq_id");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO course_prereqs (course_id, prereq_id) VALUES ($course_id, $prereq_id)");
}

header("Location: prereqs.php?course_id=$course_id&msg=تم إضافة المتطلب بنجاح");
exit();
?>
