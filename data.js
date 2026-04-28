var DEFAULT_USERS = [
  {
    id: 1,
    role: 'admin',
    name: 'المدير',
    email: 'admin@ppu.edu.ps',
    password: 'admin123',
    studentNum: null
  }
];

var DEFAULT_COURSES = [];

var DEFAULT_REGISTRATIONS = [];

var DEFAULT_COMPLETED = {};

function getUsers() {
  var stored = localStorage.getItem('ppu_users');
  return stored ? JSON.parse(stored) : DEFAULT_USERS;
}

function saveUsers(users) {
  localStorage.setItem('ppu_users', JSON.stringify(users));
}

function getCourses() {
  var stored = localStorage.getItem('ppu_courses');
  return stored ? JSON.parse(stored) : DEFAULT_COURSES;
}

function saveCourses(courses) {
  localStorage.setItem('ppu_courses', JSON.stringify(courses));
}

function getRegistrations() {
  var stored = localStorage.getItem('ppu_registrations');
  return stored ? JSON.parse(stored) : DEFAULT_REGISTRATIONS;
}

function saveRegistrations(regs) {
  localStorage.setItem('ppu_registrations', JSON.stringify(regs));
}

function getCompleted() {
  var stored = localStorage.getItem('ppu_completed');
  return stored ? JSON.parse(stored) : DEFAULT_COMPLETED;
}

function saveCompleted(completed) {
  localStorage.setItem('ppu_completed', JSON.stringify(completed));
}

function getCurrentUser() {
  var stored = sessionStorage.getItem('ppu_current_user');
  return stored ? JSON.parse(stored) : null;
}

function setCurrentUser(user) {
  sessionStorage.setItem('ppu_current_user', JSON.stringify(user));
}

function logout() {
  sessionStorage.removeItem('ppu_current_user');
  window.location.href = 'login.html';
}

function requireLogin(expectedRole) {
  var user = getCurrentUser();
  if (!user) {
    window.location.href = 'login.html';
    return null;
  }
  if (expectedRole && user.role !== expectedRole) {
    if (user.role === 'admin') window.location.href = 'admin.html';
    else window.location.href = 'student.html';
    return null;
  }
  return user;
}

function initData() {
  if (!localStorage.getItem('ppu_users')) saveUsers(DEFAULT_USERS);
  if (!localStorage.getItem('ppu_courses')) saveCourses(DEFAULT_COURSES);
  if (!localStorage.getItem('ppu_registrations')) saveRegistrations(DEFAULT_REGISTRATIONS);
  if (!localStorage.getItem('ppu_completed')) saveCompleted(DEFAULT_COMPLETED);
}

initData();