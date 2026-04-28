var currentUser = requireLogin('student');
if (!currentUser) throw new Error('redirect');

document.getElementById('navUser').textContent = currentUser.name;

function showPage(page) {
  document.querySelectorAll('.nav-tab').forEach(function (el) {
    el.classList.remove('active');
  });
  document.getElementById('tab-' + page).classList.add('active');

  var pages = {
    dashboard: pageDashboard,
    courses: pageCourses,
    mycourses: pageMyCourses
  };

  document.getElementById('mainContent').innerHTML = pages[page]();
}


function showAlert(msg, type) {
  var box = document.getElementById('alertBox');
  box.textContent = msg;
  box.className = 'alert-box ' + (type === 'error' ? 'alert-error' : 'alert-success');
  setTimeout(function () { box.className = 'alert-box'; }, 3000);
}

function getCourse(id) {
  return getCourses().find(function (c) { return c.id === id; });
}

function registeredCount(courseId) {
  return getRegistrations().filter(function (r) { return r.courseId === courseId; }).length;
}

function isRegistered(courseId) {
  return getRegistrations().some(function (r) {
    return r.studentId === currentUser.id && r.courseId === courseId;
  });
}

function getMyCompleted() {
  var comp = getCompleted();
  return comp[currentUser.id] || [];
}

function hasPrereqs(course) {
  var completed = getMyCompleted();
  return course.prereqs.every(function (pid) { return completed.includes(pid); });
}

function getMyRegs() {
  return getRegistrations().filter(function (r) { return r.studentId === currentUser.id; });
}

function progressColor(pct) {
  if (pct >= 90) return 'fill-red';
  if (pct >= 60) return 'fill-yellow';
  return 'fill-green';
}


function pageDashboard() {
  var myRegs = getMyRegs();
  var courses = getCourses();
  var completed = getMyCompleted();

  var html = '<div class="page-title">مرحباً، ' + currentUser.name + '</div>';

  html += '<div class="dash-stats">'
    + dashStat(myRegs.length, 'كورسات مسجلة')
    + dashStat(courses.length, 'كورسات متاحة')
    + dashStat(completed.length, 'كورسات مكتملة')
    + '</div>';

  return html;
}

function dashStat(num, label) {
  return '<div class="dash-stat-box">'
    + '<div class="dash-stat-num">' + num + '</div>'
    + '<div class="dash-stat-label">' + label + '</div>'
    + '</div>';
}


filter = filter || '';
var q = filter.toLowerCase();

var filtered = getCourses().filter(function (c) {
  return c.nameAr.toLowerCase().includes(q) || c.code.toLowerCase().includes(q);
});

var cards = filtered.map(function (c) {
  var cnt = registeredCount(c.id);
  var pct = Math.round(cnt / c.capacity * 100);
  var seatsLeft = c.capacity - cnt;
  var isFull = cnt >= c.capacity;
  var alrReg = isRegistered(c.id);
  var prereqOk = hasPrereqs(c);

  var prereqText = c.prereqs.length === 0
    ? 'لا يوجد متطلبات'
    : 'متطلب: ' + c.prereqs.map(function (pid) {
      var p = getCourse(pid);
      return p ? p.code : '';
    }).join(', ');

  var btn;
  if (alrReg) btn = '<button class="btn btn-full" disabled>مسجل</button>';
  else if (isFull) btn = '<button class="btn btn-full" disabled>مكتمل</button>';
  else if (!prereqOk) btn = '<button class="btn btn-full" disabled>المتطلبات ناقصة</button>';
  else btn = '<button class="btn btn-green btn-full" onclick="registerCourse(' + c.id + ')">تسجيل</button>';

  return '<div class="course-card">'
    + '<span class="course-code">' + c.code + '</span>'
    + '<div class="course-name">' + c.nameAr + '</div>'
    + '<div class="course-desc">' + c.desc + '</div>'
    + '<div>'
    + '<div class="progress-label">' + seatsLeft + ' / ' + c.capacity + ' مقعد متبقي</div>'
    + '<div class="progress-bar"><div class="progress-fill ' + progressColor(pct) + '" style="width:' + pct + '%"></div></div>'
    + '</div>'
    + '<div class="course-prereq">' + prereqText + '</div>'
    + btn
    + '</div>';
}).join('');

