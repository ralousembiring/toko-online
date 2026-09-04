
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

<!-- ========================================
     FLOATING WHATSAPP BUTTON
========================================= -->

<a
    href="https://wa.me/6281933485477?text=Halo%20Admin%2C%20saya%20ingin%20bertanya%20mengenai%20produk%20di%20Toko%20Online."
    class="whatsapp-float"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Chat melalui WhatsApp"
>

    <!-- Logo WhatsApp -->
    <svg
        class="whatsapp-logo"
        viewBox="0 0 32 32"
        xmlns="http://www.w3.org/2000/svg"
        aria-hidden="true"
    >
        <path
            fill="currentColor"
            d="M16 3C8.82 3 3 8.82 3 16c0 2.3.6 4.56 1.74 6.55L3 29l6.62-1.7A12.93 12.93 0 0 0 16 29c7.18 0 13-5.82 13-13S23.18 3 16 3Zm0 23.6c-1.98 0-3.92-.53-5.62-1.54l-.4-.24-3.93 1.01 1.05-3.83-.26-.42A10.63 10.63 0 1 1 16 26.6Zm5.83-7.96c-.32-.16-1.9-.94-2.2-1.05-.3-.11-.52-.16-.74.16-.22.33-.84 1.05-1.03 1.27-.19.22-.38.24-.7.08-.32-.16-1.35-.5-2.57-1.58-.95-.85-1.59-1.9-1.78-2.22-.19-.33-.02-.5.14-.66.15-.15.32-.38.49-.57.16-.19.22-.33.33-.55.11-.22.05-.41-.03-.57-.08-.16-.74-1.78-1.01-2.44-.27-.64-.54-.55-.74-.56h-.63c-.22 0-.57.08-.87.41-.3.33-1.14 1.11-1.14 2.71s1.17 3.14 1.33 3.36c.16.22 2.3 3.51 5.57 4.92.78.34 1.39.54 1.87.69.79.25 1.51.21 2.08.13.63-.09 1.9-.78 2.17-1.54.27-.76.27-1.41.19-1.54-.08-.13-.3-.21-.63-.37Z"
        />
    </svg>

    <span class="whatsapp-text">
        Chat WhatsApp
    </span>

</a>


<style>

/* ========================================
   FLOATING WHATSAPP
========================================= */

.whatsapp-float {

    position: fixed;

    right: 25px;
    bottom: 25px;

    display: flex;
    align-items: center;
    justify-content: center;

    gap: 10px;

    padding: 13px 18px;

    background: #25D366;

    color: white;

    text-decoration: none;

    border-radius: 50px;

    font-family: Arial, sans-serif;

    font-size: 14px;

    font-weight: bold;

    box-shadow:
        0 4px 15px rgba(0, 0, 0, 0.25);

    z-index: 9999;

    transition:
        transform 0.3s ease,
        box-shadow 0.3s ease;

}


/* ========================================
   LOGO WHATSAPP
========================================= */

.whatsapp-logo {

    width: 30px;
    height: 30px;

    flex-shrink: 0;

}


/* ========================================
   HOVER EFFECT
========================================= */

.whatsapp-float:hover {

    transform: translateY(-4px);

    box-shadow:
        0 8px 22px rgba(0, 0, 0, 0.3);

}


/* ========================================
   MOBILE
========================================= */

@media (max-width: 600px) {

    .whatsapp-float {

        width: 58px;
        height: 58px;

        padding: 0;

        right: 15px;
        bottom: 15px;

        border-radius: 50%;

    }

    .whatsapp-logo {

        width: 34px;
        height: 34px;

    }

    .whatsapp-text {

        display: none;

    }

}


/* ========================================
   ANIMASI DENYUT
========================================= */

@keyframes whatsappPulse {

    0% {
        box-shadow:
            0 0 0 0 rgba(37, 211, 102, 0.5);
    }

    70% {
        box-shadow:
            0 0 0 12px rgba(37, 211, 102, 0);
    }

    100% {
        box-shadow:
            0 0 0 0 rgba(37, 211, 102, 0);
    }

}

.whatsapp-float {

    animation:
        whatsappPulse 2.5s infinite;

}

</style>

</body>

</html>
```
