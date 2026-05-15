<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>المتطلبات - نظام الكورسات</title>
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

$courses_result = $conn->query("SELECT * FROM courses");
$courses = [];
while ($row = $courses_result->fetch_assoc()) {
    $courses[] = $row;
}

$selected_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : '';
?>

<nav class="navbar">
  <div class="nav-brand">CourseHub</div>
  <div class="nav-tabs">
    <a class="nav-tab" href="admin.php">الرئيسية</a>
    <a class="nav-tab" href="students.php">الطلاب</a>
    <a class="nav-tab" href="courses.php">الكورسات</a>
    <a class="nav-tab active" href="prereqs.php">المتطلبات</a>
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

  <div class="page-title">إدارة المتطلبات</div>

  <div class="card">
    <h3>اختر كورساً</h3>
    <form method="get">
      <select name="course_id" onchange="this.form.submit()" style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:14px">
        <option value="">-- اختر كورس --</option>
        <?php foreach ($courses as $c) { ?>
          <option value="<?php echo $c['id']; ?>" <?php if($selected_id == $c['id']) echo 'selected'; ?>>
            <?php echo htmlspecialchars($c['code'] . ' - ' . $c['name_ar']); ?>
          </option>
        <?php } ?>
      </select>
    </form>
  </div>

  <?php if ($selected_id != '') { ?>
    <?php
    
    $sel_result = $conn->query("SELECT * FROM courses WHERE id = $selected_id");
    $sel_course = $sel_result->fetch_assoc();

    
    $prereqs_result = $conn->query("SELECT c.* FROM course_prereqs cp JOIN courses c ON cp.prereq_id = c.id WHERE cp.course_id = $selected_id");
    $prereq_ids = [];
    $prereqs = [];
    while ($row = $prereqs_result->fetch_assoc()) {
        $prereqs[] = $row;
        $prereq_ids[] = $row['id'];
    }
    ?>

    <div class="card">
      <h3>متطلبات: <?php echo htmlspecialchars($sel_course['name_ar']); ?></h3>

      <?php if (count($prereqs) == 0) { ?>
        <div style="color:#9ca3af;padding:8px 0">لا يوجد متطلبات</div>
      <?php } else { ?>
        <?php foreach ($prereqs as $p) { ?>
          <div class="prereq-item">
            <span><span class="badge"><?php echo htmlspecialchars($p['code']); ?></span> <?php echo htmlspecialchars($p['name_ar']); ?></span>
            <a href="delete_prereq.php?course_id=<?php echo $selected_id; ?>&prereq_id=<?php echo $p['id']; ?>" onclick="return confirm('حذف هذا المتطلب؟')">
              <button class="btn btn-red btn-sm">حذف</button>
            </a>
          </div>
        <?php } ?>
      <?php } ?>

      <?php
      
      $available = array_filter($courses, function($c) use ($selected_id, $prereq_ids) {
          return $c['id'] != $selected_id && !in_array($c['id'], $prereq_ids);
      });
      ?>

      <?php if (count($available) > 0) { ?>
        <form method="get" action="add_prereq.php" style="margin-top:16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">
          <input type="hidden" name="course_id" value="<?php echo $selected_id; ?>">
          <select name="prereq_id" style="flex:1;min-width:160px;padding:9px;border:1.5px solid #e5e7eb;border-radius:7px;font-family:inherit">
            <option value="">-- اختر كورس للإضافة --</option>
            <?php foreach ($available as $a) { ?>
              <option value="<?php echo $a['id']; ?>"><?php echo htmlspecialchars($a['code'] . ' - ' . $a['name_ar']); ?></option>
            <?php } ?>
          </select>
          <button type="submit" class="btn btn-purple">+ إضافة</button>
        </form>
      <?php } ?>

    </div>
  <?php } ?>

</main>
</body>
</html>
