<?php
// Pastikan path ini benar (gunakan __DIR__ biar aman)
require_once __DIR__ . '/../config/config.php';

// --- 1. TAMBAH DATA (SUPPORT MULTIPLE UPLOAD) ---
function tambahBarang($data, $file) {
    global $conn;
    $user_id = 1; // Dummy User ID

    // Mapping Input (Indo -> Inggris)
    $type        = isset($data['jenis']) ? htmlspecialchars($data['jenis']) : (isset($data['type']) ? htmlspecialchars($data['type']) : 'lost');
    $title       = isset($data['judul']) ? htmlspecialchars($data['judul']) : (isset($data['title']) ? htmlspecialchars($data['title']) : '');
    $description = isset($data['deskripsi']) ? htmlspecialchars($data['deskripsi']) : (isset($data['description']) ? htmlspecialchars($data['description']) : '');
    $location    = isset($data['lokasi']) ? htmlspecialchars($data['lokasi']) : (isset($data['location']) ? htmlspecialchars($data['location']) : '');
    $date        = $data['date_event']; 

    // Insert Data Laporan Dulu
    $query = "INSERT INTO REPORTS (user_id, type, title, description, location_text, date_event, status) 
              VALUES ('$user_id', '$type', '$title', '$description', '$location', '$date', 'active')";

    if (mysqli_query($conn, $query)) {
        $report_id = mysqli_insert_id($conn);

        // === LOGIC BARU: LOOPING UPLOAD BANYAK FOTO ===
        // Cek apakah ada file yang diupload? (Array)
        if (!empty($file['foto']['name'][0])) {
            $total_files = count($file['foto']['name']); // Hitung ada berapa file

            // Loop sebanyak file yang ada
            for ($i = 0; $i < $total_files; $i++) {
                $nama_file = $file['foto']['name'][$i];
                $tmp_file  = $file['foto']['tmp_name'][$i];
                
                // Pastikan nama file tidak kosong
                if (!empty($nama_file)) {
                    // Beri nama unik (tambah index $i biar gak bentrok)
                    $nama_baru = time() . "_" . $i . "_" . $nama_file;
                    $target    = "../public/uploads/" . $nama_baru;
                    
                    // Buat folder uploads jika belum ada
                    if (!is_dir("../public/uploads")) mkdir("../public/uploads");

                    if (move_uploaded_file($tmp_file, $target)) {
                        // Simpan nama file ke tabel REPORT_PHOTOS
                        mysqli_query($conn, "INSERT INTO REPORT_PHOTOS (report_id, image_path) VALUES ('$report_id', '$nama_baru')");
                    }
                }
            }
        }
        return true;
    }
    return false;
}

// --- 2. READ (UPDATE: AMBIL SEMUA FOTO JADI STRING) ---
function tampilkanSemuaBarang() {
    global $conn;
    // Kita pakai GROUP_CONCAT untuk menggabungkan semua nama file foto jadi satu baris dipisah koma
    $query = "SELECT r.*, u.username, 
              GROUP_CONCAT(rp.image_path) as all_photos
              FROM REPORTS r
              JOIN USERS u ON r.user_id = u.user_id
              LEFT JOIN REPORT_PHOTOS rp ON r.report_id = rp.report_id
              GROUP BY r.report_id
              ORDER BY r.created_at DESC";
              
    $result = mysqli_query($conn, $query);
    $rows = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Pecah string "foto1.jpg,foto2.jpg" menjadi Array PHP biar gampang diproses
        $row['photos_array'] = $row['all_photos'] ? explode(',', $row['all_photos']) : [];
        
        // Ambil foto pertama aja buat Thumbnail di list
        $row['thumbnail'] = !empty($row['photos_array']) ? $row['photos_array'][0] : null;
        
        $rows[] = $row;
    }
    return $rows;
}

