<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . "/../config/db_connect.php";

$user_id = $_SESSION['user_id'];

$sqlFound = "SELECT COUNT(*) AS total
             FROM reports
             WHERE user_id = ?
               AND type = 'found'";
$stmt = $conn->prepare($sqlFound);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$found = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

$sqlLost = "SELECT COUNT(*) AS total
            FROM reports
            WHERE user_id = ?
              AND type = 'lost'";
$stmt = $conn->prepare($sqlLost);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$lost = $stmt->get_result()->fetch_assoc()['total'] ?? 0;

// Menunggu Konfirmasi -> sementara 0 (belum ada flow klaim admin)
$waiting = 0;



$sqlLatest = "SELECT report_id, title, description, location_text, date_event, status, photo, type
              FROM reports
              WHERE user_id = ?
              ORDER BY created_at DESC
              LIMIT 5";
$stmt = $conn->prepare($sqlLatest);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result    = $stmt->get_result();
$myReports = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>Dashboard - Lost & Found Campus</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .report-gallery {
      display: flex;
      overflow-x: auto;
      gap: 15px;
      padding-bottom: 20px;
      scrollbar-width: thin;
      scrollbar-color: #888 #f1f1f1;
      -webkit-overflow-scrolling: touch;
    }
    .report-gallery::-webkit-scrollbar { height: 8px; }
    .report-gallery::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }
    .report-gallery::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }
    .report-gallery::-webkit-scrollbar-thumb:hover { background: #555; }

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
    .report-card-item:hover { transform: translateY(-5px); }

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
    .status-found  { background:#2563eb; }
    .status-lost   { background:#f97316; }
    .status-other  { background:#10b981; }

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
    .modal-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #eee;
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
    .modal-actions .btn-secondary {
        background-color: #e0e7ff;
        color: #4f46e5;
    }

    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideInUp {
      from { transform: translateY(50px); opacity: 0; }
      to   { transform: translateY(0); opacity: 1; }
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
          <div class="small">
            <?php echo isset($_SESSION['full_name']) ? htmlspecialchars($_SESSION['full_name']) : 'Pengguna'; ?>
          </div>
          <a href="logout.php">Logout</a>
        </div>
      </div>
    </nav>

    <main class="main">
      <header class="main-head">
        <h2>Dashboard Pengguna</h2>
      </header>

      <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; border: 1px solid #34d399; margin-bottom: 20px; text-align: center; font-weight: bold;">
          ✅ <?php echo htmlspecialchars($_GET['msg'] ?? 'Berhasil.'); ?>
        </div>
        <script>
          setTimeout(function() {
            let alertBox = document.querySelector('div[style*="background: #d1fae5"]');
            if (alertBox) {
              alertBox.style.transition = "opacity 0.5s";
              alertBox.style.opacity = "0";
              setTimeout(() => alertBox.remove(), 500);
            }
            window.history.replaceState(null, null, window.location.pathname);
          }, 3000);
        </script>
      <?php endif; ?>

      <section class="grid">
        <div class="card stat">
          <h3>Ditemukan (Barang Saya)</h3>
          <div class="big"><?php echo $found; ?></div>
        </div>
        <div class="card stat">
          <h3>Hilang (Barang Saya)</h3>
          <div class="big"><?php echo $lost; ?></div>
        </div>
        <div class="card stat">
          <h3>Menunggu Konfirmasi</h3>
          <div class="big"><?php echo $waiting; ?></div>
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
            <?php foreach ($myReports as $r): ?>
              <?php
              $photoPath = !empty($r['photo'])
                  ? 'uploads/' . $r['photo']
                  : 'assets/img/placeholder.jpg';

              if ($r['type'] === 'found') {
                  $statusLabel = 'Barang Ditemukan';
                  $statusClass = 'status-found';
              } elseif ($r['type'] === 'lost') {
                  $statusLabel = 'Barang Hilang';
                  $statusClass = 'status-lost';
              } else {
                  $statusLabel = 'Status Lain';
                  $statusClass = 'status-other';
              }
              ?>
              <div class="report-card-item"
                   data-report-id="<?php echo $r['report_id']; ?>"
                   data-report-title="<?php echo htmlspecialchars($r['title']); ?>"
                   data-report-desc="<?php echo htmlspecialchars($r['description']); ?>"
                   data-report-location="<?php echo htmlspecialchars($r['location_text']); ?>"
                   data-report-date="<?php echo htmlspecialchars($r['date_event']); ?>"
                   data-report-status="<?php echo $statusLabel; ?>"
                   data-report-photo="<?php echo $photoPath; ?>">
                <img src="<?php echo $photoPath; ?>" alt="<?php echo htmlspecialchars($r['title']); ?>">
                <div class="report-card-overlay">
                  <h4><?php echo htmlspecialchars($r['title']); ?></h4>
                  <span class="status-tag <?php echo $statusClass; ?>">
                    <?php echo $statusLabel; ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>

            <?php if (empty($myReports)): ?>
              <p style="padding:10px 0;color:#6b7280;font-size:14px;">
                Belum ada laporan. Buat laporan baru dulu.
              </p>
            <?php endif; ?>
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
          <button class="btn btn-secondary" id="modalClaimButton">Klaim Barang Ini</button>
        </div>
      </div>
    </div>
  </div>

<script>
  var modal    = document.getElementById("reportModal");
  var span     = document.getElementsByClassName("close-button")[0];
  var claimBtn = document.getElementById("modalClaimButton");

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

      if (this.dataset.reportStatus === 'Barang Ditemukan') {
        statusTag.classList.add('status-found');
      } else if (this.dataset.reportStatus === 'Barang Hilang') {
        statusTag.classList.add('status-lost');
      } else {
        statusTag.classList.add('status-other');
      }

      document.getElementById("modalEditButton").href =
        "report_edit.php?id=" + this.dataset.reportId;

      claimBtn.dataset.reportId = this.dataset.reportId;

      modal.style.display = "block";
    });
  });

  span.onclick = function() { modal.style.display = "none"; }
  window.onclick = function(e) {
    if (e.target == modal) modal.style.display = "none";
  }

  claimBtn.addEventListener('click', function () {
    var reportId = this.dataset.reportId;
    if (!reportId) return;
    window.location.href = "claim_report.php?id=" + reportId;
  });
</script>
</body>
</html>