<?php
require_once "auth.php";
require_once "../config/db_connect.php";

// Ambil semua user dari database
// Pastikan kolom 'last_login' sudah ada di database, jika belum hapus ', last_login'
$query = "SELECT user_id, full_name, username, email, role, last_login FROM users ORDER BY user_id ASC";
$result = $conn->query($query);
$users = $result->fetch_all(MYSQLI_ASSOC);

// Ambil user pertama untuk detail awal
$selected_user = $users[0] ?? null;
$selected_user_id = $selected_user['user_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin Panel</title>
    <link rel="stylesheet" href="../public/assets/css/style.css">
    <style>
        body { background: #f3f6fc; }

        .main-head{
            display:flex;justify-content:space-between;align-items:center;
            padding:24px 24px 0;
        }
        .main-head h2{
            font-size:24px;font-weight:600;color:#0f172a;margin:0;
        }
        .head-actions{display:flex;align-items:center;gap:12px;}
        .search{
            border-radius:999px;border:1px solid #d1d5db;
            padding:8px 16px;font-size:14px;min-width:260px;outline:none;
        }
        .search:focus{
            border-color:#6366f1;
            box-shadow:0 0 0 2px rgba(99,102,241,0.15);
        }
        .btn-primary.small{
            border-radius:999px;
            background:linear-gradient(135deg,#6366f1,#8b5cf6);
            border:none;color:#fff;padding:9px 18px;font-size:14px;
            cursor:pointer;box-shadow:0 8px 20px rgba(99,102,241,0.25);
        }
        .btn-primary.small:hover{filter:brightness(1.05);}

        .alert{
            padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px;
        }
        .alert.success{background:#dcfce7;color:#15803d;border:1px solid #86efac;}
        .alert.error{background:#fee2e2;color:#b91c1c;border:1px solid #fca5a5;}

        .cards-list{padding:16px 24px 32px;}
        .card{
            background:#fff;border-radius:18px;
            box-shadow:0 18px 40px rgba(15,23,42,0.08);
            padding:24px;
        }

        /* Detail Panel - Top */
        .detail-panel{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
            gap:24px;
            padding:20px 0 28px;
            border-bottom:1px solid #e5e7eb;
            margin-bottom:28px;
        }

        .detail-item{
            display:flex;flex-direction:column;
            gap:4px;
        }

        .detail-label{
            font-size:12px;
            font-weight:600;
            text-transform:uppercase;
            letter-spacing:.05em;
            color:#6b7280;
        }

        .detail-value{
            font-size:15px;
            font-weight:500;
            color:#0f172a;
        }

        .detail-value.muted{
            color:#999;
            font-weight:normal;
        }

        /* Table */
        .table{width:100%;border-collapse:collapse;font-size:14px;}
        .table thead{background:#f9fafb;}
        .table th,.table td{padding:12px;text-align:left;}
        .table th{
            font-size:12px;text-transform:uppercase;
            letter-spacing:.03em;color:#6b7280;font-weight:600;
        }
        .table tbody tr:nth-child(even){background:#f9fafb;}
        .table tbody tr:hover{background:#eef2ff;}

        .tag{
            border-radius:999px;padding:4px 10px;font-size:11px;
            font-weight:500;display:inline-block;
        }
        .tag.red{background:#fee2e2;color:#b91c1c;}
        .tag.orange{background:#fef3c7;color:#b45309;}

        .action-badge{
            border-radius:6px;padding:6px 14px;
            font-size:12px;border:none;cursor:pointer;
            background:#6366f1;color:#fff;
        }
        .action-badge:hover{background:#4f46e5;}

        .btn-delete{
            border-radius:8px;
            padding:10px 20px;
            font-size:13px;
            border:none;cursor:pointer;
            background:#ef4444;color:#fff;
            flex:1;max-width:200px;
        }
        .btn-delete:hover{background:#dc2626;}
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
            <li><a href="reports.php">Kelola Laporan</a></li>
            <li class="active">Kelola Pengguna</li>
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
                <h2>Kelola Semua Pengguna</h2>
                <p style="margin-top:4px;font-size:13px;color:#6b7280;">
                    Kelola akun admin dan user dalam sistem Lost &amp; Found.
                </p>
            </div>
            <div class="head-actions">
                <input class="search" id="searchInput" placeholder="Cari nama / email...">
            </div>
        </header>
        
        <div style="margin: 20px 24px 0;">
            
            <?php if (isset($_GET['success'])): ?>
                <div style="background:#dcfce7; color:#15803d; border:1px solid #86efac; padding:12px 16px; border-radius:8px; display:flex; align-items:center; gap:10px;">
                    <span style="font-size:18px;">✅</span>
                    <span>Pengguna berhasil dihapus secara permanen.</span>
                </div>
                <script>
                    setTimeout(function(){
                        window.location.href = 'users.php'; // Refresh bersih parameter URL
                    }, 3000);
                </script>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div style="background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; padding:12px 16px; border-radius:8px; display:flex; align-items:center; gap:10px;">
                    <span style="font-size:18px;">❌</span>
                    <span><?php echo htmlspecialchars($_GET['error']); ?></span>
                </div>
            <?php endif; ?>
            
        </div>
        <div class="cards-list">

        <?php if (isset($_GET['success'])): ?>
            <div style="margin:0 24px;">
                <div class="alert success">✓ User berhasil dihapus</div>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div style="margin:0 24px;">
                <div class="alert error">✗ <?php echo htmlspecialchars($_GET['error']); ?></div>
            </div>
        <?php endif; ?>

        <div class="cards-list">
            <div class="card">
                <?php if ($selected_user): ?>
                <div class="detail-panel" id="detailPanel">
                    <div class="detail-item">
                        <span class="detail-label">Nama Lengkap</span>
                        <span class="detail-value" id="detailNama">
                            <?php echo htmlspecialchars($selected_user['full_name']); ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Username</span>
                        <span class="detail-value" id="detailUsername">
                            <?php echo htmlspecialchars($selected_user['username']); ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value" id="detailEmail">
                            <?php echo htmlspecialchars($selected_user['email']); ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Tipe Akun</span>
                        <span class="detail-value" id="detailRole">
                            <span class="tag <?php echo $selected_user['role'] === 'admin' ? 'red' : 'orange'; ?>">
                                <?php echo ucfirst($selected_user['role']); ?>
                            </span>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Login Terakhir</span>
                        <span class="detail-value" id="detailLogin">
                            <?php 
                            if ($selected_user['last_login']) {
                                $date = new DateTime($selected_user['last_login']);
                                echo $date->format('d/m/Y H:i');
                            } else {
                                echo '<span class="muted">Belum login</span>';
                            }
                            ?>
                        </span>
                    </div>

                    <div style="display:flex;gap:8px;align-self:flex-end;">
                        <form method="POST" action="user_delete.php" style="display:inline;">
                            <input type="hidden" name="user_id" id="deleteUserId" value="<?php echo $selected_user['user_id']; ?>">
                            
                            <button type="submit" class="btn-delete" id="btnDelete"
                                onclick="return confirm('Yakin ingin menghapus pengguna <?php echo htmlspecialchars($selected_user['full_name']); ?>?')">
                                Hapus Pengguna
                            </button>
                        </form>
                    </div>
                </div>
                <?php endif; ?>

                <table class="table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Email</th>
                        <th>Tipe</th>
                        <th>Terakhir Login</th>
                        <th style="text-align:center;">Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <td><?php echo $index + 1; ?></td>
                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <span class="tag <?php echo $user['role'] === 'admin' ? 'red' : 'orange'; ?>">
                                <?php echo ucfirst($user['role']); ?>
                            </span>
                        </td>
                        <td>
                            <?php 
                            if ($user['last_login']) {
                                $date = new DateTime($user['last_login']);
                                echo $date->format('d/m/Y H:i');
                            } else {
                                echo '<span style="color:#999;">Belum login</span>';
                            }
                            ?>
                        </td>
                        <td style="text-align:center;">
                            <a href="#top" onclick="selectUser(<?php echo htmlspecialchars(json_encode($user)); ?>); return false;" class="action-badge">
                                Detail
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </main>
</div>

<script>
    function selectUser(user) {
        // 1. Update Teks Tampilan
        document.getElementById('detailNama').textContent = user.full_name;
        document.getElementById('detailUsername').textContent = user.username;
        document.getElementById('detailEmail').textContent = user.email;

        // 2. Update Tampilan Role
        const roleTag = user.role === 'admin'
            ? '<span class="tag red">Admin</span>'
            : '<span class="tag orange">User</span>';
        document.getElementById('detailRole').innerHTML = roleTag;

        // 3. Update Tampilan Login
        const loginText = user.last_login
            ? new Date(user.last_login).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
              })
            : '<span class="muted">Belum login</span>';
        document.getElementById('detailLogin').innerHTML = loginText;

        // 4. Update ID di input hidden (PENTING untuk user_delete.php)
        document.getElementById('deleteUserId').value = user.user_id;

        // 5. UPDATE PESAN KONFIRMASI (PENTING AGAR NAMA TIDAK SALAH)
        const btn = document.getElementById('btnDelete');
        if (btn) {
            btn.setAttribute('onclick', `return confirm('Yakin ingin menghapus pengguna ${user.full_name}?')`);
        }

        // Scroll ke atas agar terlihat perubahannya
        document.querySelector('.card').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Fitur Search Table
    document.getElementById('searchInput').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('.table tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
</script>

</body>
</html>