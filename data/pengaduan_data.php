<?php
// File untuk menangani data pengaduan
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
        'message' => 'Pengaduan berhasil disimpan ke file lokal',
        'data' => []
    ];
    
    // Simpan data ke file JSON
    $pengaduanData = [];
    
    if (file_exists('pengaduan.json')) {
        $pengaduanData = json_decode(file_get_contents('pengaduan.json'), true);
    }
    
    // Ambil data dari form
    $newPengaduan = [
        'id' => uniqid(),
        'nama' => $_POST['nama'] ?? '',
        'nik' => $_POST['nik'] ?? '',
        'alamat' => $_POST['alamat'] ?? '',
        'telepon' => $_POST['telepon'] ?? '',
        'jenis_pengaduan' => $_POST['jenis_pengaduan'] ?? '',
        'judul_pengaduan' => $_POST['judul_pengaduan'] ?? '',
        'isi_pengaduan' => $_POST['isi_pengaduan'] ?? '',
        'urgensi' => $_POST['urgensi'] ?? '',
        'bukti' => '',
        'tanggal_submit' => date('Y-m-d H:i:s'),
        'status' => 'pending'
    ];
    
    // Handle file upload jika ada
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/pengaduan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['bukti']['name'];
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $uploadPath)) {
            $newPengaduan['bukti'] = $fileName;
        }
    }
    
    // Tambahkan ke array data
    $pengaduanData[] = $newPengaduan;
    
    // Simpan ke file JSON
    file_put_contents('pengaduan.json', json_encode($pengaduanData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    echo json_encode($response);
    exit;
}

// Jika database tersedia, simpan ke database
try {
    // Buat tabel jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS pengaduan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(255) NOT NULL,
        nik VARCHAR(16) NOT NULL,
        alamat TEXT NOT NULL,
        telepon VARCHAR(20) NOT NULL,
        jenis_pengaduan VARCHAR(100) NOT NULL,
        judul_pengaduan VARCHAR(255) NOT NULL,
        isi_pengaduan TEXT NOT NULL,
        urgensi ENUM('rendah', 'sedang', 'tinggi', 'sangat_tinggi') NOT NULL,
        bukti VARCHAR(255),
        tanggal_submit DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'proses', 'selesai', 'ditolak') DEFAULT 'pending'
    )";
    
    $pdo->exec($sql);
    
    // Prepare statement untuk insert
    $stmt = $pdo->prepare("INSERT INTO pengaduan (nama, nik, alamat, telepon, jenis_pengaduan, judul_pengaduan, isi_pengaduan, urgensi, bukti) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $bukti = '';
    
    // Handle file upload jika ada
    if (isset($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/pengaduan/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['bukti']['name'];
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['bukti']['tmp_name'], $uploadPath)) {
            $bukti = $fileName;
        }
    }
    
    // Execute statement
    $stmt->execute([
        $_POST['nama'],
        $_POST['nik'],
        $_POST['alamat'],
        $_POST['telepon'],
        $_POST['jenis_pengaduan'],
        $_POST['judul_pengaduan'],
        $_POST['isi_pengaduan'],
        $_POST['urgensi'],
        $bukti
    ]);
    
    $response = [
        'status' => 'success',
        'message' => 'Pengaduan berhasil disimpan ke database',
        'data' => [
            'id' => $pdo->lastInsertId(),
            'nama' => $_POST['nama'],
            'urgensi' => $_POST['urgensi'],
            'tanggal_submit' => date('Y-m-d H:i:s')
        ]
    ];
    
    echo json_encode($response);
    
} catch(PDOException $e) {
    $response = [
        'status' => 'error',
        'message' => 'Gagal menyimpan pengaduan: ' . $e->getMessage()
    ];
    
    echo json_encode($response);
}
?>
