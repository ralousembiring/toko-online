```php
<?php

session_start();

require_once "../config/database.php";

// ========================================
// CEK LOGIN
// ========================================

if (!isset($_SESSION["pelanggan_id"])) {

    header("Location: login.php");
    exit;

}


// ========================================
// AMBIL DATA PELANGGAN
// ========================================

$id = $_SESSION["pelanggan_id"];

$stmt = $pdo->prepare("
    SELECT *
    FROM pelanggan
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);


// Kalau data tidak ditemukan
if (!$pelanggan) {

    session_unset();
    session_destroy();

    header("Location: login.php");
    exit;

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

    <title>
        Profil Saya - Toko Online
    </title>


    <style>

        * {
            box-sizing: border-box;
        }

        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background: #f4f6f9;

            color: #1f2937;

        }


        /* ================================
           NAVBAR
        ================================= */

        .navbar {

            background: #1f2937;

            color: white;

            padding: 18px 40px;

            display: flex;

            justify-content: space-between;

            align-items: center;

        }


        .navbar h2 {

            margin: 0;

        }


        .navbar a {

            color: white;

            text-decoration: none;

            margin-left: 20px;

        }


        .navbar a:hover {

            text-decoration: underline;

        }


        /* ================================
           CONTAINER
        ================================= */

        .container {

            max-width: 700px;

            margin: 50px auto;

            padding: 20px;

        }


        /* ================================
           PROFILE CARD
        ================================= */

        .profile-card {

            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.08);

        }


        .profile-title {

            text-align: center;

            margin-bottom: 30px;

        }


        .profile-icon {

            font-size: 60px;

            margin-bottom: 10px;

        }


        .profile-title h1 {

            margin: 0;

        }


        .profile-title p {

            color: #6b7280;

        }


        /* ================================
           DATA
        ================================= */

        .data {

            margin-bottom: 20px;

        }


        .data label {

            display: block;

            font-weight: bold;

            margin-bottom: 7px;

        }


        .data-value {

            background: #f3f4f6;

            padding: 12px;

            border-radius: 7px;

            color: #374151;

        }


        /* ================================
           BUTTON
        ================================= */

        .buttons {

            display: flex;

            gap: 10px;

            margin-top: 30px;

        }


        .btn {

            flex: 1;

            padding: 12px;

            border-radius: 7px;

            text-align: center;

            text-decoration: none;

            border: none;

            cursor: pointer;

        }


        .btn-back {

            background: #e5e7eb;

            color: #1f2937;

        }


        .btn-logout {

            background: #dc2626;

            color: white;

        }


        @media (max-width: 600px) {

            .navbar {

                padding: 15px 20px;

                flex-direction: column;

                gap: 15px;

            }


            .navbar a {

                margin-left: 10px;

            }


            .container {

                margin-top: 20px;

            }


            .buttons {

                flex-direction: column;

            }

        }

    </style>

</head>


<body>


<!-- ========================================
     NAVBAR
======================================== -->

<nav class="navbar">


    <h2>

        🛒 Toko Online

    </h2>


    <div>


        <a href="index.php">

            🏠 Produk

        </a>


        <a href="cart.php">

            🛒 Keranjang

        </a>


        <a href="logout.php">

            🚪 Logout

        </a>


    </div>


</nav>


<!-- ========================================
     PROFILE
======================================== -->

<div class="container">


    <div class="profile-card">


        <div class="profile-title">

            <div class="profile-icon">

                👤

            </div>


            <h1>

                Profil Saya

            </h1>


            <p>

                Informasi akun pelanggan

            </p>

        </div>


        <!-- NAMA -->

        <div class="data">

            <label>

                Nama

            </label>


            <div class="data-value">

                <?= htmlspecialchars($pelanggan["nama"]) ?>

            </div>

        </div>


        <!-- EMAIL -->

        <div class="data">

            <label>

                Email

            </label>


            <div class="data-value">

                <?= htmlspecialchars($pelanggan["email"]) ?>

            </div>

        </div>


        <!-- NO HP -->

        <div class="data">

            <label>

                No. HP

            </label>


            <div class="data-value">

                <?= !empty($pelanggan["no_hp"])
                    ? htmlspecialchars($pelanggan["no_hp"])
                    : "Belum diisi"
                ?>

            </div>

        </div>


        <!-- ALAMAT -->

        <div class="data">

            <label>

                Alamat

            </label>


            <div class="data-value">

                <?= !empty($pelanggan["alamat"])
                    ? nl2br(htmlspecialchars($pelanggan["alamat"]))
                    : "Belum diisi"
                ?>

            </div>

        </div>


        <!-- BUTTON -->

        <div class="buttons">


            <a
                href="index.php"
                class="btn btn-back"
            >

                ← Kembali

            </a>


            <a
                href="logout.php"
                class="btn btn-logout"
            >

                Logout

            </a>


        </div>


    </div>


</div>


</body>

</html>
```
