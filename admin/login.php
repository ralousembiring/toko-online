<?php

session_start();

require_once "../config/database.php";


// ========================================
// JIKA SUDAH LOGIN
// ========================================

if (isset($_SESSION["admin_id"])) {

    header("Location: index.php");

    exit();

}


// ========================================
// VARIABEL
// ========================================

$error = "";


// ========================================
// PROSES LOGIN
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");

    $password = $_POST["password"] ?? "";


    // ========================================
    // VALIDASI
    // ========================================

    if ($username === "" || $password === "") {

        $error = "Username dan password wajib diisi.";

    } else {


        // ========================================
        // CARI ADMIN
        // ========================================

        $stmt = $pdo->prepare(
            "SELECT * FROM admin WHERE username = ? LIMIT 1"
        );

        $stmt->execute([$username]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);


        // ========================================
        // CEK PASSWORD
        // ========================================

        if (
    $admin &&
    $password === $admin["password"]
) {


            // ========================================
            // LOGIN BERHASIL
            // ========================================

            session_regenerate_id(true);


            $_SESSION["admin_id"] = $admin["id"];

            $_SESSION["admin_username"] =
                $admin["username"];


            header("Location: index.php");

            exit();


        } else {

            $error = "Username atau password salah.";

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

    <title>Login Admin</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background: #f5f6fa;

            min-height: 100vh;

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .login-box {

            width: 400px;

            max-width: 90%;

            background: white;

            padding: 35px;

            border-radius: 12px;

            box-shadow:
                0 5px 20px rgba(0,0,0,0.1);

        }


        h1 {

            text-align: center;

            margin-top: 0;

        }


        .subtitle {

            text-align: center;

            color: #666;

            margin-bottom: 30px;

        }


        .form-group {

            margin-bottom: 20px;

        }


        label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;

        }


        input {

            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 6px;

            font-size: 15px;

        }


        button {

            width: 100%;

            padding: 13px;

            background: #2563eb;

            color: white;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            font-size: 16px;

        }


        button:hover {

            background: #1d4ed8;

        }


        .error {

            background: #fee2e2;

            color: #991b1b;

            padding: 12px;

            border-radius: 6px;

            margin-bottom: 20px;

        }


        .back {

            display: block;

            text-align: center;

            margin-top: 20px;

            color: #2563eb;

            text-decoration: none;

        }

    </style>

</head>


<body>


    <div class="login-box">

        <h1>🔐 Admin Login</h1>

        <p class="subtitle">
            Toko Online
        </p>


        <?php if ($error !== ""): ?>

            <div class="error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Masukkan username"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    required
                >

            </div>


            <button type="submit">
                🔑 Login
            </button>


        </form>


        <a
            href="../public/index.php"
            class="back"
        >
            ← Kembali ke Toko
        </a>

    </div>


</body>

</html>