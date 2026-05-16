<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

$id = (int)$_GET['id'];

$stmt = $conn->prepare("DELETE FROM registrations WHERE student_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: students.php?msg=تم حذف الطالب بنجاح");
} else {
    header("Location: students.php?err=حدث خطأ أثناء الحذف");
}
exit();
?>
