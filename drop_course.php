<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

include("db.php");

$student_id = (int)$_SESSION['user_id'];
$reg_id = (int)$_GET['reg_id'];

$check = $conn->prepare("SELECT id FROM registrations WHERE id = ? AND student_id = ?");
$check->bind_param("ii", $reg_id, $student_id);
$check->execute();
$check->store_result();

if ($check->num_rows == 0) {
    header("Location: my_courses.php");
    exit();
}

$stmt = $conn->prepare("DELETE FROM registrations WHERE id = ?");
$stmt->bind_param("i", $reg_id);

if ($stmt->execute()) {
    header("Location: my_courses.php?msg=تم الغاء التسجيل بنجاح");
} else {
    header("Location: my_courses.php?err=حدث خطأ");
}
exit();
?>
