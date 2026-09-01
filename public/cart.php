<?php

session_start();

require_once "../config/database.php";


// ========================================
// INISIALISASI CART
// ========================================

if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}


// ========================================
// PROSES CART
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";

    $produk_id = (int) ($_POST["produk_id"] ?? 0);


    // ====================================
    // TAMBAH
    // ====================================

    if ($action === "tambah" && $produk_id > 0) {

        $stmt = $pdo->prepare(
            "SELECT stok FROM produk WHERE id = ?"
        );

        $stmt->execute([$produk_id]);

        $product = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($product) {

            $stok = (int) $product["stok"];

            $jumlahSekarang =
                $_SESSION["cart"][$produk_id] ?? 0;


            if ($jumlahSekarang < $stok) {

                $_SESSION["cart"][$produk_id] =
                    $jumlahSekarang + 1;

            }

        }

    }


    // ====================================
    // KURANGI
    // ====================================

    elseif ($action === "kurangi" && $produk_id > 0) {

        if (isset($_SESSION["cart"][$produk_id])) {

            $_SESSION["cart"][$produk_id]--;

            if ($_SESSION["cart"][$produk_id] <= 0) {

                unset(
                    $_SESSION["cart"][$produk_id]
                );

            }

        }

    }


    // ====================================
    // HAPUS
    // ====================================

    elseif ($action === "hapus" && $produk_id > 0) {

        unset(
            $_SESSION["cart"][$produk_id]
        );

    }


    // ====================================
    // UPDATE JUMLAH
    // ====================================

    elseif ($action === "update") {

        $jumlah = (int) (
            $_POST["jumlah"] ?? 0
        );


        if ($produk_id > 0 && $jumlah > 0) {

            $stmt = $pdo->prepare(
                "SELECT stok FROM produk WHERE id = ?"
            );

            $stmt->execute([$produk_id]);

            $product =
                $stmt->fetch(PDO::FETCH_ASSOC);


            if ($product) {

                $stok =
                    (int) $product["stok"];


                $_SESSION["cart"][$produk_id] =
                    min($jumlah, $stok);

            }

        }

    }


    header("Location: cart.php");

    exit();
}


// ========================================
// AMBIL DATA PRODUK
// ========================================

$cartItems = [];

$total = 0;


