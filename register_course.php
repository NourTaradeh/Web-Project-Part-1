<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

include("db.php");

$student_id = (int)$_SESSION['user_id'];
$course_id = (int)$_GET['course_id'];

$check = $conn->query("SELECT id FROM registrations WHERE student_id = $student_id AND course_id = $course_id");
if ($check->num_rows > 0) {
    header("Location: available_courses.php?err=انت مسجل في هذا الكورس مسبقا");
    exit();
}

$course_result = $conn->query("SELECT * FROM courses WHERE id = $course_id");
$course = $course_result->fetch_assoc();
$cnt_result = $conn->query("SELECT COUNT(*) as cnt FROM registrations WHERE course_id = $course_id");
$cnt = $cnt_result->fetch_assoc()['cnt'];

if ($cnt >= $course['capacity']) {
    header("Location: available_courses.php?err=الكورس مكتمل");
    exit();
}

$comp_result = $conn->query("SELECT course_id FROM completed_courses WHERE student_id = $student_id");
$completed_ids = [];
while ($row = $comp_result->fetch_assoc()) {
    $completed_ids[] = $row['course_id'];
}

$prereqs_result = $conn->query("SELECT prereq_id FROM course_prereqs WHERE course_id = $course_id");
while ($p = $prereqs_result->fetch_assoc()) {
    if (!in_array($p['prereq_id'], $completed_ids)) {
        header("Location: available_courses.php?err=لم تكتمل المتطلبات السابقة");
        exit();
    }
}

$date = date('Y-m-d');
$sql = "INSERT INTO registrations (student_id, course_id, date) VALUES ($student_id, $course_id, '$date')";
$result = $conn->query($sql);

if ($result === TRUE) {
    header("Location: available_courses.php?msg=تم التسجيل بنجاح");
} else {
    header("Location: available_courses.php?err=حدث خطأ أثناء التسجيل");
}
exit();
?>
