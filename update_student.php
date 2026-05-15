<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تعديل طالب</title>
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

$id = $_GET['id'];
$sql = "SELECT * FROM users WHERE id = $id";
$result = $conn->query($sql);
$student = $result->fetch_assoc();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $student_num = $_POST['student_num'];
    $major = $_POST['major'];
    $year = $_POST['year'];

    
    $check = $conn->query("SELECT id FROM users WHERE email = '$email' AND id != $id");
    if ($check->num_rows > 0) {
        $error = "البريد الإلكتروني مستخدم مسبقاً";
    } else {
        $sql = "UPDATE users SET name='$name', email='$email', password='$password', student_num='$student_num', major='$major', year=$year WHERE id=$id";
        $result = $conn->query($sql);

        if ($result === TRUE) {
            header("Location: students.php?msg=تم تعديل الطالب بنجاح");
            exit();
        } else {
            $error = "حدث خطأ: " . $conn->error;
        }
    }
}
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
    <span class="nav-username"><?php echo $_SESSION['name']; ?></span>
    <a href="logout.php"><button class="btn-logout">خروج</button></a>
  </div>
</nav>

<main class="main">

  <?php if ($error != "") { ?>
    <div class="alert-box alert-error"><?php echo $error; ?></div>
  <?php } ?>

  <div class="page-title">تعديل بيانات الطالب</div>

  <div class="card" style="max-width:500px">
    <form method="post">
      <div class="modal-body" style="padding:0">
        <label>الاسم الكامل *</label>
        <input type="text" name="name" value="<?php echo $student['name']; ?>" required>

        <label>البريد الإلكتروني *</label>
        <input type="email" name="email" value="<?php echo $student['email']; ?>" required>

        <label>كلمة المرور *</label>
        <input type="text" name="password" value="<?php echo $student['password']; ?>" required>

        <label>الرقم الجامعي *</label>
        <input type="text" name="student_num" value="<?php echo $student['student_num']; ?>" required>

        <label>التخصص</label>
        <input type="text" name="major" value="<?php echo $student['major']; ?>">

        <label>السنة</label>
        <select name="year">
          <option value="1" <?php if($student['year']==1) echo 'selected'; ?>>السنة 1</option>
          <option value="2" <?php if($student['year']==2) echo 'selected'; ?>>السنة 2</option>
          <option value="3" <?php if($student['year']==3) echo 'selected'; ?>>السنة 3</option>
          <option value="4" <?php if($student['year']==4) echo 'selected'; ?>>السنة 4</option>
          <option value="5" <?php if($student['year']==5) echo 'selected'; ?>>السنة 5</option>
        </select>
      </div>
      <div style="margin-top:16px;display:flex;gap:10px">
        <a href="students.php"><button type="button" class="btn btn-gray">إلغاء</button></a>
        <button type="submit" class="btn btn-purple">حفظ</button>
      </div>
    </form>
  </div>

</main>
</body>
</html>
