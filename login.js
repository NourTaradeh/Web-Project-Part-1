var user = getCurrentUser();
if (user) {
  window.location.href = user.role === 'admin' ? 'admin.html' : 'student.html';
}

function doLogin() {
  var email = document.getElementById('email').value;
  var pass = document.getElementById('password').value;

  var found = getUsers().find(function (u) {
    return u.email.toLowerCase() === email && u.password === pass;
  });

  if (!found) {
    showError('البريد الإلكتروني أو كلمة المرور غير صحيحة');
    return;
  }

  setCurrentUser(found);
  window.location.href = found.role === 'admin' ? 'admin.html' : 'student.html';
}

function showError(msg) {
  var el = document.getElementById('errorMsg');
  el.textContent = msg;
  el.classList.add('show');
}

document.getElementById('email').addEventListener('input', function () {
  document.getElementById('errorMsg').classList.remove('show');
});

document.getElementById('password').addEventListener('input', function () {
  document.getElementById('errorMsg').classList.remove('show');
});