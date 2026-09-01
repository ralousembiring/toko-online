<?php

session_start();

require_once "../config/database.php";

// Kalau sudah login, langsung ke halaman utama
if (isset($_SESSION["pelanggan_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email == "" || $password == "") {

        $error = "Email dan password wajib diisi.";

    } else {

        // Cari pelanggan berdasarkan email
        $stmt = $pdo->prepare("
            SELECT *
            FROM pelanggan
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->execute([$email]);

        $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pelanggan) {

            // Cek password
            if (password_verify($password, $pelanggan["password"])) {

                // Buat session pelanggan
                $_SESSION["pelanggan_id"] = $pelanggan["id"];
                $_SESSION["pelanggan_nama"] = $pelanggan["nama"];
                $_SESSION["pelanggan_email"] = $pelanggan["email"];

                // Kembali ke halaman utama
                header("Location: index.php");
                exit;

            } else {

                $error = "Email atau password salah.";

            }

        } else {

            $error = "Email atau password salah.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Login - Toko Online</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f5f5;
        }

        .container {
            width: 400px;
            max-width: 90%;
            margin: 80px auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        input {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            border: none;
            border-radius: 5px;
            background: #222;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background: #444;
        }

        .error {
            background: #ffdede;
            color: #b00000;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .success {
            background: #dff5e1;
            color: #16702a;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .register {
            text-align: center;
            margin-top: 20px;
        }

        .register a {
            color: #0066cc;
            text-decoration: none;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>Login Pelanggan</h2>

    <?php if (isset($_SESSION["success"])): ?>

        <div class="success">

            <?= htmlspecialchars($_SESSION["success"]) ?>

        </div>

        <?php unset($_SESSION["success"]); ?>

    <?php endif; ?>


    <?php if ($error != ""): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <form method="POST">

        <label>Email</label>

        <input
            type="email"
            name="email"
            required
        >


        <label>Password</label>

        <input
            type="password"
            name="password"
            required
        >


        <button type="submit">

            Login

        </button>

    </form>


    <div class="register">

        Belum punya akun?

        <a href="register.php">

            Daftar sekarang

        </a>

    </div>

</div>

</body>

</html>