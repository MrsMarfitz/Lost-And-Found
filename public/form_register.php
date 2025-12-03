<!DOCTYPE html>
<html>
<head>
    <title>Test Register</title>
</head>
<body>
    <h2>Form Pendaftaran</h2>
    <form action="../actions/register_process.php" method="POST">
        
        <label>Nama Lengkap:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>No HP:</label><br>
        <input type="text" name="phone" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Konfirmasi Password:</label><br>
        <input type="password" name="password_confirm" required><br><br>

        <button type="submit">DAFTAR SEKARANG</button>
    </form>
</body>
</html>