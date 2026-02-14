<?php
/**
 * Berkendara Ria - Subscribe Handler
 * 
 * Menerima data subscriber via POST (AJAX)
 * Menyimpan ke database MySQL
 * Return JSON response
 */

// Set JSON response header
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	http_response_code(405);
	echo json_encode([
		'success' => false,
		'message' => 'Method not allowed. Gunakan POST.'
	]);
	exit;
}

// Include database config
require_once __DIR__ . '/db_config.php';

// Get and sanitize input
$nama = isset($_POST['nama']) ? trim($_POST['nama']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$no_hp = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';

// --- Validation ---
$errors = [];

if (empty($nama)) {
	$errors[] = 'Nama tidak boleh kosong';
}

if (empty($email)) {
	$errors[] = 'Email tidak boleh kosong';
}
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
	$errors[] = 'Format email tidak valid';
}

if (empty($no_hp)) {
	$errors[] = 'No. HP/WhatsApp tidak boleh kosong';
}
elseif (!preg_match('/^[0-9+\-\s]{8,20}$/', $no_hp)) {
	$errors[] = 'Format nomor HP tidak valid';
}

if (!empty($errors)) {
	http_response_code(400);
	echo json_encode([
		'success' => false,
		'message' => implode(', ', $errors)
	]);
	exit;
}

// --- Sanitize ---
$nama = htmlspecialchars($nama, ENT_QUOTES, 'UTF-8');
$no_hp = htmlspecialchars($no_hp, ENT_QUOTES, 'UTF-8');

// --- Database Insert ---
try {
	$pdo = getDBConnection();

	if ($pdo === null) {
		throw new Exception('Gagal terhubung ke database. Pastikan XAMPP (MySQL) sudah aktif.');
	}

	// Check if email already exists
	$checkStmt = $pdo->prepare('SELECT id FROM subscribers WHERE email = :email');
	$checkStmt->execute([':email' => $email]);

	if ($checkStmt->fetch()) {
		echo json_encode([
			'success' => false,
			'message' => 'Email sudah terdaftar! Gunakan email lain.'
		]);
		exit;
	}

	// Insert new subscriber
	$stmt = $pdo->prepare('
        INSERT INTO subscribers (nama, email, no_hp, created_at)
        VALUES (:nama, :email, :no_hp, NOW())
    ');

	$stmt->execute([
		':nama' => $nama,
		':email' => $email,
		':no_hp' => $no_hp,
	]);

	echo json_encode([
		'success' => true,
		'message' => 'Selamat datang di Berkendara Ria, ' . $nama . '! 🏍️ Kami akan segera menghubungimu.'
	]);

}
catch (PDOException $e) {
	error_log('Subscribe error: ' . $e->getMessage());

	http_response_code(500);
	echo json_encode([
		'success' => false,
		'message' => 'Terjadi kesalahan database. Pastikan MySQL sudah aktif di XAMPP.'
	]);

}
catch (Exception $e) {
	http_response_code(500);
	echo json_encode([
		'success' => false,
		'message' => $e->getMessage()
	]);
}