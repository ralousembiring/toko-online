<?php

session_start();


// ========================================
// CEK LOGIN ADMIN
// ========================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");

    exit();

}


require_once "../config/database.php";


// ========================================
// DATA DASHBOARD
// ========================================


// Total produk

$stmt = $pdo->query(
    "SELECT COUNT(*) FROM produk"
);

$totalProduk = $stmt->fetchColumn();


// Total stok

$stmt = $pdo->query(
    "SELECT COALESCE(SUM(stok), 0) FROM produk"
);

$totalStok = $stmt->fetchColumn();


// Produk stok rendah
// (stok <= 5)

$stmt = $pdo->query(
    "SELECT COUNT(*) FROM produk WHERE stok <= 5"
);

$stokRendah = $stmt->fetchColumn();


// Produk terbaru

$stmt = $pdo->query(
    "SELECT *
     FROM produk
     ORDER BY id DESC
     LIMIT 5"
);

$produkTerbaru = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        Dashboard Admin - Toko Online
    </title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            font-family: Arial, sans-serif;

            background: #f4f6f9;

        }


        /* ==============================
           SIDEBAR
        ============================== */

        .sidebar {

            position: fixed;

            left: 0;

            top: 0;

            width: 240px;

            height: 100vh;

            background: #1f2937;

            color: white;

            padding: 25px 15px;

        }


        .logo {

            font-size: 22px;

            font-weight: bold;

            text-align: center;

            margin-bottom: 35px;

        }


        .menu a {

            display: block;

            color: #d1d5db;

            text-decoration: none;

            padding: 13px 15px;

            border-radius: 7px;

            margin-bottom: 5px;

        }


        .menu a:hover {

            background: #374151;

            color: white;

        }


        .menu .active {

            background: #2563eb;

            color: white;

        }


        .logout {

            position: absolute;

            bottom: 25px;

            left: 15px;

            right: 15px;

        }


        .logout a {

            display: block;

            text-align: center;

            background: #dc2626;

            color: white;

            padding: 11px;

            border-radius: 7px;

            text-decoration: none;

        }


        /* ==============================
           MAIN
        ============================== */

        .main {

            margin-left: 240px;

            padding: 30px;

        }


        .topbar {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 30px;

        }


        .topbar h1 {

            margin: 0;

        }


        .admin-name {

            background: white;

            padding: 10px 15px;

            border-radius: 8px;

            box-shadow:
                0 2px 8px rgba(0,0,0,0.05);

        }


        /* ==============================
           CARDS
        ============================== */

        .cards {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 20px;

            margin-bottom: 30px;

        }


        .card {

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);

        }


        .card-title {

            color: #6b7280;

            margin-bottom: 10px;

        }


        .card-number {

            font-size: 32px;

            font-weight: bold;

        }


        /* ==============================
           CONTENT
        ============================== */

        .content-box {

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);

        }


        .content-header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;

        }


        .content-header h2 {

            margin: 0;

        }


        .btn {

            background: #2563eb;

            color: white;

            padding: 10px 15px;

            border-radius: 6px;

            text-decoration: none;

        }


        table {

            width: 100%;

            border-collapse: collapse;

        }


        th,
        td {

            padding: 13px;

            text-align: left;

            border-bottom: 1px solid #eee;

        }


        th {

            color: #6b7280;

            font-size: 14px;

        }


        /* ==============================
           RESPONSIVE
        ============================== */

        @media (max-width: 900px) {

            .sidebar {

                width: 200px;

            }

            .main {

                margin-left: 200px;

            }

            .cards {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 600px) {

            .sidebar {

                position: relative;

                width: 100%;

                height: auto;

            }

            .logout {

                position: static;

                margin-top: 20px;

            }

            .main {

                margin-left: 0;

            }

        }

    </style>

</head>


<body>


<!-- =====================================
     SIDEBAR
===================================== -->

<div class="sidebar">


    <div class="logo">

        🛒 TOKO ONLINE

    </div>


    <div class="menu">

        <a
            href="index.php"
            class="active"
        >
            📊 Dashboard
        </a>


        <a href="produk/index.php">
            📦 Produk
        </a>


       <a href="pesanan/index.php">
          🧾 Pesanan
       </a>


        <a href="#">
            👥 Pelanggan
        </a>

    </div>


    <div class="logout">

        <a href="logout.php">

            🚪 Logout

        </a>

    </div>

</div>



<!-- =====================================
     MAIN CONTENT
===================================== -->

<div class="main">


    <div class="topbar">

        <h1>
            Dashboard
        </h1>


        <div class="admin-name">

            👤
            <?= htmlspecialchars(
                $_SESSION["admin_username"]
            ) ?>

        </div>

    </div>



    <!-- ==============================
         STATISTICS
    ============================== -->

    <div class="cards">


        <div class="card">

            <div class="card-title">

                📦 Total Produk

            </div>

            <div class="card-number">

                <?= $totalProduk ?>

            </div>

        </div>



        <div class="card">

            <div class="card-title">

                📊 Total Stok

            </div>

            <div class="card-number">

                <?= $totalStok ?>

            </div>

        </div>



        <div class="card">

            <div class="card-title">

                ⚠️ Stok Rendah

            </div>

            <div class="card-number">

                <?= $stokRendah ?>

            </div>

        </div>


    </div>



    <!-- ==============================
         PRODUK TERBARU
    ============================== -->

    <div class="content-box">


        <div class="content-header">

            <h2>
                📦 Produk Terbaru
            </h2>


            <a
                href="produk/index.php"
                class="btn"
            >
                Kelola Produk
            </a>

        </div>



        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Nama Produk</th>

                    <th>Harga</th>

                    <th>Stok</th>

                </tr>

            </thead>


            <tbody>


                <?php foreach (
                    $produkTerbaru
                    as $item
                ): ?>

                    <tr>

                        <td>
                            <?= $item["id"] ?>
                        </td>


                        <td>
                            <?= htmlspecialchars(
                                $item["nama"]
                            ) ?>
                        </td>


                        <td>

                            Rp<?= number_format(
                                $item["harga"],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </td>


                        <td>
                            <?= $item["stok"] ?>
                        </td>

                    </tr>


                <?php endforeach; ?>


            </tbody>

        </table>


    </div>


</div>


</body>

</html>