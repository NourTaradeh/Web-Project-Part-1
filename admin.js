var currentUser = requireLogin('admin');
if (!currentUser) throw new Error('redirect');

document.getElementById('navUser').textContent = currentUser.name;

var editingCourseId = null;
var editingStudentId = null;

function showPage(page) {
  document.querySelectorAll('.nav-tab').forEach(function(el) {
    el.classList.remove('active');
  });
  document.getElementById('tab-' + page).classList.add('active');

  var pages = {
    students:  pageStudents,
    courses:   pageCourses,
    prereqs:   pagePrereqs,
    regs:      pageRegistrations
  };

  document.getElementById('mainContent').innerHTML = pages[page]();
  closeMenu();
}

function toggleMenu() {
  document.getElementById('mobileMenu').classList.toggle('open');
}

function closeMenu() {
  document.getElementById('mobileMenu').classList.remove('open');
}

function showAlert(msg, type) {
  var box = document.getElementById('alertBox');
  box.textContent = msg;
  box.className = 'alert-box ' + (type === 'error' ? 'alert-error' : 'alert-success');
  setTimeout(function() { box.className = 'alert-box'; }, 3000);
}

function getCourse(id) {
  return getCourses().find(function(c) { return c.id === id; });
}

function registeredCount(courseId) {
  return getRegistrations().filter(function(r) { return r.courseId === courseId; }).length;
}



function pageStudents(filter) {
  filter = filter || '';
  var q = filter.toLowerCase();
  var students = getUsers().filter(function(u) {
    return u.role === 'student' && (
      u.name.toLowerCase().includes(q) ||
      u.email.toLowerCase().includes(q) ||
      (u.studentNum || '').includes(q)
    );
  });

  var rows = students.map(function(s) {
    var regsCount = getRegistrations().filter(function(r) { return r.studentId === s.id; }).length;
    return '<tr>'
      + '<td>' + s.name + '</td>'
      + '<td>' + (s.studentNum || '-') + '</td>'
      + '<td>' + s.email + '</td>'
      + '<td>' + (s.major || '-') + '</td>'
      + '<td><span class="badge">' + regsCount + '</span></td>'
      + '<td><div class="actions">'
      + '<button class="btn btn-blue btn-sm" onclick="openEditStudent(' + s.id + ')">تعديل</button>'
      + '<button class="btn btn-red btn-sm" onclick="deleteStudent(' + s.id + ')">حذف</button>'
      + '</div></td>'
      + '</tr>';
  }).join('');

  var table = students.length === 0
    ? '<div class="empty">لا يوجد طلاب</div>'
    : '<div class="table-wrap"><table>'
    + '<tr><th>الاسم</th><th>الرقم الجامعي</th><th>البريد</th><th>التخصص</th><th>كورسات</th><th>إجراء</th></tr>'
    + rows + '</table></div>';

  return '<div class="top-bar">'
    + '<div class="page-title">إدارة الطلاب</div>'
    + '<button class="btn btn-purple" onclick="openAddStudent()">+ إضافة طالب</button>'
    + '</div>'
    + '<input class="search-bar" placeholder="ابحث بالاسم أو البريد أو الرقم الجامعي..."'
    + ' id="studentSearch" oninput="searchStudents()" value="' + filter + '">'
    + table;
}

function searchStudents() {
  var val = document.getElementById('studentSearch').value;
  document.getElementById('mainContent').innerHTML = pageStudents(val);
  focusInput('studentSearch');
}

function openAddStudent() {
  editingStudentId = null;
  document.getElementById('studentModalTitle').textContent = 'إضافة طالب جديد';
  ['sName','sEmail','sPass','sNum','sMajor'].forEach(function(id) {
    document.getElementById(id).value = '';
  });
  document.getElementById('sYear').value = '3';
  document.getElementById('studentModal').classList.add('open');
}

function openEditStudent(id) {
  editingStudentId = id;
  var s = getUsers().find(function(u) { return u.id === id; });
  document.getElementById('studentModalTitle').textContent = 'تعديل بيانات الطالب';
  document.getElementById('sName').value  = s.name;
  document.getElementById('sEmail').value = s.email;
  document.getElementById('sPass').value  = s.password;
  document.getElementById('sNum').value   = s.studentNum || '';
  document.getElementById('sMajor').value = s.major || '';
  document.getElementById('sYear').value  = s.year || '3';
  document.getElementById('studentModal').classList.add('open');
}

function closeStudentModal() {
  document.getElementById('studentModal').classList.remove('open');
}

