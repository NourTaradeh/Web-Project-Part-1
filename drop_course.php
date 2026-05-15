<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

include("db.php");

$student_id = $_SESSION['user_id'];
$reg_id = $_GET['reg_id'];

$check = $conn->query("SELECT id FROM registrations WHERE id = $reg_id AND student_id = $student_id");
if ($check->num_rows == 0) {
    header("Location: my_courses.php");
    exit();
}

$sql = "DELETE FROM registrations WHERE id = $reg_id";
$result = $conn->query($sql);

if ($result === TRUE) {
    header("Location: my_courses.php?msg=تم الغاء التسجيل بنجاح");
} else {
    header("Location: my_courses.php?err=حدث خطأ");
}
exit();
?>
