<?php
session_start();
// Sesuaikan path berdasarkan struktur folder kamu (naik satu folder ke config)
require '../config/config.php';
require '../config/db_connect.php';

// 1. Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Cek Parameter ID
if (!isset($_GET['id'])) {
    // Jika tidak ada ID, kembalikan ke list
    header("Location: report_list.php");
    exit();
}

$report_id = $_GET['id'];
$msg = "";

// 3. PROSES UPDATE (Jika tombol Simpan ditekan)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $title = $_POST['title'];
    $location = $_POST['location']; // Sesuai nama kolom baru
    $incident_date = $_POST['incident_date']; // Sesuai nama kolom baru
    $description = $_POST['description'];
    $status = $_POST['status'];
    $type = $_POST['type'];

    // --- Logika Upload Foto ---
    $photo_sql = ""; // Default kosong (tidak update foto)
    
    // Cek apakah user upload foto baru?
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "uploads/"; // Folder uploads ada di sebelah file ini (public/uploads)
        
        // Buat nama file unik agar tidak bentrok
        $file_extension = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '.' . $file_extension;
        $target_file = $target_dir . $new_filename;
        
        // Coba upload
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            // Jika sukses upload, siapkan query update kolom photo
            // Simpan HANYA nama filenya ke database, bukan full path
            $photo_sql = ", photo = '$new_filename'"; 
        } else {
            $msg = "<div style='color:red; margin-bottom:10px;'>Gagal mengupload gambar baru.</div>";
        }
    }

    // --- Query Update Database ---
    // Perhatikan: Kita menyisipkan $photo_sql di tengah query
    $query = "UPDATE reports SET 
              title = '$title',
              location = '$location',
              incident_date = '$incident_date',
              description = '$description',
              status = '$status',
              type = '$type'
              $photo_sql
              WHERE report_id = '$report_id'";

    if ($conn->query($query) === TRUE) {
        $msg = "<div style='background:#d4edda; color:#155724; padding:10px; border-radius:5px; margin-bottom:15px;'>Data berhasil diperbarui!</div>";
    } else {
        $msg = "<div style='background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px;'>Error: " . $conn->error . "</div>";
    }
}

// 4. AMBIL DATA LAMA
// Query ini akan otomatis mengisi form saat halaman dibuka
$query_get = "SELECT * FROM reports WHERE report_id = '$report_id'";
$result = $conn->query($query_get);

if ($result->num_rows == 0) {
    echo "Laporan tidak ditemukan.";
    exit();
}

$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan - Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* CSS Tambahan agar form rapi */
        .edit-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 800px;
            margin: 20px auto;
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; color: #333; }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .btn-save {
            background-color: #4e73df;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-back {
            background-color: #858796;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 10px;
        }
        .img-preview {
            max-width: 200px;
            border-radius: 5px;
            border: 1px solid #ddd;
            padding: 5px;
            margin-top: 5px;
        }
    </style>
</head>
<body>

<div class="app">
    <!-- SIDEBAR (Sesuaikan menu dengan report_list.php) -->
    <nav class="sidebar">
        <div class="s-top">
            <!-- Asumsi path logo -->
            <img src="assets/img/logo.png" class="s-logo" alt="logo">
            <h3>LostFound</h3>
        </div>
        <ul class="menu">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="report_create.php">Buat Laporan</a></li>
            <li class="active"><a href="report_list.php">Daftar Laporan</a></li>
            <li><a href="profile.php">Profil Saya</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main class="main">
        <header class="main-head">
            <h2>Edit Laporan</h2>
        </header>
        
        <div class="edit-container">
            <?php echo $msg; ?>

            <!-- enctype="multipart/form-data" WAJIB ada untuk upload foto -->
            <form action="" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Judul Barang</label>
                    <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($data['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Tipe Laporan</label>
                    <select name="type" class="form-control">
                        <option value="Lost" <?php if($data['type'] == 'Lost') echo 'selected'; ?>>Kehilangan (Lost)</option>
                        <option value="Found" <?php if($data['type'] == 'Found') echo 'selected'; ?>>Ditemukan (Found)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Lokasi Kejadian</label>
                    <input type="text" name="location" class="form-control" value="<?php echo htmlspecialchars($data['location']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Tanggal Kejadian</label>
                    <input type="date" name="incident_date" class="form-control" value="<?php echo $data['incident_date']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status" class="form-control">
                        <option value="Hilang" <?php if($data['status'] == 'Hilang') echo 'selected'; ?>>Hilang</option>
                        <option value="Ditemukan" <?php if($data['status'] == 'Ditemukan') echo 'selected'; ?>>Ditemukan</option>
                        <option value="Selesai" <?php if($data['status'] == 'Selesai') echo 'selected'; ?>>Selesai</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4" required><?php echo htmlspecialchars($data['description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>Foto Barang (Saat Ini)</label>
                    <?php 
                        // Cek apakah ada foto di database
                        if (!empty($data['photo'])) {
                            echo '<img src="uploads/'.$data['photo'].'" class="img-preview" alt="Foto Laporan">';
                        } else {
                            echo '<p>Tidak ada foto.</p>';
                        }
                    ?>
                    <br><br>
                    <label>Ganti Foto (Biarkan kosong jika tidak ingin mengganti)</label>
                    <input type="file" name="photo" class="form-control">
                </div>

                <div style="margin-top: 30px;">
                    <a href="report_list.php" class="btn-back">Kembali</a>
                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                </div>

            </form>
        </div>
    </main>
</div>

</body>
</html>