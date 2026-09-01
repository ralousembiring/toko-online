
<?php

session_start();

require_once "../config/database.php";

// ========================================
// AMBIL DATA PRODUK
// ========================================

$query = "SELECT * FROM produk ORDER BY id DESC";

$result = $pdo->query($query);

$produk = $result->fetchAll(PDO::FETCH_ASSOC);

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
        Toko Online
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

        .navbar .user {
            color: white;
            margin-left: 20px;
        }

        /* ================================
           CONTAINER
        ================================= */

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 20px;
        }

        .title {
            margin-bottom: 30px;
        }

        .title h1 {
            margin-bottom: 5px;
        }

        .title p {
            color: #6b7280;
        }

        /* ================================
           PRODUK
        ================================= */

        .products {
            display: grid;
            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(250px, 1fr)
                );
            gap: 20px;
        }

        .product {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.06);
        }

        /* ================================
           GAMBAR PRODUK
        ================================= */

        .product-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
            background: #f3f4f6;
        }

        .product h3 {
            margin-top: 0;
            font-size: 20px;
        }

        .price {
            font-size: 20px;
            font-weight: bold;
            margin: 10px 0;
        }

        .stock {
            color: #6b7280;
            font-size: 14px;
        }

        .description {
            color: #4b5563;
            min-height: 45px;
        }

        /* ================================
           BUTTON
        ================================= */

        .btn {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 7px;
            cursor: pointer;
            font-size: 15px;
        }

        .btn-cart {
            background: #2563eb;
            color: white;
        }

        .btn-cart:hover {
            background: #1d4ed8;
        }

        .btn-disabled {
            background: #d1d5db;
            color: #6b7280;
            cursor: not-allowed;
        }

        /* ================================
           EMPTY
        ================================= */

        .empty {
            background: white;
            padding: 50px;
            text-align: center;
            border-radius: 10px;
        }

        /* ================================
           RESPONSIVE
        ================================= */

        @media (max-width: 600px) {

            .navbar {
                padding: 15px 20px;
                flex-direction: column;
                gap: 15px;
            }

            .navbar h2 {
                font-size: 20px;
            }

            .navbar a {
                margin-left: 10px;
            }

            .container {
                margin-top: 20px;
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


        <?php if (isset($_SESSION["pelanggan_id"])): ?>

    <span class="user">

        👤 Halo,
        <?= htmlspecialchars($_SESSION["pelanggan_nama"]) ?>

    </span>

    <a href="profil.php">
        👤 Profil
    </a>

    <a href="logout.php">
        🚪 Logout
    </a>


        <?php else: ?>

            <a href="login.php">
                🔐 Login
            </a>


            <a href="register.php">
                📝 Daftar
            </a>

        <?php endif; ?>

    </div>

</nav>


<!-- ========================================
     CONTENT
======================================== -->

<div class="container">


    <div class="title">

        <h1>
            Daftar Produk
        </h1>

        <p>
            Pilih produk yang ingin kamu beli.
        </p>

    </div>


    <?php if (!empty($produk)): ?>


        <div class="products">


            <?php foreach ($produk as $item): ?>


                <div class="product">


                    <!-- =================================
                         GAMBAR PRODUK
                    ================================== -->

                    <?php if (!empty($item["gambar"])): ?>

                        <img
                            src="../uploads/product/<?= htmlspecialchars($item["gambar"]) ?>"
                            alt="<?= htmlspecialchars($item["nama"]) ?>"
                            class="product-image"
                        >

                    <?php else: ?>

                        <div
                            class="product-image"
                            style="
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-size:50px;
                            "
                        >
                            🌸
                        </div>

                    <?php endif; ?>


                    <!-- =================================
                         NAMA PRODUK
                    ================================== -->

                    <h3>

                        <?= htmlspecialchars($item["nama"]) ?>

                    </h3>


                    <!-- =================================
                         HARGA
                    ================================== -->

                    <div class="price">

                        Rp<?= number_format(
                            $item["harga"],
                            0,
                            ',',
                            '.'
                        ) ?>

                    </div>


                    <!-- =================================
                         STOK
                    ================================== -->

                    <div class="stock">

                        Stok:

                        <?= (int) $item["stok"] ?>

                    </div>


                    <!-- =================================
                         DESKRIPSI
                    ================================== -->

                    <p class="description">

                        <?= htmlspecialchars(
                            $item["deskripsi"] ?? ""
                        ) ?>

                    </p>


                    <!-- =================================
                         TAMBAH KE KERANJANG
                    ================================== -->

                    <?php if ((int) $item["stok"] > 0): ?>


                        <form
                            action="cart.php"
                            method="POST"
                        >

                            <input
                                type="hidden"
                                name="action"
                                value="tambah"
                            >


                            <input
                                type="hidden"
                                name="produk_id"
                                value="<?= (int) $item["id"] ?>"
                            >


                            <button
                                type="submit"
                                class="btn btn-cart"
                            >

                                🛒 Tambah ke Keranjang

                            </button>

                        </form>


                    <?php else: ?>


                        <button
                            class="btn btn-disabled"
                            disabled
                        >

                            Stok Habis

                        </button>


                    <?php endif; ?>


                </div>


            <?php endforeach; ?>


        </div>


    <?php else: ?>


        <div class="empty">

            <h2>
                📦 Belum ada produk
            </h2>

            <p>
                Silakan tambahkan produk melalui halaman admin.
            </p>

        </div>


    <?php endif; ?>


</div>


</body>

</html>
```
