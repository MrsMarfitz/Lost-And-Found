<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Laporan - Lost & Found Campus</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
      /* Styles for report grid */
      .report-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); /* Responsive grid */
        gap: 20px;
        padding: 20px 0;
      }

      .report-card-item {
        width: 100%; /* Take full width of grid column */
        height: 160px; /* Fixed height for consistency */
        border-radius: 10px;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s ease-in-out;
        background: #fff; /* Fallback background */
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

      /* Modal styles - same as dashboard.php */
      .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1000; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0,0,0,0.7); /* Black w/ opacity */
        animation: fadeIn 0.3s;
      }

      .modal-content {
        background-color: #fefefe;
        margin: 5% auto; /* 5% from the top and centered */
        padding: 0; /* Remove default padding */
        border-radius: 10px;
        width: 80%; /* Could be more or less, depending on screen size */
        max-width: 900px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        position: relative;
        overflow: hidden; /* For rounded corners */
        animation: slideInUp 0.4s;
      }

      .modal-header {
          position: relative;
          height: 350px; /* Fixed height for image area */
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

      /* Animations */
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="report_create.php">Buat Laporan</a></li>
                <li class="active">Daftar Laporan</li>
                <li><a href="profile.php">Profil Saya</a></li>
                <li><a href="../admin/index.php">Admin Panel</a></li>
            </ul>
            <div class="s-bottom">
                <img src="assets/img/user.jpg" class="avatar" alt="user">
                <div>
                    <div class="small">Pengguna Aktif</div>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </nav>
        
        <main class="main">
            <header class="main-head">
                <h2>Daftar Semua Laporan</h2>
                <div class="head-actions">
                    <input class="search" placeholder="Filter laporan...">
                    <a href="report_create.php" class="btn-primary small">Laporan Baru</a>
                </div>
            </header>
            
            <div class="card">
                <h3>Semua Laporan Tersedia</h3>
                <div class="report-grid">
                    <div class="report-card-item" data-report-id="104" data-report-title="Kunci Motor Honda" data-report-desc="Kunci motor Honda Vario dengan gantungan kunci Doraemon. Hilang di parkiran barat." data-report-location="Parkiran Barat" data-report-date="2025-11-30" data-report-status="Hilang" data-report-photo="assets/img/kunci_honda.jpg">
                        <img src="assets/img/kunci_honda.jpg" alt="Kunci Motor Honda">
                        <div class="report-card-overlay">
                            <h4>Kunci Motor Honda</h4>
                            <span class="status-tag red">Hilang</span>
                        </div>
                    </div>
                    <div class="report-card-item" data-report-id="105" data-report-title="Jam Tangan Casio" data-report-desc="Jam tangan Casio digital warna hitam, tali karet. Ditemukan di lantai 2 perpustakaan." data-report-location="Perpustakaan Lt. 2" data-report-date="2025-11-29" data-report-status="Ditemukan" data-report-photo="assets/img/jam_casio.jpg">
                        <img src="assets/img/jam_casio.jpg" alt="Jam Tangan Casio">
                        <div class="report-card-overlay">
                            <h4>Jam Tangan Casio</h4>
                            <span class="status-tag green">Ditemukan</span>
                        </div>
                    </div>
                    <div class="report-card-item" data-report-id="106" data-report-title="Tas Laptop Merah" data-report-desc="Tas laptop warna merah, merek Asus, dengan beberapa stiker di bagian depan. Hilang di Lab Komputer." data-report-location="Lab Komputer" data-report-date="2025-11-28" data-report-status="Menunggu" data-report-photo="assets/img/tas_laptop_merah.jpg">
                        <img src="assets/img/tas_laptop_merah.jpg" alt="Tas Laptop Merah">
                        <div class="report-card-overlay">
                            <h4>Tas Laptop Merah</h4>
                            <span class="status-tag orange">Menunggu</span>
                        </div>
                    </div>
                    <div class="report-card-item" data-report-id="107" data-report-title="Kartu Mahasiswa" data-report-desc="Kartu mahasiswa atas nama 'Andi Nugroho', NIM: 12345678. Hilang di area kantin." data-report-location="Area Kantin" data-report-date="2025-11-27" data-report-status="Hilang" data-report-photo="assets/img/kartu_mhs.jpg">
                        <img src="assets/img/kartu_mhs.jpg" alt="Kartu Mahasiswa">
                        <div class="report-card-overlay">
                            <h4>Kartu Mahasiswa</h4>
                            <span class="status-tag red">Hilang</span>
                        </div>
                    </div>
                    <div class="report-card-item" data-report-id="108" data-report-title="USB Drive 32GB" data-report-desc="Flashdisk 32GB merek SanDisk, warna hitam. Ditemukan di ruang B-102." data-report-location="Ruang B-102" data-report-date="2025-11-26" data-report-status="Ditemukan" data-report-photo="assets/img/flashdisk.jpg">
                        <img src="assets/img/flashdisk.jpg" alt="USB Drive 32GB">
                        <div class="report-card-overlay">
                            <h4>USB Drive 32GB</h4>
                            <span class="status-tag green">Ditemukan</span>
                        </div>
                    </div>
                    <div class="report-card-item" data-report-id="109" data-report-title="Kacamata Baca" data-report-desc="Kacamata baca bingkai hitam, minus 1.5. Hilang di area parkiran dosen." data-report-location="Parkiran Dosen" data-report-date="2025-11-25" data-report-status="Hilang" data-report-photo="assets/img/kacamata.jpg">
                        <img src="assets/img/kacamata.jpg" alt="Kacamata Baca">
                        <div class="report-card-overlay">
                            <h4>Kacamata Baca</h4>
                            <span class="status-tag red">Hilang</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <div id="reportModal" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <span class="close-button">&times;</span>
          <img id="modal