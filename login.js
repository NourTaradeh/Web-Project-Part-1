// إعادة توجيه المستخدم إن كان مسجلاً دخوله
var user = getCurrentUser();
if (user) {
  window.location.href = user.role === 'admin' ? 'admin.html' : 'student.html';
}

function togglePass() {
  var inp = document.getElementById('password');
  inp.type = inp.type === 'password' ? 'text' : 'password';
}

function doLogin() {
  var email = document.getElementById('email').value.trim().toLowerCase();
  var pass  = document.getElementById('password').value;

  if (!email || !pass) {
    showError('يرجى إدخال البريد الإلكتروني وكلمة المرور');
    return;
  }

  var found = getUsers().find(function(u) {
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

document.addEventListener('keydown', function(e) {
  if (e.key === 'Enter') doLogin();
});

document.getElementById('email').addEventListener('input', function() {
  document.getElementById('errorMsg').classList.remove('show');
});

document.getElementById('password').addEventListener('input', function() {
  document.getElementById('errorMsg').classList.remove('show');
});
