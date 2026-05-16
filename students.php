<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الطلاب - نظام الكورسات</title>
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
    $like = '%' . $search . '%';
    $stmt = $conn->prepare("SELECT u.*, (SELECT COUNT(*) FROM registrations r WHERE r.student_id = u.id) as regs_count 
            FROM users u 
            WHERE u.role = 'student' AND (u.name LIKE ? OR u.email LIKE ? OR u.student_num LIKE ?)");
    $stmt->bind_param("sss", $like, $like, $like);
} else {
    $stmt = $conn->prepare("SELECT u.*, (SELECT COUNT(*) FROM registrations r WHERE r.student_id = u.id) as regs_count 
            FROM users u WHERE u.role = 'student'");
}

$stmt->execute();
$result = $stmt->get_result();
?>

<nav class="navbar">
  <div class="nav-brand">CourseHub</div>
  <div class="nav-tabs">
    <a class="nav-tab" href="admin.php">الرئيسية</a>
    <a class="nav-tab active" href="students.php">الطلاب</a>
    <a class="nav-tab" href="courses.php">الكورسات</a>
    <a class="nav-tab" href="prereqs.php">المتطلبات</a>
    <a class="nav-tab" href="registrations.php">التسجيلات</a>
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
  <?php if (isset($_GET['err'])) { ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($_GET['err']); ?></div>
  <?php } ?>

  <div class="top-bar">
    <div class="page-title">إدارة الطلاب</div>
    <a href="add_student.php"><button class="btn btn-purple">+ إضافة طالب</button></a>
  </div>

  <form method="get">
    <input class="search-bar" type="text" name="search" placeholder="ابحث بالاسم أو البريد أو الرقم الجامعي..." value="<?php echo htmlspecialchars($search); ?>">
  </form>

  <div class="table-wrap">
    <table>
      <tr>
        <th>الاسم</th>
        <th>الرقم الجامعي</th>
        <th>البريد</th>
        <th>التخصص</th>
        <th>كورسات</th>
        <th>إجراء</th>
      </tr>
      <?php if ($result->num_rows == 0) { ?>
        <tr><td colspan="6"><div class="empty">لا يوجد طلاب</div></td></tr>
      <?php } else { ?>
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['student_num'] ? $row['student_num'] : '-'); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['major'] ? $row['major'] : '-'); ?></td>
            <td><span class="badge"><?php echo (int)$row['regs_count']; ?></span></td>
            <td>
              <div class="actions">
                <a href="update_student.php?id=<?php echo (int)$row['id']; ?>"><button class="btn btn-blue btn-sm">تعديل</button></a>
                <a href="delete_student.php?id=<?php echo (int)$row['id']; ?>" onclick="return confirm('هل أنت متأكد من حذف هذا الطالب؟')"><button class="btn btn-red btn-sm">حذف</button></a>
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
