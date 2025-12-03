<?php
session_start();
require_once __DIR__ . "/../config/db_connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$claimer_id = $_SESSION['user_id'];
$report_id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$report = null;
if ($report_id > 0) {
    $sql = "SELECT report_id, title, description 
            FROM reports 
            WHERE report_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $report_id);
    $stmt->execute();
    $report = $stmt->get_result()->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $report_id     = (int)($_POST['report_id'] ?? 0);
    $evidence_text = trim($_POST['evidence_text'] ?? '');

    if ($report_id > 0 && $evidence_text !== '') {
        $sql = "INSERT INTO claims (report_id, claimer_id, evidence_text, status)
                VALUES (?, ?, ?, 'Pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $report_id, $claimer_id, $evidence_text);
        $stmt->execute();

        header("Location: dashboard.php?status=success&msg=Klaim%20berhasil%20dikirim");
        exit();
    } else {
        $error = "Harap isi bukti kepemilikan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Klaim Barang - Lost & Found</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app">
    <main class="main" style="max-width:600px;margin:40px auto;">
        <h2>Klaim Barang Ini</h2>

        <?php if ($report): ?>
            <p><strong>Laporan:</strong>
                #<?php echo $report['report_id']; ?> -
                <?php echo htmlspecialchars($report['title']); ?>
            </p>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div style="background:#fee2e2;color:#b91c1c;padding:10px;border-radius:8px;margin-bottom:10px;">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="hidden" name="report_id" value="<?php echo $report_id; ?>">

            <label for="evidence_text" style="font-weight:600;font-size:14px;">Bukti Kepemilikan</label>
            <p style="font-size:13px;color:#6b7280;margin-top:4px;">
                Tulis bukti khusus yang hanya pemilik tahu
                (contoh: isi dompet, warna gantungan kunci, nomor IMEI, dll).
            </p>
            <textarea name="evidence_text" id="evidence_text" rows="5"
                      style="width:100%;padding:10px;border-radius:8px;border:1px solid #d1d5db;"
                      required></textarea>

            <button type="submit"
                    style="margin-top:15px;padding:10px 18px;border:none;border-radius:8px;
                           background:#3b82f6;color:#fff;font-weight:600;cursor:pointer;">
                Kirim Klaim
            </button>
            <a href="dashboard.php"
               style="margin-top:15px;margin-left:8px;padding:10px 18px;border-radius:8px;
                      background:#e5e7eb;color:#111827;font-weight:500;text-decoration:none;">
                Batal
            </a>
        </form>
    </main>
</div>
</body>
</html>
