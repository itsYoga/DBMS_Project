<?php
// 引入資料庫連接
include 'db.php';

// 啟用 session
session_start();

// 檢查是否已登入
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// 從 session 中取得使用者名稱
$username = $_SESSION['username'];

// 查詢 student_id
$student_id = getStudentIdByUsername($conn, $username);

if ($student_id) {
    // 查詢並顯示課表
    $schedule = getStudentSchedule($conn, $student_id);
    displaySchedule($schedule, $username);
} else {
    echo "找不到該使用者的學生編號。";
}

// 函數：根據使用者名稱取得 student_id
function getStudentIdByUsername($conn, $username) {
    $query = "SELECT student_id FROM Users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->bind_result($student_id);
    $stmt->fetch();
    $stmt->close();
    return $student_id;
}

// 函數：取得學生的課表
function getStudentSchedule($conn, $student_id) {
    $query = "SELECT Courses.course_name, Course_Schedules.day_of_week, Course_Schedules.start_time, Course_Schedules.end_time
              FROM Enrollment_Records
              JOIN Courses ON Enrollment_Records.course_id = Courses.course_id
              JOIN Course_Schedules ON Courses.course_id = Course_Schedules.course_id
              JOIN Classrooms ON Courses.classroom_id = Classrooms.classroom_id
              WHERE Enrollment_Records.student_id = ?";

    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        // 準備查詢失敗，輸出錯誤訊息
        die("Prepare failed: " . $conn->error);
    }
    
    // 綁定參數並執行查詢
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $schedule = $result->fetch_all(MYSQLI_ASSOC);
    
    $stmt->close();
    return $schedule;
}

// 函數：顯示課表
function displaySchedule($schedule, $username) {
    // 將課表轉換成每週的格式
    $weekly_schedule = [];
    foreach ($schedule as $class) {
        $day = $class['day_of_week'];
        $time_slots = getTimeSlots($class['start_time'], $class['end_time']);
        foreach ($time_slots as $time_slot) {
            $weekly_schedule[$day][$time_slot] = "{$class['course_name']}<br>{$class['start_time']} - {$class['end_time']}";
        }
    }

    // 顯示週課表
    echo "<h1>{$username} 的課表</h1>";
    echo "<table border='1' cellspacing='0' cellpadding='5' style='text-align: center;'>";
    echo "<tr><th>時間</th><th>星期一</th><th>星期二</th><th>星期三</th><th>星期四</th><th>星期五</th></tr>";

    // 節次對應時間
    $time_slots = [
        1 => "06:20-08:10",
        2 => "08:20-09:10",
        3 => "09:20-10:10",
        4 => "10:20-11:10",
        5 => "11:15-12:05",
        6 => "13:10-14:00",
        7 => "14:10-15:00",
        8 => "15:10-16:00",
    ];

    foreach ($time_slots as $slot => $time) {
        echo "<tr>";
        echo "<td>$time</td>";
        for ($day = 1; $day <= 5; $day++) {
            echo "<td>" . ($weekly_schedule[$day][$slot] ?? "") . "</td>";
        }
        echo "</tr>";
    }

    echo "</table>";

    // 返回主畫面按鈕
    echo "<br><p><a href='enter.php'><button type='button'>返回主畫面</button></a></p>";
}

// 函數：根據時間區間決定節次
function getTimeSlots($start_time, $end_time) {
    $slots = [];
    $time_slots = [
        1 => ["06:20", "08:10"],
        2 => ["08:20", "09:10"],
        3 => ["09:20", "10:10"],
        4 => ["10:20", "11:10"],
        5 => ["11:15", "12:05"],
        6 => ["13:10", "14:00"],
        7 => ["14:10", "15:00"],
        8 => ["15:10", "16:00"],
    ];

    foreach ($time_slots as $slot => $range) {
        // 檢查是否在課程時間範圍內
        if ($start_time < $range[1] && $end_time > $range[0]) {
            $slots[] = $slot;
        }
    }

    return $slots;
}

?>
