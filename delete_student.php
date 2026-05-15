<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

$id = (int)$_GET['id'];

$conn->query("DELETE FROM registrations WHERE student_id = $id");

$sql = "DELETE FROM users WHERE id = $id";
$result = $conn->query($sql);

if ($result === TRUE) {
    header("Location: students.php?msg=تم حذف الطالب بنجاح");
} else {
    header("Location: students.php?err=حدث خطأ أثناء الحذف");
}
exit();
?>
