<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>التسجيلات - نظام الكورسات</title>
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

$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($search != '') {
    $sql = "SELECT r.*, u.name as student_name, u.student_num, c.code as course_code, c.name_ar as course_name
            FROM registrations r
            JOIN users u ON r.student_id = u.id
            JOIN courses c ON r.course_id = c.id
            WHERE u.name LIKE '%$search%' OR u.student_num LIKE '%$search%' OR c.code LIKE '%$search%' OR c.name_ar LIKE '%$search%'";
} else {
    $sql = "SELECT r.*, u.name as student_name, u.student_num, c.code as course_code, c.name_ar as course_name
            FROM registrations r
            JOIN users u ON r.student_id = u.id
            JOIN courses c ON r.course_id = c.id";
}

$result = $conn->query($sql);
?>

<nav class="navbar">
  <div class="nav-brand">CourseHub</div>
  <div class="nav-tabs">
    <a class="nav-tab" href="admin.php">الرئيسية</a>
    <a class="nav-tab" href="students.php">الطلاب</a>
    <a class="nav-tab" href="courses.php">الكورسات</a>
    <a class="nav-tab" href="prereqs.php">المتطلبات</a>
    <a class="nav-tab active" href="registrations.php">التسجيلات</a>
  </div>
  <div class="nav-left">
    <span class="nav-username"><?php echo $_SESSION['name']; ?></span>
    <a href="logout.php"><button class="btn-logout">خروج</button></a>
  </div>
</nav>

<main class="main">

  <div class="page-title">التسجيلات</div>

  <form method="get">
    <input class="search-bar" type="text" name="search" placeholder="ابحث بالاسم أو الرقم أو الكورس..." value="<?php echo $search; ?>">
  </form>

  <div class="table-wrap">
    <table>
      <tr>
        <th>الطالب</th>
        <th>الرقم الجامعي</th>
        <th>الكود</th>
        <th>الكورس</th>
        <th>التاريخ</th>
      </tr>
      <?php if ($result->num_rows == 0) { ?>
        <tr><td colspan="5"><div class="empty">لا يوجد نتائج</div></td></tr>
      <?php } else { ?>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?php echo $row['student_name']; ?></td>
            <td><?php echo $row['student_num'] ? $row['student_num'] : '-'; ?></td>
            <td><span class="badge"><?php echo $row['course_code']; ?></span></td>
            <td><?php echo $row['course_name']; ?></td>
            <td><?php echo $row['date']; ?></td>
          </tr>
        <?php } ?>
      <?php } ?>
    </table>
  </div>

</main>
</body>
</html>
