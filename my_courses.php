<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>كورساتي</title>
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

$sql = "SELECT r.id as reg_id, r.date, c.id as course_id, c.code, c.name_ar, c.credits
        FROM registrations r
        JOIN courses c ON r.course_id = c.id
        WHERE r.student_id = $student_id";
$result = $conn->query($sql);
?>

<nav class="navbar">
  <div class="nav-brand">CourseHub</div>
  <div class="nav-tabs">
    <a class="nav-tab" href="student.php">الرئيسية</a>
    <a class="nav-tab" href="available_courses.php">الكورسات</a>
    <a class="nav-tab active" href="my_courses.php">كورساتي</a>
  </div>
  <div class="nav-left">
    <span class="nav-username"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
    <a href="logout.php"><button class="btn-logout">خروج</button></a>
  </div>
</nav>

<main class="main">

  <?php if (isset($_GET['msg'])) { ?>
    <div class="alert-box alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
  <?php } ?>

  <div class="page-title">كورساتي</div>

  <?php if ($result->num_rows == 0) { ?>
    <div class="card">
      <div class="empty">
        لا يوجد كورسات مسجلة بعد<br><br>
        <a href="available_courses.php"><button class="btn btn-blue">تصفح الكورسات</button></a>
      </div>
    </div>
  <?php } else { ?>
    <div class="table-wrap">
      <table>
        <tr>
          <th>الكود</th>
          <th>الكورس</th>
          <th>الساعات</th>
          <th>التاريخ</th>
          <th>إجراء</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><span class="course-code"><?php echo htmlspecialchars($row['code']); ?></span></td>
            <td><?php echo htmlspecialchars($row['name_ar']); ?></td>
            <td><?php echo (int)$row['credits']; ?></td>
            <td><?php echo htmlspecialchars($row['date']); ?></td>
            <td>
              <a href="drop_course.php?reg_id=<?php echo $row['reg_id']; ?>" onclick="return confirm('هل أنت متأكد من الغاء تسجيلك في <?php echo htmlspecialchars($row['name_ar']); ?>؟')">
                <button class="btn btn-red btn-sm">الغاء التسجيل</button>
              </a>
            </td>
          </tr>
        <?php } ?>
      </table>
    </div>
  <?php } ?>

</main>
</body>
</html>
