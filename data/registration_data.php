<?php
// File untuk menangani data pendaftaran akun
header('Content-Type: application/json');

// Konfigurasi database (sesuaikan dengan kebutuhan)
$host = 'localhost';
$dbname = 'kecamatan_ngluyu_db';
$username = 'root';
$password = '';

try {
    // Koneksi ke database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Set charset
    $pdo->exec("SET NAMES utf8");
    
} catch(PDOException $e) {
    // Jika database tidak tersedia, simpan ke file JSON
    $response = [
        'status' => 'success',
        'message' => 'Pendaftaran berhasil disimpan ke file lokal',
        'data' => []
    ];
    
    // Simpan data ke file JSON
    $registrationData = [];
    
    if (file_exists('registration.json')) {
        $registrationData = json_decode(file_get_contents('registration.json'), true);
    }
    
    // Ambil data dari form
    $newRegistration = [
        'id' => uniqid(),
        'nama_lengkap' => $_POST['nama_lengkap'] ?? '',
        'tempat_lahir' => $_POST['tempat_lahir'] ?? '',
        'tanggal_lahir' => $_POST['tanggal_lahir'] ?? '',
        'jenis_kelamin' => $_POST['jenis_kelamin'] ?? '',
        'nik' => $_POST['nik'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
        'email' => $_POST['email'] ?? '',
        'telepon' => $_POST['telepon'] ?? '',
        'username' => $_POST['username'] ?? '',
        'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        'pekerjaan' => $_POST['pekerjaan'] ?? '',
        'pendidikan' => $_POST['pendidikan'] ?? '',
        'foto_ktp' => '',
        'tanggal_daftar' => date('Y-m-d H:i:s'),
        'status' => 'pending'
    ];
    
    // Handle file upload KTP
    if (isset($_FILES['foto_ktp']) && $_FILES['foto_ktp']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/ktp/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['foto_ktp']['name'];
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['foto_ktp']['tmp_name'], $uploadPath)) {
            $newRegistration['foto_ktp'] = $fileName;
        }
    }
    
    // Tambahkan ke array data
    $registrationData[] = $newRegistration;
    
    // Simpan ke file JSON
    file_put_contents('registration.json', json_encode($registrationData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    $response['data'] = [
        'id' => $newRegistration['id'],
        'nama' => $newRegistration['nama_lengkap'],
        'username' => $newRegistration['username'],
        'tanggal_daftar' => $newRegistration['tanggal_daftar']
    ];
    
    echo json_encode($response);
    exit;
}

// Jika database tersedia, simpan ke database
try {
    // Buat tabel jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_lengkap VARCHAR(255) NOT NULL,
        tempat_lahir VARCHAR(100) NOT NULL,
        tanggal_lahir DATE NOT NULL,
        jenis_kelamin ENUM('L', 'P') NOT NULL,
        nik VARCHAR(16) UNIQUE NOT NULL,
        alamat TEXT NOT NULL,
        email VARCHAR(255) UNIQUE NOT NULL,
        telepon VARCHAR(20) NOT NULL,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        pekerjaan VARCHAR(100),
        pendidikan VARCHAR(50),
        foto_ktp VARCHAR(255),
        tanggal_daftar DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'active', 'inactive', 'rejected') DEFAULT 'pending',
        last_login DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    
    // Cek apakah username atau email sudah ada
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ? OR nik = ?");
    $checkStmt->execute([$_POST['username'], $_POST['email'], $_POST['nik']]);
    
    if ($checkStmt->rowCount() > 0) {
        $response = [
            'status' => 'error',
            'message' => 'Username, email, atau NIK sudah terdaftar!'
        ];
        echo json_encode($response);
        exit;
    }
    
    // Prepare statement untuk insert
    $stmt = $pdo->prepare("INSERT INTO users (nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, nik, alamat, email, telepon, username, password, pekerjaan, pendidikan, foto_ktp) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $fotoKtp = '';
    
    // Handle file upload KTP
    if (isset($_FILES['foto_ktp']) && $_FILES['foto_ktp']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/ktp/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['foto_ktp']['name'];
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['foto_ktp']['tmp_name'], $uploadPath)) {
            $fotoKtp = $fileName;
        }
    }
    
    // Execute statement
    $stmt->execute([
        $_POST['nama_lengkap'],
        $_POST['tempat_lahir'],
        $_POST['tanggal_lahir'],
        $_POST['jenis_kelamin'],
        $_POST['nik'],
        $_POST['alamat'],
        $_POST['email'],
        $_POST['telepon'],
        $_POST['username'],
        password_hash($_POST['password'], PASSWORD_DEFAULT),
        $_POST['pekerjaan'],
        $_POST['pendidikan'],
        $fotoKtp
    ]);
    
    $response = [
        'status' => 'success',
        'message' => 'Pendaftaran berhasil disimpan ke database',
        'data' => [
            'id' => $pdo->lastInsertId(),
            'nama' => $_POST['nama_lengkap'],
            'username' => $_POST['username'],
            'email' => $_POST['email'],
            'tanggal_daftar' => date('Y-m-d H:i:s')
        ]
    ];
    
    echo json_encode($response);
    
} catch(PDOException $e) {
    $response = [
        'status' => 'error',
        'message' => 'Gagal menyimpan pendaftaran: ' . $e->getMessage()
    ];
    
    echo json_encode($response);
}
?>
