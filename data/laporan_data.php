<?php
// File untuk menangani data laporan
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
        'message' => 'Laporan berhasil disimpan ke file lokal',
        'data' => []
    ];
    
    // Simpan data ke file JSON
    $laporanData = [];
    
    if (file_exists('laporan.json')) {
        $laporanData = json_decode(file_get_contents('laporan.json'), true);
    }
    
    // Ambil data dari form
    $newLaporan = [
        'id' => uniqid(),
        'nama' => $_POST['nama'] ?? '',
        'nik' => $_POST['nik'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
        'telepon' => $_POST['telepon'] ?? '',
        'jenis_laporan' => $_POST['jenis_laporan'] ?? '',
        'judul_laporan' => $_POST['judul_laporan'] ?? '',
        'isi_laporan' => $_POST['isi_laporan'] ?? '',
        'lampiran' => '',
        'tanggal_submit' => date('Y-m-d H:i:s'),
        'status' => 'pending'
    ];
    
    // Handle file upload jika ada
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/laporan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['lampiran']['name'];
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['lampiran']['tmp_name'], $uploadPath)) {
            $newLaporan['lampiran'] = $fileName;
        }
    }
    
    // Tambahkan ke array data
    $laporanData[] = $newLaporan;
    
    // Simpan ke file JSON
    file_put_contents('laporan.json', json_encode($laporanData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo json_encode($response);
    exit;
}

// Jika database tersedia, simpan ke database
try {
    // Buat tabel jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS laporan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(255) NOT NULL,
        nik VARCHAR(16) NOT NULL,
        alamat TEXT NOT NULL,
        telepon VARCHAR(20) NOT NULL,
        jenis_laporan VARCHAR(100) NOT NULL,
        judul_laporan VARCHAR(255) NOT NULL,
        isi_laporan TEXT NOT NULL,
        lampiran VARCHAR(255),
        tanggal_submit DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'proses', 'selesai', 'ditolak') DEFAULT 'pending'
    )";
    
    $pdo->exec($sql);
    
    // Prepare statement untuk insert
    $stmt = $pdo->prepare("INSERT INTO laporan (nama, nik, alamat, telepon, jenis_laporan, judul_laporan, isi_laporan, lampiran) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $lampiran = '';
    
    // Handle file upload jika ada
    if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/laporan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['lampiran']['name'];
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['lampiran']['tmp_name'], $uploadPath)) {
            $lampiran = $fileName;
        }
    }
    
    // Execute statement
    $stmt->execute([
        $_POST['nama'],
        $_POST['nik'],
        $_POST['alamat'],
        $_POST['telepon'],
        $_POST['jenis_laporan'],
        $_POST['judul_laporan'],
        $_POST['isi_laporan'],
        $lampiran
    ]);
    
    $response = [
        'status' => 'success',
        'message' => 'Laporan berhasil disimpan ke database',
        'data' => [
            'id' => $pdo->lastInsertId(),
            'nama' => $_POST['nama'],
            'tanggal_submit' => date('Y-m-d H:i:s')
        ]
    ];
    
    echo json_encode($response);
    
} catch(PDOException $e) {
    $response = [
        'status' => 'error',
        'message' => 'Gagal menyimpan laporan: ' . $e->getMessage()
    ];
    
    echo json_encode($response);
}
?>
