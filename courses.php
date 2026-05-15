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
      <tbody id="courses-tbody">
        <?php while ($row = $result->fetch_assoc()) { ?>
          <tr>
            <td><span class="badge"><?php echo htmlspecialchars($row['code']); ?></span></td>
            <td><?php echo htmlspecialchars($row['name_ar']); ?></td>
            <td><?php echo (int)$row['credits']; ?></td>
            <td><?php echo (int)$row['capacity']; ?></td>
            <td><?php echo (int)$row['regs_count']; ?></td>
            <td>
              <div class="actions">
                <a href="update_course.php?id=<?php echo $row['id']; ?>"><button class="btn btn-blue btn-sm">تعديل</button></a>
                <a href="delete_course.php?id=<?php echo $row['id']; ?>" onclick="return confirm('هل أنت متأكد من حذف هذا الكورس؟')"><button class="btn btn-red btn-sm">حذف</button></a>
              </div>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>

</main>

<script>
function loadCourses() {
    fetch('get_courses.php')
        .then(function(res) { return res.json(); })
        .then(function(courses) {
            var tbody = document.getElementById('courses-tbody');
            var html = '';
            if (courses.length == 0) {
                html = '<tr><td colspan="6"><div class="empty">لا يوجد كورسات</div></td></tr>';
            } else {
                courses.forEach(function(row) {
                    html += '<tr>';
                    html += '<td><span class="badge">' + row.code + '</span></td>';
                    html += '<td>' + row.name_ar + '</td>';
                    html += '<td>' + row.credits + '</td>';
                    html += '<td>' + row.capacity + '</td>';
                    html += '<td>' + row.regs_count + '</td>';
                    html += '<td><div class="actions">';
                    html += '<a href="update_course.php?id=' + row.id + '"><button class="btn btn-blue btn-sm">تعديل</button></a>';
                    html += '<a href="delete_course.php?id=' + row.id + '" onclick="return confirm(\'هل أنت متأكد من حذف هذا الكورس؟\')"><button class="btn btn-red btn-sm">حذف</button></a>';
                    html += '</div></td>';
                    html += '</tr>';
                });
            }
            tbody.innerHTML = html;
        });
}

setInterval(loadCourses, 5000);
</script>

</body>
</html>