return '<div class="page-title">الكورسات المتاحة</div>'
  + '<input class="search-bar" placeholder="ابحث باسم الكورس أو الكود..."'
  + ' id="coursesSearch" oninput="searchCourses()" value="' + filter + '">'
  + (filtered.length === 0
    ? '<div class="empty">لا يوجد نتائج</div>'
    : '<div class="courses-grid">' + cards + '</div>');
}

function searchCourses() {
  var val = document.getElementById('coursesSearch').value;
  document.getElementById('mainContent').innerHTML = pageCourses(val);
  var inp = document.getElementById('coursesSearch');
  if (inp) { inp.focus(); inp.setSelectionRange(inp.value.length, inp.value.length); }
}

function registerCourse(courseId) {
  if (isRegistered(courseId)) { showAlert('انت مسجل في هذا الكورس مسبقا', 'error'); return; }
  var c = getCourse(courseId);
  if (!hasPrereqs(c)) { showAlert('لم تكتمل المتطلبات السابقة', 'error'); return; }
  if (registeredCount(courseId) >= c.capacity) { showAlert('الكورس مكتمل', 'error'); return; }

  showConfirm(
    'تأكيد التسجيل',
    'هل تريد التسجيل في كورس <strong>' + c.nameAr + '</strong> (' + c.code + ')؟',
    'تسجيل',
    'confirm-btn-green',
    function () {
      var today = new Date().toLocaleDateString('en-CA').replace(/-/g, '/');
      var regs = getRegistrations();
      regs.push({ id: Date.now(), studentId: currentUser.id, courseId: courseId, date: today });
      saveRegistrations(regs);
      showAlert('تم التسجيل بنجاح', 'success');
      showPage('courses');
    }
  );
}

function pageMyCourses() {
  var myRegs = getMyRegs();

  if (myRegs.length === 0) {
    return '<div class="page-title">كورساتي</div>'
      + '<div class="card"><div class="empty">لا يوجد كورسات مسجلة بعد<br><br>'
      + '<button class="btn btn-blue" onclick="showPage(\'courses\')">تصفح الكورسات</button></div></div>';
  }

  var rows = myRegs.map(function (r) {
    var c = getCourse(r.courseId);
    return '<tr>'
      + '<td><span class="course-code">' + c.code + '</span></td>'
      + '<td>' + c.nameAr + '</td>'
      + '<td>' + c.credits + '</td>'
      + '<td>' + r.date + '</td>'
      + '<td><button class="btn btn-red btn-sm" onclick="dropCourse(' + r.id + ')">الغاء التسجيل</button></td>'
      + '</tr>';
  }).join('');

  return '<div class="page-title">كورساتي</div>'
    + '<div class="table-wrap"><table>'
    + '<tr><th>الكود</th><th>الكورس</th><th>الساعات</th><th>التاريخ</th><th>اجراء</th></tr>'
    + rows + '</table></div>';
}

function dropCourse(regId) {
  var reg = getRegistrations().find(function (r) { return r.id === regId; });
  var c = reg ? getCourse(reg.courseId) : null;
  var name = c ? c.nameAr + ' (' + c.code + ')' : 'هذا الكورس';

  showConfirm(
    'تأكيد الالغاء',
    'هل انت متأكد من الغاء تسجيلك في كورس <strong>' + name + '</strong>؟',
    'الغاء التسجيل',
    'confirm-btn-red',
    function () {
      saveRegistrations(getRegistrations().filter(function (r) { return r.id !== regId; }));
      showAlert('تم الغاء التسجيل بنجاح', 'success');
      showPage('mycourses');
    }
  );
}

function showConfirm(title, message, confirmText, confirmClass, onConfirm) {
  document.getElementById('confirmTitle').textContent = title;
  document.getElementById('confirmMessage').innerHTML = message;
  var btn = document.getElementById('confirmOkBtn');
  btn.textContent = confirmText;
  btn.className = 'confirm-btn ' + confirmClass;
  btn.onclick = function () { closeConfirm(); onConfirm(); };
  document.getElementById('confirmOverlay').classList.add('open');
}

function closeConfirm() {
  document.getElementById('confirmOverlay').classList.remove('open');
}

function handleOverlayClick(e) {
  if (e.target === document.getElementById('confirmOverlay')) closeConfirm();
}

showPage('dashboard');