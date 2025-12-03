<?php
session_start(); // <--- WAJIB ADA DI PALING ATAS
// Jika belum login, tendang ke login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Dashboard - Lost & Found Campus</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* --- CSS ASLI KAMU (TIDAK DIUBAH) --- */
    .report-gallery {
      display: flex;
      overflow-x: auto;
      gap: 15px;
      padding-bottom: 20px; 
      scrollbar-width: thin;
      scrollbar-color: #888 #f1f1f1; 
      -webkit-overflow-scrolling: touch; 
    }

    .report-gallery::-webkit-scrollbar {
      height: 8px;
    }
    .report-gallery::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    .report-gallery::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }
    .report-gallery::-webkit-scrollbar-thumb:hover {
      background: #555;
    }

    .report-card-item {
      flex: 0 0 auto; 
      width: 250px; 
      height: 180px; 
      border-radius: 10px;
      overflow: hidden;
      position: relative;
      cursor: pointer;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      transition: transform 0.2s ease-in-out;
      background: #fff; 
    }

    .report-card-item:hover {
      transform: translateY(-5px);
    }

    .report-card-item img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      position: absolute;
      top: 0;
      left: 0;
    }

    .report-card-overlay {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 100%);
      color: white;
      padding: 15px;
      display: flex;
      flex-direction: column;
      justify-content: flex-end;
      min-height: 50%; 
    }

    .report-card-overlay h4 {
      margin: 0 0 5px 0;
      font-size: 1.1em;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .report-card-overlay .status-tag {
      background: #555;
      color: white;
      padding: 3px 8px;
      border-radius: 5px;
      font-size: 0.8em;
      align-self: flex-start;
    }

    /* Modal styles */
    .modal {
      display: none; 
      position: fixed; 
      z-index: 1000; 
      left: 0;
      top: 0;
      width: 100%; 
      height: 100%; 
      overflow: auto; 
      background-color: rgba(0,0,0,0.7); 
      animation: fadeIn 0.3s;
    }

    .modal-content {
      background-color: #fefefe;
      margin: 5% auto; 
      padding: 0; 
      border-radius: 10px;
      width: 80%; 
      max-width: 900px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.3);
      position: relative;
      overflow: hidden; 
      animation: slideInUp 0.4s;
    }

    .modal-header {
        position: relative;
        height: 350px; 
        overflow: hidden;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
    }
    .modal-header img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .modal-header .close-button {
      position: absolute;
      top: 15px;
      right: 15px;
      color: white;
      font-size: 30px;
      font-weight: bold;
      cursor: pointer;
      text-shadow: 0 0 5px rgba(0,0,0,0.7);
      background-color: rgba(0,0,0,0.5);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .modal-header .close-button:hover,
    .modal-header .close-button:focus {
      color: #bbb;
      text-decoration: none;
      cursor: pointer;
    }

    .modal-body {
        padding: 30px;
        background-color: #fff;
    }
    .modal-body h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 1.8em;
        color: #333;
    }
    .modal-body p {
        line-height: 1.6;
        color: #555;
        margin-bottom: 10px;
    }
    .modal-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
    .modal-details div {
        font-size: 0.95em;
    }
    .modal-details strong {
        display: block;
        margin-bottom: 5px;
        color: #333;
    }
    .modal-actions {
        margin-top: 30px;
        display: flex;
        gap: 10px;
        justify-content: flex-start;
    }
    .modal-actions .btn {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: background-color 0.2s ease;
    }
    .modal-actions .btn-primary {
        background-color: #3b82f6;
        color: white;
    }
    .modal-actions .btn-primary:hover {
        background-color: #2563eb;
    }
    .modal-actions .btn-secondary {
        background-color: #e0e7ff;
        color: #4f46e5;
    }
    .modal-actions .btn-secondary:hover {
        background-color: #c7d2fe;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideInUp {
      from { transform: translateY(50px); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }
  </style>
</head>
<body>

  <div class="app">
    <nav class="sidebar">
      <div class="s-top">
        <img src="assets/img/logo.png" class="s-logo" alt="logo">
        <h3>LostFound</h3>
      </div>

      <ul class="menu">
        <li class="active">Dashboard</li>
        <li><a href="report_create.php">Buat Laporan</a></li>
        <li><a href="report_list.php">Daftar Laporan</a></li>
        <li><a href="profile.php">Profil Saya</a></li>
        
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') : ?>
            <li><a href="../admin/index.php">Admin Panel</a></li>
        <?php endif; ?>
        </ul>

      <div class="s-bottom">
        <img src="assets/img/user.jpg" class="avatar" alt="user">
        <div>
          <div class="small"><?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'Pengguna'; ?></div> 
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </nav>

    <main class="main">
      <header class="main-head">
        <h2>Dashboard Pengguna</h2>
        <div class="head-actions">
          <input class="search" placeholder="Cari barang...">
          <a href="report_create.php" class="btn-primary small">Laporan Baru</a>
        </div>
      </header>

      <section class="grid">
        <div class="card stat">
          <h3>Ditemukan (Barang Saya)</h3><div class="big">12</div>
        </div>
        <div class="card stat">
          <h3>Hilang (Barang Saya)</h3><div class="big">8</div>
        </div>
        <div class="card stat">
          <h3>Menunggu Konfirmasi</h3><div class="big">3</div>
        </div>
        <div class="card">
          <h3>Aksi Cepat</h3>
          <div class="icons">
            <a href="report_create.php" class="icon" title="Buat Laporan Baru">➕</a>
            <a href="report_list.php" class="icon" title="Lihat Semua Laporan">📄</a>
            <a href="profile.php" class="icon" title="Lihat Profil">👤</a>
          </div>
        </div>
      </section>

      <section class="cards-list">
        <div class="card">
          <h3>Laporan Terbaru Saya</h3>
          <div class="report-gallery">
            <div class="report-card-item" data-report-id="101" data-report-title="Dompet Hitam" data-report-desc="Dompet kulit warna hitam, merek XYZ, berisi KTP dan KTM. Hilang di sekitar perpustakaan." data-report-location="Perpustakaan" data-report-date="2025-11-20" data-report-status="Ditemukan" data-report-photo="assets/img/dompet.jpg">
              <img src="assets/img/dompet.jpg" alt="Dompet Hitam">
              <div class="report-card-overlay">
                <h4>Dompet Hitam</h4>
                <span class="status-tag green">Ditemukan</span>
              </div>
            </div>

            <div class="report-card-item" data-report-id="102" data-report-title="Kunci Motor XMAX" data-report-desc="Kunci motor XMAX dengan gantungan kunci berwarna biru muda. Hilang sekitar jam 1 siang di area parkiran." data-report-location="Parkiran" data-report-date="2025-11-21" data-report-status="Hilang" data-report-photo="assets/img/kunci_motor.jpg">
              <img src="assets/img/kunci_motor.jpg" alt="Kunci Motor XMAX">
              <div class="report-card-overlay">
                <h4>Kunci Motor XMAX</h4>
                <span class="status-tag red">Hilang</span>
              </div>
            </div>

            <div class="report-card-item" data-report-id="103" data-report-title="Powerbank" data-report-desc="Powerbank merek ABC kapasitas 10000mAh, warna putih. Diperkirakan jatuh di ruang kelas A saat kuliah." data-report-location="Ruang Kelas A" data-report-date="2025-11-22" data-report-status="Menunggu" data-report-photo="assets/img/powerbank.jpg">
              <img src="assets/img/powerbank.jpg" alt="Powerbank">
              <div class="report-card-overlay">
                <h4>Powerbank</h4>
                <span class="status-tag orange">Menunggu</span>
              </div>
            </div>

            <div class="report-card-item" data-report-id="104" data-report-title="Tas Laptop Merah" data-report-desc="Tas laptop warna merah, merek Lenovo, berisi charger dan mouse. Ditemukan di area kantin." data-report-location="Kantin" data-report-date="2025-11-23" data-report-status="Ditemukan" data-report-photo="assets/img/tas_laptop.jpg">
              <img src="assets/img/tas_laptop.jpg" alt="Tas Laptop Merah">
              <div class="report-card-overlay">
                <h4>Tas Laptop Merah</h4>
                <span class="status-tag green">Ditemukan</span>
              </div>
            </div>

            <div class="report-card-item" data-report-id="105" data-report-title="Buku Catatan" data-report-desc="Buku catatan kuliah mata kuliah Matematika Diskrit, sampul biru. Hilang di area parkiran motor." data-report-location="Parkiran Motor" data-report-date="2025-11-24" data-report-status="Hilang" data-report-photo="assets/img/buku.jpg">
              <img src="assets/img/buku.jpg" alt="Buku Catatan">
              <div class="report-card-overlay">
                <h4>Buku Catatan</h4>
                <span class="status-tag red">Hilang</span>
              </div>
            </div>

          </div>
        </div>

        <div class="card">
          <h3>Pengumuman Kampus</h3>
          <p>Ditemukan sebuah laptop di area kantin, segera hubungi Admin.</p>
        </div>
      </section>
    </main>
  </div>

  <div id="reportModal" class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <span class="close-button">&times;</span>
        <img id="modalReportPhoto" src="assets/img/placeholder.jpg" alt="Report Photo">
      </div>
      <div class="modal-body">
        <h3 id="modalReportTitle">Judul Laporan</h3>
        <p id="modalReportDescription">Deskripsi laporan...</p>
        <div class="modal-details">
          <div>
            <strong>ID Laporan:</strong> <span id="modalReportId">#XXX</span>
          </div>
          <div>
            <strong>Lokasi:</strong> <span id="modalReportLocation"></span>
          </div>
          <div>
            <strong>Tanggal Kejadian:</strong> <span id="modalReportDate"></span>
          </div>
          <div>
            <strong>Status:</strong> <span id="modalReportStatus" class="status-tag"></span>
          </div>
        </div>
        <div class="modal-actions">
          <a href="#" id="modalEditButton" class="btn btn-primary">Edit Laporan</a>
          <button class="btn btn-secondary">Klaim Barang Ini</button>
        </div>
      </div>
    </div>
  </div>

<script src="assets/js/app.js"></script>
<script>
  
  var modal = document.getElementById("reportModal");
  var span = document.getElementsByClassName("close-button")[0];

  document.querySelectorAll('.report-card-item').forEach(item => {
    item.addEventListener('click', function() {
      document.getElementById("modalReportPhoto").src = this.dataset.reportPhoto;
      document.getElementById("modalReportTitle").innerText = this.dataset.reportTitle;
      document.getElementById("modalReportDescription").innerText = this.dataset.reportDesc;
      document.getElementById("modalReportId").innerText = "#" + this.dataset.reportId;
      document.getElementById("modalReportLocation").innerText = this.dataset.reportLocation;
      document.getElementById("modalReportDate").innerText = this.dataset.reportDate;
      
      var statusTag = document.getElementById("modalReportStatus");
      statusTag.innerText = this.dataset.reportStatus;
      statusTag.className = 'status-tag'; 
      if (this.dataset.reportStatus === 'Ditemukan') {
        statusTag.classList.add('green');
      } else if (this.dataset.reportStatus === 'Hilang') {
        statusTag.classList.add('red');
      } else {
        statusTag.classList.add('orange'); 
      }

      document.getElementById("modalEditButton").href = "report_edit.php?id=" + this.dataset.reportId;

      modal.style.display = "block";
    });
  });

  span.onclick = function() {
    modal.style.display = "none";
  }

  window.onclick = function(event) {
    if (event.target == modal) {
      modal.style.display = "none";
    }
  }
</script>
</body>
</html>