// --- 3. READ SINGLE (UNTUK EDIT - AMBIL SEMUA FOTO) ---
function ambilSatuBarang($id) {
    global $conn;
    $id = (int)$id;
    
    // 1. Ambil Data Laporannya (Judul, Deskripsi, dll)
    $query = "SELECT * FROM REPORTS WHERE report_id = $id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    
    // 2. Ambil Fotonya (Bisa banyak, jadi kita ambil array)
    if ($data) {
        $q_foto = mysqli_query($conn, "SELECT * FROM REPORT_PHOTOS WHERE report_id = $id");
        $fotos = [];
        while ($f = mysqli_fetch_assoc($q_foto)) {
            $fotos[] = $f; // Masukkan foto ke keranjang array
        }
        $data['photos'] = $fotos; // Simpan di dalam variabel data utama
        
        // Simpan 1 foto utama buat thumbnail (biar kompatibel sama kode lama)
        $data['image_path'] = isset($fotos[0]['image_path']) ? $fotos[0]['image_path'] : null;
    }
    
    return $data;
}

// --- 4. UPDATE (EDIT - UPDATE DATA + NAMBAH FOTO + HAPUS FOTO LAMA) ---
function ubahBarang($data, $file) {
    global $conn;
    $id = (int)$data['report_id'];
    
    $title       = isset($data['judul']) ? htmlspecialchars($data['judul']) : $data['title'];
    $description = isset($data['deskripsi']) ? htmlspecialchars($data['deskripsi']) : $data['description'];
    $location    = isset($data['lokasi']) ? htmlspecialchars($data['lokasi']) : $data['location'];
    $date        = $data['date_event'];

    // 1. Update Data Teks
    $query = "UPDATE REPORTS SET title='$title', description='$description', location_text='$location', date_event='$date' WHERE report_id=$id";

    if (mysqli_query($conn, $query)) {
        
        // 2. LOGIC HAPUS FOTO LAMA (Jika ada yang dicentang)
        if (isset($data['delete_photos'])) {
            foreach ($data['delete_photos'] as $photo_id) {
                $photo_id = (int)$photo_id;
                
                // Ambil nama file dulu biar bisa dihapus dari folder
                $q = mysqli_query($conn, "SELECT image_path FROM REPORT_PHOTOS WHERE photo_id = $photo_id");
                $dt = mysqli_fetch_assoc($q);
                
                if ($dt) {
                    $path = "../public/uploads/" . $dt['image_path'];
                    // Hapus File Fisik
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    // Hapus Record Database
                    mysqli_query($conn, "DELETE FROM REPORT_PHOTOS WHERE photo_id = $photo_id");
                }
            }
        }

        // 3. LOGIC TAMBAH FOTO BARU
        if (!empty($file['foto']['name'][0])) {
            $total_files = count($file['foto']['name']);
            for ($i = 0; $i < $total_files; $i++) {
                $nama_file = $file['foto']['name'][$i];
                $tmp_file  = $file['foto']['tmp_name'][$i];
                
                if (!empty($nama_file)) {
                    $nama_baru = time() . "_" . $i . "_" . $nama_file;
                    $target    = "../public/uploads/" . $nama_baru;
                    
                    if (!is_dir("../public/uploads")) mkdir("../public/uploads");
                    
                    if (move_uploaded_file($tmp_file, $target)) {
                        mysqli_query($conn, "INSERT INTO REPORT_PHOTOS (report_id, image_path) VALUES ('$id', '$nama_baru')");
                    }
                }
            }
        }
        return true;
    }
    return false;
}

// --- 5. DELETE (HAPUS SEMUA FOTO TERKAIT) ---
function hapusBarang($id) {
    global $conn;
    $id = (int)$id;
    
    // 1. Ambil SEMUA foto yang terkait laporan ini
    $q = mysqli_query($conn, "SELECT image_path FROM REPORT_PHOTOS WHERE report_id=$id");
    while ($dt = mysqli_fetch_assoc($q)) {
        if (!empty($dt['image_path'])) {
            $path = "../public/uploads/" . $dt['image_path'];
            // Hapus file fisik satu per satu
            if (file_exists($path)) unlink($path); 
        }
    }
    
    // 2. Hapus Data di Database
    return mysqli_query($conn, "DELETE FROM REPORTS WHERE report_id=$id");
}
?>