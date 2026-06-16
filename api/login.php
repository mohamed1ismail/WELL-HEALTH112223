<?php
require_once 'db.php';
corsHeaders();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $body = getBody();
    $inputUser = $body['username'] ?? '';
    $inputPass = $body['password'] ?? '';

    if (empty($inputUser) || empty($inputPass)) {
        sendJSON(['success' => false, 'error' => 'اسم المستخدم وكلمة المرور مطلوبة'], 400);
    }

    try {
        // جلب بيانات الحساب من جدول الإعدادات
        $stmt = $db->query("SELECT adminUsername, adminPassword FROM settings LIMIT 1");
        $settings = $stmt->fetch();

        $dbUsername = ($settings && !empty($settings['adminUsername'])) ? $settings['adminUsername'] : 'admin';
        $dbPassword = ($settings && !empty($settings['adminPassword'])) ? $settings['adminPassword'] : 'wellhealth123';

        if ($inputUser === $dbUsername && $inputPass === $dbPassword) {
            sendJSON(['success' => true]);
        } else {
            sendJSON(['success' => false, 'error' => 'اسم المستخدم أو كلمة المرور غير صحيحة'], 401);
        }
    } catch (Exception $e) {
        sendJSON(['success' => false, 'error' => $e->getMessage()], 500);
    }
} else {
    sendJSON(['error' => 'Method not allowed'], 405);
}
