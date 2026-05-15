<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>لوحة المدير - نظام الكورسات</title>
  <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include("db.php");

$students_result = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'student'");
$students_count = $students_result->fetch_assoc()['cnt'];

$courses_result = $conn->query("SELECT COUNT(*) as cnt FROM courses");
$courses_count = $courses_result->fetch_assoc()['cnt'];

$regs_result = $conn->query("SELECT COUNT(*) as cnt FROM registrations");
$regs_count = $regs_result->fetch_assoc()['cnt'];

$full_result = $conn->query("SELECT COUNT(*) as cnt FROM courses c WHERE (SELECT COUNT(*) FROM registrations r WHERE r.course_id = c.id) >= c.capacity");
$full_count = $full_result->fetch_assoc()['cnt'];
?>

<nav class="navbar">
  <div class="nav-brand">CourseHub</div>
  <div class="nav-tabs">
    <a class="nav-tab active" href="admin.php">الرئيسية</a>
    <a class="nav-tab" href="students.php">الطلاب</a>
    <a class="nav-tab" href="courses.php">الكورسات</a>
    <a class="nav-tab" href="prereqs.php">المتطلبات</a>
    <a class="nav-tab" href="registrations.php">التسجيلات</a>
  </div>
  <div class="nav-left">
    <span class="nav-username"><?php echo $_SESSION['name']; ?></span>
    <a href="logout.php"><button class="btn-logout">خروج</button></a>
  </div>
</nav>

<main class="main">
  <div class="page-title">لوحة المدير</div>

  <div class="dash-stats">
    <div class="dash-stat-box">
      <div class="dash-stat-num"><?php echo $students_count; ?></div>
      <div class="dash-stat-label">عدد الطلاب</div>
    </div>
    <div class="dash-stat-box">
      <div class="dash-stat-num"><?php echo $courses_count; ?></div>
      <div class="dash-stat-label">عدد الكورسات</div>
    </div>
    <div class="dash-stat-box">
      <div class="dash-stat-num"><?php echo $regs_count; ?></div>
      <div class="dash-stat-label">عدد التسجيلات</div>
    </div>
    <div class="dash-stat-box">
      <div class="dash-stat-num"><?php echo $full_count; ?></div>
      <div class="dash-stat-label">كورسات ممتلئة</div>
    </div>
  </div>
</main>

</body>
</html>