function saveStudent() {
  var name  = document.getElementById('sName').value.trim();
  var email = document.getElementById('sEmail').value.trim().toLowerCase();
  var pass  = document.getElementById('sPass').value.trim();
  var num   = document.getElementById('sNum').value.trim();
  var major = document.getElementById('sMajor').value.trim();
  var year  = document.getElementById('sYear').value;

  if (!name || !email || !pass || !num) {
    showAlert('يرجى ملء جميع الحقول الإلزامية', 'error');
    return;
  }

  var users = getUsers();
  var duplicate = users.find(function(u) {
    return u.email.toLowerCase() === email && u.id !== editingStudentId;
  });
  if (duplicate) { showAlert('البريد الإلكتروني مستخدم مسبقاً', 'error'); return; }

  if (editingStudentId) {
    var idx = users.findIndex(function(u) { return u.id === editingStudentId; });
    Object.assign(users[idx], { name, email, password: pass, studentNum: num, major, year });
  } else {
    users.push({ id: Date.now(), role: 'student', name, email, password: pass, studentNum: num, major, year });
  }

  saveUsers(users);
  closeStudentModal();
  showAlert('تم الحفظ بنجاح', 'success');
  showPage('students');
}

function deleteStudent(id) {
  if (!confirm('هل أنت متأكد من حذف هذا الطالب؟')) return;
  saveUsers(getUsers().filter(function(u) { return u.id !== id; }));
  saveRegistrations(getRegistrations().filter(function(r) { return r.studentId !== id; }));
  showAlert('تم الحذف', 'success');
  showPage('students');
}

function pageCourses() {
  var courses = getCourses();

  var rows = courses.map(function(c) {
    return '<tr>'
      + '<td><span class="badge">' + c.code + '</span></td>'
      + '<td>' + c.nameAr + '</td>'
      + '<td>' + c.credits + '</td>'
      + '<td>' + c.capacity + '</td>'
      + '<td>' + registeredCount(c.id) + '</td>'
      + '<td><div class="actions">'
      + '<button class="btn btn-blue btn-sm" onclick="openEditCourse(' + c.id + ')">تعديل</button>'
      + '<button class="btn btn-red btn-sm" onclick="deleteCourse(' + c.id + ')">حذف</button>'
      + '</div></td>'
      + '</tr>';
  }).join('');

  return '<div class="top-bar">'
    + '<div class="page-title">إدارة الكورسات</div>'
    + '<button class="btn btn-purple" onclick="openAddCourse()">+ إضافة كورس</button>'
    + '</div>'
    + '<div class="table-wrap"><table>'
    + '<tr><th>الكود</th><th>الكورس</th><th>الساعات</th><th>السعة</th><th>المسجّلون</th><th>إجراء</th></tr>'
    + rows + '</table></div>';
}

function openAddCourse() {
  editingCourseId = null;
  document.getElementById('courseModalTitle').textContent = 'إضافة كورس جديد';
  ['fCode','fNameAr','fDesc'].forEach(function(id) { document.getElementById(id).value = ''; });
  document.getElementById('fCredits').value  = '3';
  document.getElementById('fCapacity').value = '30';
  document.getElementById('courseModal').classList.add('open');
}

function openEditCourse(id) {
  editingCourseId = id;
  var c = getCourse(id);
  document.getElementById('courseModalTitle').textContent = 'تعديل الكورس';
  document.getElementById('fCode').value     = c.code;
  document.getElementById('fNameAr').value   = c.nameAr;
  document.getElementById('fDesc').value     = c.desc;
  document.getElementById('fCredits').value  = c.credits;
  document.getElementById('fCapacity').value = c.capacity;
  document.getElementById('courseModal').classList.add('open');
}

function closeCourseModal() {
  document.getElementById('courseModal').classList.remove('open');
}

function saveCourse() {
  var code     = document.getElementById('fCode').value.trim().toUpperCase();
  var nameAr   = document.getElementById('fNameAr').value.trim();
  var desc     = document.getElementById('fDesc').value.trim();
  var credits  = parseInt(document.getElementById('fCredits').value);
  var capacity = parseInt(document.getElementById('fCapacity').value);

  if (!code || !nameAr) { showAlert('يرجى ملء الكود والاسم', 'error'); return; }

  var courses = getCourses();
  if (editingCourseId) {
    var idx = courses.findIndex(function(c) { return c.id === editingCourseId; });
    Object.assign(courses[idx], { code, nameAr, desc, credits, capacity });
  } else {
    courses.push({ id: Date.now(), code, nameAr, desc, credits, capacity, prereqs: [] });
  }

  saveCourses(courses);
  closeCourseModal();
  showAlert('تم الحفظ بنجاح', 'success');
  showPage('courses');
}

function deleteCourse(id) {
  if (!confirm('هل أنت متأكد من حذف هذا الكورس؟')) return;
  var courses = getCourses().filter(function(c) { return c.id !== id; });
  courses.forEach(function(c) { c.prereqs = c.prereqs.filter(function(p) { return p !== id; }); });
  saveCourses(courses);
  saveRegistrations(getRegistrations().filter(function(r) { return r.courseId !== id; }));
  showAlert('تم الحذف', 'success');
  showPage('courses');
}

