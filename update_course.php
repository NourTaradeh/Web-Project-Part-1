<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تعديل كورس</title>
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

$id = (int)$_GET['id'];
$result = $conn->query("SELECT * FROM courses WHERE id = $id");
$course = $result->fetch_assoc();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $conn->real_escape_string(strtoupper($_POST['code']));
    $name_ar = $conn->real_escape_string($_POST['name_ar']);
    $desc = $conn->real_escape_string($_POST['desc']);
    $credits = (int)$_POST['credits'];
    $capacity = (int)$_POST['capacity'];

    $sql = "UPDATE courses SET code='$code', name_ar='$name_ar', `desc`='$desc', credits=$credits, capacity=$capacity WHERE id=$id";
    $result = $conn->query($sql);

    if ($result === TRUE) {
        header("Location: courses.php?msg=تم تعديل الكورس بنجاح");
        exit();
    } else {
        $error = "حدث خطأ: " . $conn->error;
    }
}
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

  <?php if ($error != "") { ?>
    <div class="alert-box alert-error"><?php echo htmlspecialchars($error); ?></div>
  <?php } ?>

  <div class="page-title">تعديل الكورس</div>

  <div class="card" style="max-width:500px">
    <form method="post">
      <div class="modal-body" style="padding:0">
        <label>الكود *</label>
        <input type="text" name="code" value="<?php echo htmlspecialchars($course['code']); ?>" required>

        <label>اسم الكورس *</label>
        <input type="text" name="name_ar" value="<?php echo htmlspecialchars($course['name_ar']); ?>" required>

        <label>الوصف</label>
        <input type="text" name="desc" value="<?php echo htmlspecialchars($course['desc']); ?>">

        <label>الساعات</label>
        <input type="number" name="credits" value="<?php echo (int)$course['credits']; ?>" min="1" max="6">

        <label>السعة</label>
        <input type="number" name="capacity" value="<?php echo (int)$course['capacity']; ?>" min="1">
      </div>
      <div style="margin-top:16px;display:flex;gap:10px">
        <a href="courses.php"><button type="button" class="btn btn-gray">إلغاء</button></a>
        <button type="submit" class="btn btn-purple">حفظ</button>
      </div>
    </form>
  </div>

</main>
</body>
</html>
