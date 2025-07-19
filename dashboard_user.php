<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Ambil kategori dari database
$kategori_result = mysqli_query($conn, "SELECT * FROM kategori");

// Proses input pengaduan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_pengaduan'])) {
    $user_id     = $_SESSION['user_id'];
    $kategori_id = $_POST['kategori_id'];
    $judul       = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi         = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal     = date('Y-m-d H:i:s');

    $insert = mysqli_query($conn, "INSERT INTO pengaduan (user_id, kategori_id, judul, isi, tanggal) VALUES ('$user_id', '$kategori_id', '$judul', '$isi', '$tanggal')");
    if ($insert) {
        $pengaduan_id = mysqli_insert_id($conn);

        if (!empty($_FILES['lampiran']['name'][0])) {
            $uploadDir = "uploads/";

            foreach ($_FILES['lampiran']['tmp_name'] as $key => $tmp_name) {
                $fileName = basename($_FILES['lampiran']['name'][$key]);
                $targetFile = $uploadDir . time() . "_" . $fileName;

                if (move_uploaded_file($tmp_name, $targetFile)) {
                    mysqli_query($conn, "INSERT INTO lampiran (pengaduan_id, file_path) VALUES ('$pengaduan_id', '$targetFile')");
                }
            }
        }

        $success = "Pengaduan berhasil dikirim!";
    } else {
        $error = "Gagal mengirim pengaduan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            margin: 0;
            padding: 20px;
            color: #2c3e50;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        .container {
            background: #fff;
            width: 100%;
            max-width: 800px;
            padding: 25px 20px 30px 20px;
            border-radius: 12px;
            box-shadow: 0 12px 30px rgba(0,0,0,0.15);
        }

        h1 {
            text-align: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        form input[type="text"],
        form select,
        form textarea,
        form input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 0.95rem;
            background-color: #f9f9f9;
        }

        form input[type="text"]:focus,
        form select:focus,
        form textarea:focus,
        form input[type="file"]:focus {
            border-color: #74ebd5;
            outline: none;
            background-color: #fff;
        }

        textarea {
            resize: vertical;
        }

        button {
            background: linear-gradient(135deg, #74ebd5, #ACB6E5);
            border: none;
            padding: 12px;
            font-size: 1rem;
            color: #fff;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }

        button:hover {
            opacity: 0.9;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
        }

        .success {
            background-color: #d4edda;
            color: #2e7d32;
            border: 1px solid #2e7d32;
        }

        .error {
            background-color: #f8d7da;
            color: #c0392b;
            border: 1px solid #c0392b;
        }

        .logout-form {
            text-align: center;
        }

        .logout-form button {
            background: #e74c3c;
            color: #fff;
            font-weight: bold;
            border: none;
            padding: 12px;
            font-size: 1rem;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }

        .logout-form button:hover {
            background: #c0392b;
        }

        @media (max-width: 480px) {
            h1 {
                font-size: 1.5rem;
            }

            button,
            .logout-form button {
                font-size: 0.95rem;
                padding: 10px;
            }

            form input,
            form select,
            form textarea {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Selamat datang, <?= htmlspecialchars($_SESSION['nama']) ?></h1>

        <?php if (!empty($success)) echo "<div class='message success'>" . htmlspecialchars($success) . "</div>"; ?>
        <?php if (!empty($error)) echo "<div class='message error'>" . htmlspecialchars($error) . "</div>"; ?>

        <form method="POST" enctype="multipart/form-data" novalidate>
            <label for="kategori_id">Kategori Pengaduan:</label>
            <select name="kategori_id" id="kategori_id" required>
                <option value="" disabled selected>-- Pilih Kategori --</option>
                <?php while ($row = mysqli_fetch_assoc($kategori_result)) { ?>
                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nama_kategori']) ?></option>
                <?php } ?>
            </select>

            <label for="judul">Judul Pengaduan:</label>
            <input type="text" id="judul" name="judul" placeholder="Masukkan judul" required>

            <label for="isi">Isi Pengaduan:</label>
            <textarea id="isi" name="isi" placeholder="Jelaskan masalah Anda..." rows="4" required></textarea>

            <label for="lampiran">Lampiran (opsional):</label>
            <input type="file" id="lampiran" name="lampiran[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.txt">

            <button type="submit" name="submit_pengaduan">Kirim Pengaduan</button>
        </form>

        <form method="POST" action="logout.php" class="logout-form">
            <button type="submit">Logout</button>
        </form>
    </div>
</body>
</html>