function pagePrereqs() {
  var options = getCourses().map(function(c) {
    return '<option value="' + c.id + '">' + c.code + ' - ' + c.nameAr + '</option>';
  }).join('');

  return '<div class="page-title">إدارة المتطلبات</div>'
    + '<div class="card"><h3>اختر كورساً</h3>'
    + '<select onchange="renderPrereqDetail(this.value)" style="width:100%;padding:10px;border:1.5px solid #e5e7eb;border-radius:8px;font-family:inherit;font-size:14px">'
    + '<option value="">-- اختر كورس --</option>' + options + '</select></div>'
    + '<div id="prereqDetail"></div>';
}

function renderPrereqDetail(selId) {
  selId = parseInt(selId);
  var detail = document.getElementById('prereqDetail');
  if (!selId) { detail.innerHTML = ''; return; }

  var courses = getCourses();
  var c = courses.find(function(x) { return x.id === selId; });

  var prereqItems = c.prereqs.length === 0
    ? '<div style="color:#9ca3af;padding:8px 0">لا يوجد متطلبات</div>'
    : c.prereqs.map(function(pid) {
      var p = courses.find(function(x) { return x.id === pid; });
      return '<div class="prereq-item">'
        + '<span><span class="badge">' + p.code + '</span> ' + p.nameAr + '</span>'
        + '<button class="btn btn-red btn-sm" onclick="removePrereq(' + selId + ',' + pid + ')">حذف</button>'
        + '</div>';
    }).join('');

  var available = courses.filter(function(x) { return x.id !== selId && !c.prereqs.includes(x.id); });
  var addSection = available.length === 0 ? '' :
    '<div style="margin-top:16px;display:flex;gap:10px;align-items:center;flex-wrap:wrap">'
    + '<select id="newPrereqSel" style="flex:1;min-width:160px;padding:9px;border:1.5px solid #e5e7eb;border-radius:7px;font-family:inherit">'
    + '<option value="">-- اختر كورس للإضافة --</option>'
    + available.map(function(x) { return '<option value="' + x.id + '">' + x.code + ' - ' + x.nameAr + '</option>'; }).join('')
    + '</select>'
    + '<button class="btn btn-purple" onclick="addPrereq(' + selId + ')">+ إضافة</button>'
    + '</div>';

  detail.innerHTML = '<div class="card"><h3>متطلبات: ' + c.nameAr + '</h3>' + prereqItems + addSection + '</div>';
}

function addPrereq(courseId) {
  var pid = parseInt(document.getElementById('newPrereqSel').value);
  if (!pid) return;
  var courses = getCourses();
  var c = courses.find(function(x) { return x.id === courseId; });
  if (!c.prereqs.includes(pid)) c.prereqs.push(pid);
  saveCourses(courses);
  renderPrereqDetail(courseId);
}

function removePrereq(courseId, prereqId) {
  var courses = getCourses();
  var c = courses.find(function(x) { return x.id === courseId; });
  c.prereqs = c.prereqs.filter(function(p) { return p !== prereqId; });
  saveCourses(courses);
  renderPrereqDetail(courseId);
}

function pageRegistrations(filter) {
  filter = filter || '';
  var q = filter.toLowerCase();
  var users   = getUsers();
  var courses = getCourses();

  var filtered = getRegistrations().filter(function(r) {
    var u = users.find(function(u) { return u.id === r.studentId; });
    var c = courses.find(function(c) { return c.id === r.courseId; });
    return (u && u.name.toLowerCase().includes(q))
      || (u && (u.studentNum || '').includes(q))
      || (c && c.code.toLowerCase().includes(q))
      || (c && c.nameAr.includes(q));
  });

  var rows = filtered.map(function(r) {
    var u = users.find(function(u) { return u.id === r.studentId; });
    var c = courses.find(function(c) { return c.id === r.courseId; });
    return '<tr>'
      + '<td>' + (u ? u.name : '-') + '</td>'
      + '<td>' + (u ? u.studentNum || '-' : '-') + '</td>'
      + '<td><span class="badge">' + (c ? c.code : '-') + '</span></td>'
      + '<td>' + (c ? c.nameAr : '-') + '</td>'
      + '<td>' + r.date + '</td>'
      + '</tr>';
  }).join('');

  var table = filtered.length === 0
    ? '<div class="empty">لا يوجد نتائج</div>'
    : '<div class="table-wrap"><table>'
    + '<tr><th>الطالب</th><th>الرقم الجامعي</th><th>الكود</th><th>الكورس</th><th>التاريخ</th></tr>'
    + rows + '</table></div>';

  return '<div class="page-title">التسجيلات</div>'
    + '<input class="search-bar" placeholder="ابحث بالاسم أو الرقم أو الكورس..."'
    + ' id="regsSearch" oninput="searchRegs()" value="' + filter + '">'
    + table;
}

function searchRegs() {
  var val = document.getElementById('regsSearch').value;
  document.getElementById('mainContent').innerHTML = pageRegistrations(val);
  focusInput('regsSearch');
}

function focusInput(id) {
  var inp = document.getElementById(id);
  if (inp) { inp.focus(); inp.setSelectionRange(inp.value.length, inp.value.length); }
}

showPage('students');
