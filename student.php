<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>صفحة الطالب - نظام الكورسات</title>
  <link rel="stylesheet" href="student.css">
</head>
<body>

<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: login.php");
    exit();
}

include("db.php");

$student_id = $_SESSION['user_id'];

$my_regs = $conn->query("SELECT COUNT(*) as cnt FROM registrations WHERE student_id = $student_id");
$my_regs_count = $my_regs->fetch_assoc()['cnt'];

$all_courses = $conn->query("SELECT COUNT(*) as cnt FROM courses");
$all_courses_count = $all_courses->fetch_assoc()['cnt'];

$completed = $conn->query("SELECT COUNT(*) as cnt FROM completed_courses WHERE student_id = $student_id");
$completed_count = $completed->fetch_assoc()['cnt'];
?>

<nav class="navbar">
  <div class="nav-brand">CourseHub</div>
  <div class="nav-tabs">
    <a class="nav-tab active" href="student.php">الرئيسية</a>
    <a class="nav-tab" href="available_courses.php">الكورسات</a>
    <a class="nav-tab" href="my_courses.php">كورساتي</a>
  </div>
  <div class="nav-left">
    <span class="nav-username"><?php echo $_SESSION['name']; ?></span>
    <a href="logout.php"><button class="btn-logout">خروج</button></a>
  </div>
</nav>

<main class="main">
  <div class="page-title">مرحباً، <?php echo $_SESSION['name']; ?></div>

  <div class="dash-stats">
    <div class="dash-stat-box">
      <div class="dash-stat-num"><?php echo $my_regs_count; ?></div>
      <div class="dash-stat-label">كورسات مسجلة</div>
    </div>
    <div class="dash-stat-box">
      <div class="dash-stat-num"><?php echo $all_courses_count; ?></div>
      <div class="dash-stat-label">كورسات متاحة</div>
    </div>
    <div class="dash-stat-box">
      <div class="dash-stat-num"><?php echo $completed_count; ?></div>
      <div class="dash-stat-label">كورسات مكتملة</div>
    </div>
  </div>
</main>

</body>
</html>
