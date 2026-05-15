<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

$id = $_GET['id'];

$conn->query("DELETE FROM course_prereqs WHERE course_id = $id OR prereq_id = $id");

$conn->query("DELETE FROM registrations WHERE course_id = $id");

$sql = "DELETE FROM courses WHERE id = $id";
$result = $conn->query($sql);

if ($result === TRUE) {
    header("Location: courses.php?msg=تم حذف الكورس بنجاح");
} else {
    header("Location: courses.php?err=حدث خطأ أثناء الحذف");
}
exit();
?>
