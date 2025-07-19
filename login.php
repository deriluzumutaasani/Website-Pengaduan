<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Cek admin
    $admin_q = mysqli_query($conn, "SELECT * FROM admins WHERE email = '$email' AND password = '$password'");
    if ($admin = mysqli_fetch_assoc($admin_q)) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['nama']     = $admin['nama'];
        header("Location: dashboard_admin.php");
        exit;
    }

    // Cek user
    $user_q = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email' AND password = '$password'");
    if ($user = mysqli_fetch_assoc($user_q)) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nama']    = $user['nama'];
        header("Location: dashboard_user.php");
        exit;
    }

    $error = "Email atau password salah.";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Login Pengaduan Desa</title>
    <style>
        /* Reset */
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #74ebd5 0%, #ACB6E5 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            animation: fadeInBody 1s ease forwards;
        }

        @keyframes fadeInBody {
            from {opacity: 0;}
            to {opacity: 1;}
        }

        .login-container {
            background: white;
            padding: 40px 35px;
            border-radius: 15px;
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
            width: 350px;
            text-align: center;
            animation: slideInDown 0.8s ease forwards;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        h2 {
            margin-bottom: 25px;
            color: #2c3e50;
            font-weight: 700;
            font-size: 1.8rem;
            letter-spacing: 1px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            font-family: inherit;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            border-color: #2980b9;
            box-shadow: 0 0 8px rgba(41, 128, 185, 0.5);
            outline: none;
            background-color: #f0f8ff;
        }

        button {
            width: 100%;
            padding: 12px 0;
            background-color: #2980b9;
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            letter-spacing: 0.8px;
        }
        button:hover {
            background-color: #3498db;
        }

        .error-message {
            color: #e74c3c;
            margin-bottom: 20px;
            font-weight: 600;
            animation: shake 0.3s ease;
        }

        @keyframes shake {
            0% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
            100% { transform: translateX(0); }
        }

        .register-text {
            margin-top: 18px;
            font-size: 0.95rem;
            color: #7f8c8d;
        }
        .register-text a {
            color: #2980b9;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        .register-text a:hover {
            color: #3498db;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Form Login</h2>

        <?php if (isset($error)) : ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" novalidate>
            <input type="email" name="email" placeholder="Email" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>

        <div class="register-text">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</body>
</html>
