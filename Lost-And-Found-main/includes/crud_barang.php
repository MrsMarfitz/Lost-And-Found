<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/db_connect.php';


function tambahBarang($data, $file) {
    global $conn;
    $user_id = 1;

    $type        = isset($data['jenis']) ? htmlspecialchars($data['jenis']) : (isset($data['type']) ? htmlspecialchars($data['type']) : 'lost');
    $title       = isset($data['judul']) ? htmlspecialchars($data['judul']) : (isset($data['title']) ? htmlspecialchars($data['title']) : '');
    $description = isset($data['deskripsi']) ? htmlspecialchars($data['deskripsi']) : (isset($data['description']) ? htmlspecialchars($data['description']) : '');
    $location    = isset($data['lokasi']) ? htmlspecialchars($data['lokasi']) : (isset($data['location']) ? htmlspecialchars($data['location']) : '');
    $date        = $data['date_event']; 

    $query = "INSERT INTO REPORTS (user_id, type, title, description, location_text, date_event, status) 
              VALUES ('$user_id', '$type', '$title', '$description', '$location', '$date', 'active')";

    if (mysqli_query($conn, $query)) {
        $report_id = mysqli_insert_id($conn);

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
                        mysqli_query($conn, "INSERT INTO REPORT_PHOTOS (report_id, image_path) VALUES ('$report_id', '$nama_baru')");
                    }
                }
            }
        }
        return true;
    }
    return false;
}

function tampilkanSemuaBarang() {
    global $conn;
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
        $row['photos_array'] = $row['all_photos'] ? explode(',', $row['all_photos']) : [];
        
        $row['thumbnail'] = !empty($row['photos_array']) ? $row['photos_array'][0] : null;
        
        $rows[] = $row;
    }
    return $rows;
}

function ambilSatuBarang($id) {
    global $conn;
    $id = (int)$id;
    
    $query = "SELECT * FROM REPORTS WHERE report_id = $id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
    
    if ($data) {
        $q_foto = mysqli_query($conn, "SELECT * FROM REPORT_PHOTOS WHERE report_id = $id");
        $fotos = [];
        while ($f = mysqli_fetch_assoc($q_foto)) {
            $fotos[] = $f;
        }
        $data['photos'] = $fotos;
        
        $data['image_path'] = isset($fotos[0]['image_path']) ? $fotos[0]['image_path'] : null;
    }
    
    return $data;
}

function ubahBarang($data, $file) {
    global $conn;
    $id = (int)$data['report_id'];
    
    $title       = isset($data['judul']) ? htmlspecialchars($data['judul']) : $data['title'];
    $description = isset($data['deskripsi']) ? htmlspecialchars($data['deskripsi']) : $data['description'];
    $location    = isset($data['lokasi']) ? htmlspecialchars($data['lokasi']) : $data['location'];
    $date        = $data['date_event'];

    $query = "UPDATE REPORTS SET title='$title', description='$description', location_text='$location', date_event='$date' WHERE report_id=$id";

    if (mysqli_query($conn, $query)) {
        
        if (isset($data['delete_photos'])) {
            foreach ($data['delete_photos'] as $photo_id) {
                $photo_id = (int)$photo_id;
                
                $q = mysqli_query($conn, "SELECT image_path FROM REPORT_PHOTOS WHERE photo_id = $photo_id");
                $dt = mysqli_fetch_assoc($q);
                
                if ($dt) {
                    $path = "../public/uploads/" . $dt['image_path'];
                    if (file_exists($path)) {
                        unlink($path);
                    }
                    mysqli_query($conn, "DELETE FROM REPORT_PHOTOS WHERE photo_id = $photo_id");
                }
            }
        }

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

function hapusBarang($id) {
    global $conn;
    $id = (int)$id;
    
    $q = mysqli_query($conn, "SELECT image_path FROM REPORT_PHOTOS WHERE report_id=$id");
    while ($dt = mysqli_fetch_assoc($q)) {
        if (!empty($dt['image_path'])) {
            $path = "../public/uploads/" . $dt['image_path'];
            if (file_exists($path)) unlink($path); 
        }
    }
    
    return mysqli_query($conn, "DELETE FROM REPORTS WHERE report_id=$id");
}

?>
