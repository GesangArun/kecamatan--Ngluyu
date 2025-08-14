# Kecamatan Ngluyu - Linktree Aplikasi Kecamatan

Aplikasi Linktree modern untuk layanan kecamatan dengan fitur laporan, pengaduan, galeri foto, dan pendaftaran akun.

## 🚀 Fitur Utama

### 1. **Halaman Utama (Linktree)**
- Desain modern dan responsif
- Logo Kecamatan Ngluyu dengan identitas visual yang menarik
- 4 menu utama dengan ilustrasi yang unik
- Animasi loading dan hover effects
- Tombol scroll to top

### 2. **Laporan**
- Form laporan lengkap dengan validasi
- Field: Nama, NIK, Alamat, Telepon, Jenis Laporan, Judul, Isi
- Upload lampiran (PDF, DOC, JPG, PNG)
- Validasi NIK 16 digit dan format telepon
- Penyimpanan data ke database atau file JSON

### 3. **Pengaduan**
- Form pengaduan dengan tingkat urgensi
- Field: Nama, NIK, Alamat, Telepon, Jenis, Judul, Isi, Urgensi
- Upload bukti pendukung
- Validasi data lengkap
- Penyimpanan data terstruktur

### 4. **Galeri Foto**
- Upload foto dengan caption
- Galeri foto yang responsif
- Preview dan hapus foto
- Validasi ukuran dan format file
- Penyimpanan metadata foto

### 5. **Pendaftaran Akun**
- Form pendaftaran 3 langkah
- Progress bar dan step indicator
- Field lengkap: Data pribadi, Kontak, Tambahan
- Upload foto KTP
- Validasi password dan konfirmasi
- Penyimpanan data user

## 🛠️ Teknologi yang Digunakan

- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Backend**: PHP 7.4+
- **Database**: MySQL (opsional) atau File JSON
- **Styling**: CSS Grid, Flexbox, Gradients
- **Icons**: Font Awesome 6.0
- **Fonts**: Google Fonts (Inter)
- **Responsive**: Mobile-first design

## 📁 Struktur Folder

```
Kecamatan.html/
├── index.html              # Halaman utama Linktree
├── laporan.html            # Halaman form laporan
├── pengaduan.html          # Halaman form pengaduan
├── foto.html               # Halaman galeri foto
├── pendaftaran.html        # Halaman pendaftaran akun
├── styles.css              # File CSS utama
├── script.js               # File JavaScript utama
├── data/                   # Folder backend
│   ├── laporan_data.php    # Handler data laporan
│   ├── pengaduan_data.php  # Handler data pengaduan
│   ├── foto_data.php       # Handler data foto
│   └── registration_data.php # Handler data pendaftaran
├── uploads/                # Folder upload file (auto-created)
│   ├── laporan/            # File laporan
│   ├── pengaduan/          # File pengaduan
│   ├── foto/               # File foto
│   └── ktp/                # File KTP
└── README.md               # Dokumentasi ini
```

## 🚀 Cara Menjalankan

### 1. **Setup Lokal (XAMPP/WAMP)**
```bash
# Copy folder ke htdocs
cp -r Kecamatan.html /path/to/htdocs/

# Akses melalui browser
http://localhost/Kecamatan.html/
```

### 2. **Setup Database (Opsional)**
```sql
-- Buat database
CREATE DATABASE kecamatan_ngluyu_db;
USE kecamatan_ngluyu_db;

-- Tabel akan dibuat otomatis saat pertama kali submit
```

### 3. **Setup File JSON (Default)**
- Jika database tidak tersedia, data akan disimpan ke file JSON
- File akan dibuat otomatis di folder `data/`

## ⚙️ Konfigurasi

### Database Configuration
Edit file di folder `data/` untuk mengubah konfigurasi database:

```php
$host = 'localhost';
$dbname = 'kecamatan_ngluyu_db';
$username = 'root';
$password = '';
```

### File Upload Settings
- **Laporan**: Max 5MB, format: PDF, DOC, DOCX, JPG, PNG
- **Pengaduan**: Max 10MB, format: PDF, DOC, DOCX, JPG, PNG
- **Foto**: Max 5MB, format: JPG, JPEG, PNG, GIF
- **KTP**: Max 2MB, format: JPG, JPEG, PNG

## 📱 Responsive Design

- **Desktop**: Layout optimal untuk layar besar
- **Tablet**: Adaptif untuk layar medium
- **Mobile**: Mobile-first design dengan touch-friendly interface

## 🔒 Keamanan

- Validasi input di frontend dan backend
- Sanitasi data sebelum penyimpanan
- Password hashing menggunakan `password_hash()`
- Prepared statements untuk mencegah SQL injection
- Validasi file upload (type, size, extension)

## 🎨 Customization

### Warna dan Theme
Edit file `styles.css` untuk mengubah:
- Gradient backgrounds
- Color schemes
- Border radius
- Shadow effects

### Logo dan Branding
- Ganti logo di `index.html`
- Ubah nama brand "Kecamatan Ngluyu"
- Sesuaikan warna primary

## 📊 Data Storage

### Database Tables
- **laporan**: Data laporan masyarakat
- **pengaduan**: Data pengaduan masyarakat
- **foto**: Metadata galeri foto
- **users**: Data user terdaftar

### File JSON (Fallback)
- `laporan.json`: Data laporan
- `pengaduan.json`: Data pengaduan
- `foto.json`: Data foto
- `registration.json`: Data pendaftaran

## 🚧 Development

### Menambah Fitur Baru
1. Buat file HTML baru
2. Tambahkan styling di `styles.css`
3. Buat handler PHP di folder `data/`
4. Update navigasi di `index.html`

### Debug Mode
- Aktifkan error reporting di PHP
- Cek console browser untuk JavaScript errors
- Monitor file logs untuk debugging

## 📞 Support

Untuk pertanyaan atau bantuan teknis:
- Buat issue di repository
- Hubungi tim development
- Konsultasi dengan admin sistem

## 📄 License

Project ini dikembangkan untuk internal use. Semua hak cipta dilindungi.

---

**Kecamatan Ngluyu** - Solusi Digital untuk Layanan Kecamatan Modern 🏛️✨
