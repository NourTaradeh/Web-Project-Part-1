<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>الكورسات المتاحة</title>
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
$search = isset($_GET['search']) ? $_GET['search'] : '';

$comp_result = $conn->query("SELECT course_id FROM completed_courses WHERE student_id = $student_id");
$completed_ids = [];
while ($row = $comp_result->fetch_assoc()) {
    $completed_ids[] = $row['course_id'];
}

if ($search != '') {
    $courses_result = $conn->query("SELECT * FROM courses WHERE name_ar LIKE '%$search%' OR code LIKE '%$search%'");
} else {
    $courses_result = $conn->query("SELECT * FROM courses");
}

$courses = [];
while ($row = $courses_result->fetch_assoc()) {
    $courses[] = $row;
}
?>

<nav class="navbar">
  <div class="nav-brand">CourseHub</div>
  <div class="nav-tabs">
    <a class="nav-tab" href="student.php">الرئيسية</a>
    <a class="nav-tab active" href="available_courses.php">الكورسات</a>
    <a class="nav-tab" href="my_courses.php">كورساتي</a>
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

  <div class="page-title">الكورسات المتاحة</div>

  <form method="get">
    <input class="search-bar" type="text" name="search" placeholder="ابحث باسم الكورس أو الكود..." value="<?php echo $search; ?>">
  </form>

  <?php if (count($courses) == 0) { ?>
    <div class="empty">لا يوجد نتائج</div>
  <?php } else { ?>
    <div class="courses-grid">
      <?php foreach ($courses as $c) { ?>
        <?php
        
        $cnt_result = $conn->query("SELECT COUNT(*) as cnt FROM registrations WHERE course_id = " . $c['id']);
        $cnt = $cnt_result->fetch_assoc()['cnt'];
        $seats_left = $c['capacity'] - $cnt;
        $pct = $c['capacity'] > 0 ? round($cnt / $c['capacity'] * 100) : 0;
        $is_full = $cnt >= $c['capacity'];

        
        $reg_check = $conn->query("SELECT id FROM registrations WHERE student_id = $student_id AND course_id = " . $c['id']);
        $already_registered = $reg_check->num_rows > 0;

        
        $prereqs_result = $conn->query("SELECT prereq_id FROM course_prereqs WHERE course_id = " . $c['id']);
        $prereqs_ok = true;
        while ($p = $prereqs_result->fetch_assoc()) {
            if (!in_array($p['prereq_id'], $completed_ids)) {
                $prereqs_ok = false;
                break;
            }
        }

        
        $prereqs_text_result = $conn->query("SELECT c2.code FROM course_prereqs cp JOIN courses c2 ON cp.prereq_id = c2.id WHERE cp.course_id = " . $c['id']);
        $prereq_codes = [];
        while ($p = $prereqs_text_result->fetch_assoc()) {
            $prereq_codes[] = $p['code'];
        }
        $prereq_text = count($prereq_codes) == 0 ? 'لا يوجد متطلبات' : 'متطلب: ' . implode(', ', $prereq_codes);

        
        if ($pct >= 90) $fill_color = 'fill-red';
        elseif ($pct >= 60) $fill_color = 'fill-yellow';
        else $fill_color = 'fill-green';
        ?>

        <div class="course-card">
          <span class="course-code"><?php echo $c['code']; ?></span>
          <div class="course-name"><?php echo $c['name_ar']; ?></div>
          <div class="course-desc"><?php echo $c['desc']; ?></div>
          <div>
            <div class="progress-label"><?php echo $seats_left; ?> / <?php echo $c['capacity']; ?> مقعد متبقي</div>
            <div class="progress-bar"><div class="progress-fill <?php echo $fill_color; ?>" style="width:<?php echo $pct; ?>%"></div></div>
          </div>
          <div class="course-prereq"><?php echo $prereq_text; ?></div>

          <?php if ($already_registered) { ?>
            <button class="btn btn-full" disabled>مسجل</button>
          <?php } elseif ($is_full) { ?>
            <button class="btn btn-full" disabled>مكتمل</button>
          <?php } elseif (!$prereqs_ok) { ?>
            <button class="btn btn-full" disabled>المتطلبات ناقصة</button>
          <?php } else { ?>
            <a href="register_course.php?course_id=<?php echo $c['id']; ?>" onclick="return confirm('هل تريد التسجيل في <?php echo $c['name_ar']; ?>؟')">
              <button class="btn btn-green btn-full">تسجيل</button>
            </a>
          <?php } ?>
        </div>

      <?php } ?>
    </div>
  <?php } ?>

</main>
</body>
</html>
