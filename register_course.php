<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

include("db.php");

$student_id = (int)$_SESSION['user_id'];
$course_id = (int)$_GET['course_id'];

$check = $conn->prepare("SELECT id FROM registrations WHERE student_id = ? AND course_id = ?");
$check->bind_param("ii", $student_id, $course_id);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    header("Location: available_courses.php?err=انت مسجل في هذا الكورس مسبقا");
    exit();
}

$stmt = $conn->prepare("SELECT capacity FROM courses WHERE id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();

$stmt2 = $conn->prepare("SELECT COUNT(*) as cnt FROM registrations WHERE course_id = ?");
$stmt2->bind_param("i", $course_id);
$stmt2->execute();
$cnt = $stmt2->get_result()->fetch_assoc()['cnt'];

if ($cnt >= $course['capacity']) {
    header("Location: available_courses.php?err=الكورس مكتمل");
    exit();
}

$comp_stmt = $conn->prepare("SELECT course_id FROM completed_courses WHERE student_id = ?");
$comp_stmt->bind_param("i", $student_id);
$comp_stmt->execute();
$comp_result = $comp_stmt->get_result();
$completed_ids = [];
while ($row = $comp_result->fetch_assoc()) {
    $completed_ids[] = $row['course_id'];
}

$pre_stmt = $conn->prepare("SELECT prereq_id FROM course_prereqs WHERE course_id = ?");
$pre_stmt->bind_param("i", $course_id);
$pre_stmt->execute();
$prereqs_result = $pre_stmt->get_result();
while ($p = $prereqs_result->fetch_assoc()) {
    if (!in_array($p['prereq_id'], $completed_ids)) {
        header("Location: available_courses.php?err=لم تكتمل المتطلبات السابقة");
        exit();
    }
}

$date = date('Y-m-d');
$ins = $conn->prepare("INSERT INTO registrations (student_id, course_id, date) VALUES (?, ?, ?)");
$ins->bind_param("iis", $student_id, $course_id, $date);

if ($ins->execute()) {
    header("Location: available_courses.php?msg=تم التسجيل بنجاح");
} else {
    header("Location: available_courses.php?err=حدث خطأ أثناء التسجيل");
}
exit();
?>
