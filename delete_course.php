<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

$id = (int)$_GET['id'];

$stmt = $conn->prepare("DELETE FROM course_prereqs WHERE course_id = ? OR prereq_id = ?");
$stmt->bind_param("ii", $id, $id);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM registrations WHERE course_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: courses.php?msg=تم حذف الكورس بنجاح");
} else {
    header("Location: courses.php?err=حدث خطأ أثناء الحذف");
}
exit();
?>
