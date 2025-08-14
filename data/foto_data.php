<?php
// File untuk menangani data foto
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
        'message' => 'Foto berhasil disimpan ke file lokal',
        'data' => []
    ];
    
    // Simpan data ke file JSON
    $fotoData = [];
    
    if (file_exists('foto.json')) {
        $fotoData = json_decode(file_get_contents('foto.json'), true);
    }
    
    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/foto/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['photo']['name'];
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
            $newFoto = [
                'id' => uniqid(),
                'caption' => $_POST['caption'] ?? '',
                'filename' => $fileName,
                'original_name' => $_FILES['photo']['name'],
                'file_size' => $_FILES['photo']['size'],
                'file_type' => $_FILES['photo']['type'],
                'upload_date' => date('Y-m-d H:i:s'),
                'status' => 'active'
            ];
            
            // Tambahkan ke array data
            $fotoData[] = $newFoto;
            
            // Simpan ke file JSON
            file_put_contents('foto.json', json_encode($fotoData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            $response['data'] = $newFoto;
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Gagal mengupload file'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Tidak ada file yang diupload'
        ];
    }
    
    echo json_encode($response);
    exit;
}

// Jika database tersedia, simpan ke database
try {
    // Buat tabel jika belum ada
    $sql = "CREATE TABLE IF NOT EXISTS foto (
        id INT AUTO_INCREMENT PRIMARY KEY,
        caption VARCHAR(255) NOT NULL,
        filename VARCHAR(255) NOT NULL,
        original_name VARCHAR(255) NOT NULL,
        file_size INT NOT NULL,
        file_type VARCHAR(100) NOT NULL,
        upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
        status ENUM('active', 'inactive', 'deleted') DEFAULT 'active'
    )";
    
    $pdo->exec($sql);
    
    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/foto/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . $_FILES['photo']['name'];
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
            // Prepare statement untuk insert
            $stmt = $pdo->prepare("INSERT INTO foto (caption, filename, original_name, file_size, file_type) VALUES (?, ?, ?, ?, ?)");
            
            // Execute statement
            $stmt->execute([
                $_POST['caption'],
                $fileName,
                $_FILES['photo']['name'],
                $_FILES['photo']['size'],
                $_FILES['photo']['type']
            ]);
            
            $response = [
                'status' => 'success',
                'message' => 'Foto berhasil disimpan ke database',
                'data' => [
                    'id' => $pdo->lastInsertId(),
                    'caption' => $_POST['caption'],
                    'filename' => $fileName,
                    'upload_date' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Gagal mengupload file'
            ];
        }
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Tidak ada file yang diupload'
        ];
    }
    
    echo json_encode($response);
    
} catch(PDOException $e) {
    $response = [
        'status' => 'error',
        'message' => 'Gagal menyimpan foto: ' . $e->getMessage()
    ];
    
    echo json_encode($response);
}
?>