if (!empty($_SESSION["cart"])) {

    $ids = array_keys($_SESSION["cart"]);


    $placeholders = implode(
        ",",
        array_fill(
            0,
            count($ids),
            "?"
        )
    );


    $stmt = $pdo->prepare(
        "SELECT *
         FROM produk
         WHERE id IN ($placeholders)"
    );


    $stmt->execute($ids);


    $products =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($products as $product) {

        $jumlah =
            $_SESSION["cart"][$product["id"]];


        // ====================================
        // CEK STOK
        // ====================================

        if ($jumlah > $product["stok"]) {

            $jumlah =
                (int) $product["stok"];


            if ($jumlah > 0) {

                $_SESSION["cart"][
                    $product["id"]
                ] = $jumlah;

            } else {

                unset(
                    $_SESSION["cart"][
                        $product["id"]
                    ]
                );

                continue;

            }

        }


        $subtotal =
            $product["harga"] * $jumlah;


        $total += $subtotal;


        $cartItems[] = [

            "id" =>
                $product["id"],

            "nama" =>
                $product["nama"],

            "harga" =>
                $product["harga"],

            "stok" =>
                $product["stok"],

            "jumlah" =>
                $jumlah,

            "subtotal" =>
                $subtotal

        ];

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

    <title>
        Keranjang Belanja
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


        .container {

            max-width: 1100px;

            margin: 40px auto;

            padding: 20px;

        }


        .header {

            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;

        }


        .header h1 {

            margin: 0;

        }


        .back {

            text-decoration: none;

            color: #2563eb;

        }


        .cart-box {

            background: white;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 3px 12px
                rgba(0,0,0,0.06);

        }


        table {

            width: 100%;

            border-collapse: collapse;

        }


        th,
        td {

            padding: 16px;

            border-bottom:
                1px solid #eee;

            text-align: left;

        }


        th {

            background: #f8fafc;

        }


        .product-name {

            font-weight: bold;

        }


        .price {

            white-space: nowrap;

        }


        .quantity {

            display: flex;

            align-items: center;

            gap: 8px;

        }


        .quantity form {

            margin: 0;

        }


        .quantity button {

            width: 32px;

            height: 32px;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            font-size: 18px;

        }


        .minus {

            background: #e5e7eb;

        }


        .plus {

            background: #2563eb;

            color: white;

        }


        .quantity span {

            min-width: 30px;

            text-align: center;

            font-weight: bold;

        }


        .delete {

            background: #fee2e2;

            color: #dc2626;

            border: none;

            padding: 8px 12px;

            border-radius: 6px;

            cursor: pointer;

        }


        .stock {

            color: #6b7280;

            font-size: 13px;

            margin-top: 5px;

        }


        .summary {

            margin-top: 25px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            padding-top: 20px;

            border-top:
                2px solid #eee;

        }


        .total-label {

            font-size: 16px;

            color: #6b7280;

        }


        .total-price {

            font-size: 28px;

            font-weight: bold;

        }


        .checkout {

            background: #16a34a;

            color: white;

            text-decoration: none;

            padding: 13px 22px;

            border-radius: 7px;

            font-weight: bold;

        }


        .empty {

            background: white;

            padding: 70px 20px;

            text-align: center;

            border-radius: 12px;

            box-shadow:
                0 3px 12px
                rgba(0,0,0,0.06);

        }


        .empty-icon {

            font-size: 60px;

        }


        .shop {

            display: inline-block;

            margin-top: 20px;

            background: #2563eb;

            color: white;

            text-decoration: none;

            padding: 12px 20px;

            border-radius: 7px;

        }


        @media (max-width: 700px) {

            .container {

                margin-top: 10px;

            }


            .header {

                align-items: flex-start;

                gap: 10px;

                flex-direction: column;

            }


            .cart-box {

                padding: 10px;

                overflow-x: auto;

            }


            table {

                min-width: 700px;

            }


            .summary {

                flex-direction: column;

                align-items: stretch;

                gap: 20px;

            }


            .checkout {

                text-align: center;

            }

        }

    </style>

</head>


<body>


<div class="container">


    <div class="header">

        <h1>
            🛒 Keranjang Belanja
        </h1>


        <a
            href="index.php"
            class="back"
        >
            ← Kembali ke Toko
        </a>

    </div>


    <?php if (!empty($cartItems)): ?>


        <div class="cart-box">

            <table>

                <thead>

                    <tr>

                        <th>
                            Produk
                        </th>

                        <th>
                            Harga
                        </th>

                        <th>
                            Jumlah
                        </th>

                        <th>
                            Subtotal
                        </th>

                        <th>
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach (
                        $cartItems
                        as $item
                    ): ?>


                        <tr>


                            <td>

                                <div class="product-name">

                                    <?= htmlspecialchars(
                                        $item["nama"]
                                    ) ?>

                                </div>


                                <div class="stock">

                                    Stok tersedia:
                                    <?= $item["stok"] ?>

                                </div>

                            </td>


                            <td class="price">

                                Rp<?= number_format(
                                    $item["harga"],
                                    0,
                                    ',',
                                    '.'
                                ) ?>

                            </td>


                            <td>


                                <div class="quantity">


                                    <!-- KURANGI -->

                                    <form
                                        method="POST"
                                    >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="kurangi"
                                        >

                                        <input
                                            type="hidden"
                                            name="produk_id"
                                            value="<?= $item["id"] ?>"
                                        >


                                        <button
                                            type="submit"
                                            class="minus"
                                        >
                                            −
                                        </button>

                                    </form>


                                    <span>

                                        <?= $item["jumlah"] ?>

                                    </span>


                                    <!-- TAMBAH -->

                                    <form
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
                                            value="<?= $item["id"] ?>"
                                        >


                                        <button
                                            type="submit"
                                            class="plus"
                                        >
                                            +
                                        </button>

                                    </form>


                                </div>


                            </td>


                            <td class="price">

                                <strong>

                                    Rp<?= number_format(
                                        $item["subtotal"],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </strong>

                            </td>


                            <td>


                                <form
                                    method="POST"
                                >

                                    <input
                                        type="hidden"
                                        name="action"
                                        value="hapus"
                                    >

                                    <input
                                        type="hidden"
                                        name="produk_id"
                                        value="<?= $item["id"] ?>"
                                    >


                                    <button
                                        type="submit"
                                        class="delete"
                                    >
                                        🗑 Hapus
                                    </button>

                                </form>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>


            <div class="summary">


                <div>

                    <div class="total-label">

                        Total Belanja

                    </div>


                    <div class="total-price">

                        Rp<?= number_format(
                            $total,
                            0,
                            ',',
                            '.'
                        ) ?>

                    </div>

                </div>


                <a
                    href="checkout.php"
                    class="checkout"
                >
                    Checkout →
                </a>


            </div>


        </div>


    <?php else: ?>


        <div class="empty">


            <div class="empty-icon">
                🛒
            </div>


            <h2>
                Keranjang masih kosong
            </h2>


            <p>
                Yuk pilih produk yang kamu suka!
            </p>


            <a
                href="index.php"
                class="shop"
            >
                🛍️ Mulai Belanja
            </a>


        </div>


    <?php endif; ?>


</div>


</body>

</html>