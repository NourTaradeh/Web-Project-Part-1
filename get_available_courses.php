<?php
session_start();
include("db.php");

$student_id = (int)$_SESSION['user_id'];
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

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
while ($c = $courses_result->fetch_assoc()) {
    $cnt_result = $conn->query("SELECT COUNT(*) as cnt FROM registrations WHERE course_id = " . $c['id']);
    $cnt = $cnt_result->fetch_assoc()['cnt'];

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

    $c['cnt'] = $cnt;
    $c['seats_left'] = $c['capacity'] - $cnt;
    $c['pct'] = $c['capacity'] > 0 ? round($cnt / $c['capacity'] * 100) : 0;
    $c['is_full'] = $cnt >= $c['capacity'];
    $c['already_registered'] = $already_registered;
    $c['prereqs_ok'] = $prereqs_ok;
    $c['prereq_text'] = count($prereq_codes) == 0 ? 'لا يوجد متطلبات' : 'متطلب: ' . implode(', ', $prereq_codes);

    $courses[] = $c;
}

echo json_encode($courses);
?>
