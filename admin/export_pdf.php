<?php
require_once __DIR__ . "/auth.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../config/db_connect.php";
require_once __DIR__ . "/../vendor/autoload.php";

use Dompdf\Dompdf;

if (!isset($conn) || !($conn instanceof mysqli)) {
    die("Koneksi database tidak ditemukan. Pastikan db_connect.php benar.");
}

$sql = "SELECT r.*, COALESCE(u.full_name, u.username) AS reporter_name
        FROM reports r
        LEFT JOIN users u ON r.user_id = u.user_id
        ORDER BY r.created_at DESC";

$result = $conn->query($sql);

$rows = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

function e($str){
  return htmlspecialchars((string)$str, ENT_QUOTES, "UTF-8");
}

$html = '
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; }
  h2 { text-align:center; margin-bottom:5px; }
  .meta { text-align:center; font-size:11px; margin-bottom:15px; color:#555; }
  table { width:100%; border-collapse: collapse; }
  th, td { border:1px solid #999; padding:6px; vertical-align: top; }
  th { background:#f2f2f2; }
</style>

<h2>Laporan Lost & Found Campus</h2>
<div class="meta">Generated: '.date("d-m-Y H:i").'</div>

<table>
  <thead>
    <tr>
      <th>No</th>
      <th>Nama</th>
      <th>Barang</th>
      <th>Jenis</th>
      <th>Lokasi</th>
      <th>Tanggal</th>
      <th>Deskripsi</th>
      <th>Status</th>
    </tr>
  </thead>
  <tbody>
';

$no=1;

if(count($rows)>0){
  foreach($rows as $r){

    $nama      = $r["reporter_name"] ?? "-";
    $barang    = $r["title"] ?? "-";
    $jenis     = strtoupper($r["type"] ?? "-");
    $lokasi    = $r["location"] ?? "-";         // <- DB temanmu
    $tanggal   = $r["incident_date"] ?? "-";    // <- DB temanmu
    $deskripsi = $r["description"] ?? "-";
    $status    = $r["status"] ?? "-";

    if($tanggal !== "-" && strtotime($tanggal)){
      $tanggal = date("d-m-Y", strtotime($tanggal));
    }

    $html .= '
      <tr>
        <td>'.($no++).'</td>
        <td>'.e($nama).'</td>
        <td>'.e($barang).'</td>
        <td>'.e($jenis).'</td>
        <td>'.e($lokasi).'</td>
        <td>'.e($tanggal).'</td>
        <td>'.e($deskripsi).'</td>
        <td>'.e($status).'</td>
      </tr>
    ';
  }
}else{
  $html .= '<tr><td colspan="8" style="text-align:center;">Tidak ada data laporan.</td></tr>';
}

$html .= '</tbody></table>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4","landscape");
$dompdf->render();
$dompdf->stream("lostfound_report.pdf", ["Attachment"=>true]);
exit;