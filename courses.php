<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الكورسات - نظام الكورسات</title>
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

$sql = "SELECT c.*, (SELECT COUNT(*) FROM registrations r WHERE r.course_id = c.id) as regs_count FROM courses c";
$result = $conn->query($sql);
?>

<nav class="navbar">
  <div class="nav-brand">CourseHub</div>
  <div class="nav-tabs">
    <a class="nav-tab" href="admin.php">الرئيسية</a>
    <a class="nav-tab" href="students.php">الطلاب</a>
    <a class="nav-tab active" href="courses.php">الكورسات</a>
    <a class="nav-tab" href="prereqs.php">المتطلبات</a>
    <a class="nav-tab" href="registrations.php">التسجيلات</a>
  </div>
  <div class="nav-left">
    <span class="nav-username"><?php echo $_SESSION['name']; ?></span>
    <a href="logout.php"><button class="btn-logout">خروج</button></a>
  </div>
</nav>

<main class="main">

  <?php if (isset($_GET['msg'])) { ?>
    <div class="alert-box alert-success"><?php echo $_GET['msg']; ?></div>
  <?php } ?>
  <?php if (isset($_GET['err'])) { ?>
    <div class="alert-box alert-error"><?php echo $_GET['err']; ?></div>
  <?php } ?>

  <div class="top-bar">
    <div class="page-title">إدارة الكورسات</div>
    <a href="add_course.php"><button class="btn btn-purple">+ إضافة كورس</button></a>
  </div>

  <div class="table-wrap">
    <table>
      <tr>
        <th>الكود</th>
        <th>الكورس</th>
        <th>الساعات</th>
        <th>السعة</th>
        <th>المسجّلون</th>
        <th>إجراء</th>
      </tr>
      <?php if ($result->num_rows == 0) { ?>
        <tr><td colspan="6"><div class="empty">لا يوجد كورسات</div></td></tr>
      <?php } else { ?>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><span class="badge"><?php echo $row['code']; ?></span></td>
            <td><?php echo $row['name_ar']; ?></td>
            <td><?php echo $row['credits']; ?></td>
            <td><?php echo $row['capacity']; ?></td>
            <td><?php echo $row['regs_count']; ?></td>
            <td>
              <div class="actions">
                <a href="update_course.php?id=<?php echo $row['id']; ?>"><button class="btn btn-blue btn-sm">تعديل</button></a>
                <a href="delete_course.php?id=<?php echo $row['id']; ?>" onclick="return confirm('هل أنت متأكد من حذف هذا الكورس؟')"><button class="btn btn-red btn-sm">حذف</button></a>
              </div>
            </td>
          </tr>
        <?php } ?>
      <?php } ?>
    </table>
  </div>

</main>
</body>
</html>
