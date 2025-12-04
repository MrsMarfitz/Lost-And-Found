<?php
session_start();
require_once '../config/db_connect.php';

// Cek Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Ambil semua laporan
$query = "SELECT reports.*, users.full_name as pelapor_name 
          FROM reports 
          LEFT JOIN users ON reports.user_id = users.user_id 
          ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Laporan - Admin Panel</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <style>
        /* Menggunakan Style yang sama dengan Users.php kamu */
        body { background: #f3f6fc; font-family: sans-serif; }

        .main-head {
            display: flex; justify-content: space-between; align-items: center;
            padding: 24px 24px 0; margin-bottom: 20px;
        }
        .main-head h2 { font-size: 24px; font-weight: 600; color: #0f172a; margin: 0; }
        
        .head-actions { display: flex; align-items: center; gap: 12px; }
        .search {
            border-radius: 999px; border: 1px solid #d1d5db;
            padding: 8px 16px; font-size: 14px; min-width: 260px; outline: none;
        }
        .btn-pdf {
            background-color: #3b82f6; color: white; border: none; padding: 8px 16px;
            border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 14px;
        }

        /* Notifikasi Style */
        .alert-box {
            margin: 0 24px 20px; padding: 12px 16px; border-radius: 8px; font-size: 14px; display: flex; align-items: center; gap: 10px;
        }
        .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
        .alert-error { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }

        /* Card & Table Style */
        .cards-list { padding: 0 24px 32px; }
        .card {
            background: #fff; border-radius: 18px;
            box-shadow: 0 18px 40px rgba(15,23,42,0.08);
            padding: 24px; overflow-x: auto;
        }

        .table { width: 100%; border-collapse: collapse; font-size: 14px; }
        .table th { text-transform: uppercase; font-size: 12px; color: #6b7280; font-weight: 600; text-align: left; padding: 12px; background: #f9fafb; }
        .table td { padding: 12px; border-bottom: 1px solid #eee; color: #333; vertical-align: middle; }
        .table tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge { padding: 4px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; text-transform: uppercase; }
        .badge-lost { background: #fee2e2; color: #b91c1c; } /* Merah */
        .badge-found { background: #dbeafe; color: #1e40af; } /* Biru */
        
        /* Status Badges */
        .status-pill { display: inline-block; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: 500; }
        .st-pending { background: #f3f4f6; color: #374151; }
        .st-approved { background: #d1fae5; color: #065f46; }
        .st-rejected { background: #fee2e2; color: #991b1b; }

        /* Action Buttons */
        .btn-action { padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600; margin-right: 4px; display: inline-block; }
        .btn-approve { background: #dbeafe; color: #2563eb; }
        .btn-approve:hover { background: #bfdbfe; }
        
        .btn-reject { background: #fee2e2; color: #dc2626; }
        .btn-reject:hover { background: #fecaca; }
        
        .btn-delete { background: #f3f4f6; color: #4b5563; }
        .btn-delete:hover { background: #e5e7eb; }

    </style>
</head>
<body>

<div class="app">
    <nav class="sidebar">
        <div class="s-top">
            <img src="../public/assets/img/logo.png" class="s-logo" alt="logo">
            <h3>Admin Panel</h3>
        </div>
        <ul class="menu">
            <li><a href="index.php">Dashboard Admin</a></li>
            <li class="active"><a href="reports.php">Kelola Laporan</a></li>
            <li><a href="users.php">Kelola Pengguna</a></li>
            <li style="margin-top: 20px;"><a href="../public/dashboard.php">Kembali ke Web</a></li>
        </ul>
        <div class="s-bottom">
            <img src="../public/assets/img/user.jpg" class="avatar" alt="user">
            <div>
                <div class="small">Admin</div>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <main class="main">
        <header class="main-head">
            <div>
                <h2>Kelola Semua Laporan</h2>
                <p style="margin-top:4px;font-size:13px;color:#6b7280;">Daftar laporan hilang & ditemukan.</p>
            </div>
            <div class="head-actions">
                <input class="search" id="searchInput" placeholder="Filter/Cari laporan...">
                <a href="export_pdf.php" target="_blank" class="btn btn-primary">Generate PDF Report</a>
            </div>
        </header>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert-box alert-success" id="notifBox">
                ✅ <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
            <script>
                setTimeout(() => {
                    document.getElementById('notifBox').style.display = 'none';
                    window.history.replaceState(null, null, window.location.pathname);
                }, 3000);
            </script>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert-box alert-error">
                ❌ <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <div class="cards-list">
            <div class="card">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Jenis</th>
                            <th>Pelapor</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Status</th>
                            <th>Aksi Admin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $row['report_id']; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($row['title']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo ($row['type'] == 'Found') ? 'badge-found' : 'badge-lost'; ?>">
                                            <?php echo strtoupper($row['type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['pelapor_name'] ?? 'Unknown'); ?></td>
                                    <td><?php echo htmlspecialchars($row['incident_date']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td>
                                        <?php 
                                            $stClass = 'st-pending';
                                            if($row['status'] == 'Approved' || $row['status'] == 'Selesai' || $row['status'] == 'Ditemukan') $stClass = 'st-approved';
                                            if($row['status'] == 'Rejected' || $row['status'] == 'Hilang') $stClass = 'st-rejected';
                                        ?>
                                        <span class="status-pill <?php echo $stClass; ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="update_status.php?id=<?php echo $row['report_id']; ?>&status=Approved" 
                                           class="btn-action btn-approve" onclick="return confirm('Approve laporan ini?')">
                                           Approve
                                        </a>
                                        <a href="update_status.php?id=<?php echo $row['report_id']; ?>&status=Rejected" 
                                           class="btn-action btn-reject" onclick="return confirm('Reject laporan ini?')">
                                           Reject
                                        </a>
                                        <a href="delete_report.php?id=<?php echo $row['report_id']; ?>" 
                                           class="btn-action btn-delete" onclick="return confirm('Hapus permanen?')">
                                           Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" style="text-align:center; padding: 20px; color:#999;">
                                    Belum ada laporan yang masuk.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<script>
    // Simple Search Filter
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.table tbody tr');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>

</body>
</html>