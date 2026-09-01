<?php

session_start();

require_once "../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nama = trim($_POST["nama"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $no_hp = trim($_POST["no_hp"]);
    $alamat = trim($_POST["alamat"]);

    if ($nama == "" || $email == "" || $password == "") {
        $error = "Nama, email, dan password wajib diisi.";
    } else {

        // Cek apakah email sudah terdaftar
        $stmt = $pdo->prepare("SELECT id FROM pelanggan WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {

            $error = "Email sudah terdaftar.";

        } else {

            // Hash password
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            // Simpan pelanggan
            $stmt = $pdo->prepare("
                INSERT INTO pelanggan
                (nama, email, password, no_hp, alamat)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $nama,
                $email,
                $password_hash,
                $no_hp,
                $alamat
            ]);

            $_SESSION["success"] = "Registrasi berhasil. Silakan login.";

            header("Location: login.php");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar - Toko Online</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 400px;
            max-width: 90%;
            margin: 50px auto;
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

        input,
        textarea {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        textarea {
            resize: vertical;
            height: 80px;
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

        .login {
            text-align: center;
            margin-top: 20px;
        }

        .login a {
            text-decoration: none;
            color: #0066cc;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>Daftar Akun</h2>

    <?php if (isset($error)): ?>

        <div class="error">
            <?= htmlspecialchars($error) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Nama</label>

        <input
            type="text"
            name="nama"
            required
        >

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

        <label>No. HP</label>

        <input
            type="text"
            name="no_hp"
        >

        <label>Alamat</label>

        <textarea
            name="alamat"
        ></textarea>

        <button type="submit">
            Daftar
        </button>

    </form>

    <div class="login">

        Sudah punya akun?

        <a href="login.php">
            Login
        </a>

    </div>

</div>

</body>

</html>