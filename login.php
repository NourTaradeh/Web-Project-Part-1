<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تسجيل الدخول - نظام الكورسات</title>
  <link rel="stylesheet" href="login.css">
</head>
<body>

<?php
session_start();

if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: student.php");
    }
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("db.php");

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($password == $user['password']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = htmlspecialchars($user['name']);
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: student.php");
            }
            exit();
        } else {
            $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
        }
    } else {
        $error = "البريد الإلكتروني أو كلمة المرور غير صحيحة";
    }
}
?>

<div class="page">
  <div class="box">
    <h1>CourseHub</h1>
    <p>أدخل بياناتك للدخول إلى حسابك</p>

    <?php if ($error != "") { ?>
      <div class="error show"><?php echo htmlspecialchars($error); ?></div>
    <?php } ?>

    <form method="post">
      <label for="email">البريد الإلكتروني</label>
      <input type="email" id="email" name="email" placeholder="example@ppu.edu.ps" required>

      <label for="password">كلمة المرور</label>
      <input type="password" id="password" name="password" placeholder="كلمة المرور" required>

      <button class="btn-login" type="submit">دخول</button>
    </form>

    <div class="hint">
      <strong>حساب الادمن:</strong><br>
      ادمن: admin@ppu.edu.ps / admin123
    </div>
  </div>
</div>

</body>
</html>
