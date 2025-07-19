<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Proses simpan tanggapan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_tanggapan'])) {
    $pengaduan_id = $_POST['pengaduan_id'];
    $admin_id     = $_SESSION['admin_id'];
    $isi          = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal      = date('Y-m-d H:i:s');

    mysqli_query($conn, "INSERT INTO tanggapan (pengaduan_id, admin_id, isi, tanggal) VALUES ('$pengaduan_id', '$admin_id', '$isi', '$tanggal')");
    mysqli_query($conn, "UPDATE pengaduan SET status='diproses' WHERE id='$pengaduan_id'");
}

$query = "
    SELECT p.*, u.nama AS nama_user, k.nama_kategori
    FROM pengaduan p
    JOIN users u ON p.user_id = u.id
    JOIN kategori k ON p.kategori_id = k.id
    ORDER BY p.tanggal DESC
";
$pengaduan_result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Admin</title>
    <style>
        /* Reset dan base */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            margin: 0;
            padding: 20px;
            color: #333;
        }
        h1 {
            text-align: center;
            color: #34495e;
            margin-bottom: 30px;
            animation: fadeInDown 1s ease forwards;
        }
        .container {
            max-width: 960px;
            margin: auto;
        }
        .pengaduan {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            padding: 20px 25px;
            margin-bottom: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: fadeInUp 0.8s ease forwards;
        }
        .pengaduan:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 30px rgba(0,0,0,0.15);
        }
        strong {
            font-size: 1.3rem;
            color: #2c3e50;
        }
        .meta-info {
            font-size: 0.85rem;
            color: #7f8c8d;
            margin-top: 3px;
            margin-bottom: 12px;
        }
        p {
            font-size: 1rem;
            line-height: 1.5;
            white-space: pre-line;
        }
        .lampiran {
            margin-top: 15px;
        }
        .lampiran img {
            max-width: 150px;
            margin-right: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            cursor: pointer;
        }
        .lampiran img:hover {
            transform: scale(1.05);
        }
        a.lampiran-file {
            display: inline-block;
            margin-top: 8px;
            padding: 5px 10px;
            background: #2980b9;
            color: white;
            border-radius: 6px;
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 0.9rem;
        }
        a.lampiran-file:hover {
            background-color: #3498db;
        }
        h4 {
            margin-top: 20px;
            color: #34495e;
            border-bottom: 2px solid #2980b9;
            padding-bottom: 5px;
            font-weight: 600;
        }
        .tanggapan {
            background: #ecf0f1;
            border-left: 4px solid #2980b9;
            padding: 12px 15px;
            margin-top: 12px;
            border-radius: 6px;
            font-size: 0.95rem;
            color: #2c3e50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .tanggapan b {
            color: #2980b9;
        }
        form {
            margin-top: 15px;
        }
        textarea {
            width: 100%;
            border: 2px solid #bdc3c7;
            border-radius: 8px;
            padding: 10px;
            font-size: 1rem;
            resize: vertical;
            transition: border-color 0.3s ease;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        textarea:focus {
            outline: none;
            border-color: #2980b9;
            background-color: #f0f8ff;
        }
        button[type="submit"] {
            background-color: #2980b9;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-top: 8px;
        }
        button[type="submit"]:hover {
            background-color: #3498db;
        }
        form.logout-form {
            text-align: center;
            margin-top: 20px;
        }
        form.logout-form button {
            background-color: #e74c3c;
            padding: 10px 25px;
        }
        form.logout-form button:hover {
            background-color: #c0392b;
        }

        /* Animasi fadeIn */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?></h1>

        <?php while ($p = mysqli_fetch_assoc($pengaduan_result)) { ?>
            <div class="pengaduan">
                <strong><?= htmlspecialchars($p['judul']) ?></strong> (<?= htmlspecialchars($p['nama_kategori']) ?>)<br>
                <div class="meta-info">
                    Oleh: <?= htmlspecialchars($p['nama_user']) ?> | Tanggal: <?= $p['tanggal'] ?> | Status: <?= htmlspecialchars($p['status']) ?>
                </div>
                <p><?= nl2br(htmlspecialchars($p['isi'])) ?></p>

                <div class="lampiran">
                    <?php
                    $lampiran_q = mysqli_query($conn, "SELECT * FROM lampiran WHERE pengaduan_id = " . $p['id']);
                    while ($lamp = mysqli_fetch_assoc($lampiran_q)) {
                        $ext = strtolower(pathinfo($lamp['file_path'], PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            echo "<img src='" . htmlspecialchars($lamp['file_path']) . "' alt='Lampiran' loading='lazy'>";
                        } else {
                            echo "<a href='" . htmlspecialchars($lamp['file_path']) . "' class='lampiran-file' target='_blank'>Lampiran File</a><br>";
                        }
                    }
                    ?>
                </div>

                <div>
                    <h4>Tanggapan:</h4>
                    <?php
                    $tanggapan_q = mysqli_query($conn, "SELECT t.*, a.nama AS nama_admin FROM tanggapan t JOIN admins a ON t.admin_id = a.id WHERE t.pengaduan_id = " . $p['id'] . " ORDER BY t.tanggal DESC");
                    if (mysqli_num_rows($tanggapan_q) > 0) {
                        while ($t = mysqli_fetch_assoc($tanggapan_q)) {
                            echo "<div class='tanggapan'><b>" . htmlspecialchars($t['nama_admin']) . "</b> (" . $t['tanggal'] . "):<br>" . nl2br(htmlspecialchars($t['isi'])) . "</div>";
                        }
                    } else {
                        echo "<i>Belum ada tanggapan</i>";
                    }
                    ?>
                </div>

                <form method="POST" style="margin-top:10px;">
                    <input type="hidden" name="pengaduan_id" value="<?= $p['id'] ?>">
                    <textarea name="isi" rows="3" placeholder="Tulis tanggapan..." required></textarea><br>
                    <button type="submit" name="submit_tanggapan">Kirim Tanggapan</button>
                </form>
            </div>
        <?php } ?>

        <form method="POST" action="logout.php" class="logout-form">
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
