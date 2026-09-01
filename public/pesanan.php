<?php

session_start();

require_once "../config/database.php";

// ========================================
// CEK ID PESANAN
// ========================================

$orderId = $_SESSION["last_order_id"] ?? 0;

if (!$orderId) {
    header("Location: index.php");
    exit();
}

// ========================================
// AMBIL DATA PESANAN
// ========================================

$stmt = $pdo->prepare("
    SELECT *
    FROM pesanan
    WHERE id = ?
");

$stmt->execute([$orderId]);

$pesanan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pesanan) {
    die("Pesanan tidak ditemukan.");
}

// ========================================
// AMBIL DETAIL PRODUK
// ========================================

$stmt = $pdo->prepare("
    SELECT
        detail_pesanan.*,
        produk.nama AS nama_produk
    FROM detail_pesanan

    INNER JOIN produk
        ON produk.id = detail_pesanan.produk_id

    WHERE detail_pesanan.pesanan_id = ?

    ORDER BY detail_pesanan.id ASC
");

$stmt->execute([$orderId]);

$detail = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        Detail Pesanan #<?= $pesanan["id"] ?>
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6fa;
        }

        .container {
            max-width: 850px;
            margin: 40px auto;
            padding: 20px;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;

            box-shadow:
                0 3px 10px rgba(0,0,0,0.06);
        }

        .success {
            text-align: center;
        }

        .success h1 {
            color: #166534;
            margin-bottom: 10px;
        }

        .order-number {
            font-size: 28px;
            font-weight: bold;
            margin: 20px 0;
        }

        .status {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .menunggu {
            background: #fef3c7;
            color: #92400e;
        }

        .diproses {
            background: #dbeafe;
            color: #1e40af;
        }

        .dikirim {
            background: #e0e7ff;
            color: #3730a3;
        }

        .selesai {
            background: #dcfce7;
            color: #166534;
        }

        .dibatalkan {
            background: #fee2e2;
            color: #991b1b;
        }

        .info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 7px;
        }

        .info-item strong {
            display: block;
            margin-bottom: 6px;
            color: #6b7280;
        }

        .full {
            grid-column: 1 / -1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        th {
            background: #f8fafc;
        }

        .total {
            text-align: right;
            font-size: 22px;
            font-weight: bold;
            margin-top: 20px;
        }

        .actions {
            text-align: center;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            margin: 5px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        @media (max-width: 700px) {

            .info {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
            }

            table {
                font-size: 14px;
            }

        }

    </style>

</head>

<body>

<div class="container">

    <!-- ========================================
         PESANAN BERHASIL
    ========================================= -->

    <div class="box success">

        <h1>
            🎉 Pesanan Berhasil!
        </h1>

        <p>
            Terima kasih sudah berbelanja di Toko Online kami.
        </p>

        <div class="order-number">

            Pesanan #<?= $pesanan["id"] ?>

        </div>

        <p>

            Status Pesanan:

            <?php

            $statusClass = strtolower(
                str_replace(
                    " ",
                    "",
                    $pesanan["status"]
                )
            );

            ?>

            <span class="status <?= $statusClass ?>">

                <?= htmlspecialchars(
                    $pesanan["status"]
                ) ?>

            </span>

        </p>

    </div>


    <!-- ========================================
         INFORMASI PELANGGAN
    ========================================= -->

    <div class="box">

        <h2>
            👤 Informasi Pemesan
        </h2>

        <div class="info">

            <div class="info-item">

                <strong>
                    Nama
                </strong>

                <?= htmlspecialchars(
                    $pesanan["nama_pelanggan"]
                ) ?>

            </div>


            <div class="info-item">

                <strong>
                    No. HP
                </strong>

                <?= htmlspecialchars(
                    $pesanan["no_hp"]
                ) ?>

            </div>


            <div class="info-item">

                <strong>
                    Email
                </strong>

                <?= htmlspecialchars(
                    $pesanan["email"] ?: "-"
                ) ?>

            </div>


            <div class="info-item">

                <strong>
                    Tanggal Pesanan
                </strong>

                <?= date(
                    "d-m-Y H:i",
                    strtotime(
                        $pesanan["tanggal_pesanan"]
                    )
                ) ?>

            </div>


            <div class="info-item full">

                <strong>
                    Alamat Pengiriman
                </strong>

                <?= nl2br(
                    htmlspecialchars(
                        $pesanan["alamat"]
                    )
                ) ?>

            </div>

        </div>

    </div>


    <!-- ========================================
         PRODUK
    ========================================= -->

    <div class="box">

        <h2>
            📦 Produk yang Dipesan
        </h2>

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

                </tr>

            </thead>


            <tbody>

                <?php foreach ($detail as $item): ?>

                    <tr>

                        <td>

                            <?= htmlspecialchars(
                                $item["nama_produk"]
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

                            <?= $item["jumlah"] ?>

                        </td>


                        <td>

                            Rp<?= number_format(
                                $item["harga"]
                                * $item["jumlah"],
                                0,
                                ',',
                                '.'
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>


        <div class="total">

            Total:

            Rp<?= number_format(
                $pesanan["total_harga"],
                0,
                ',',
                '.'
            ) ?>

        </div>

    </div>


    <!-- ========================================
         BUTTON
    ========================================= -->

    <div class="actions">

        <a
            href="index.php"
            class="btn btn-primary"
        >
            🛒 Kembali ke Toko
        </a>

    </div>

</div>

</body>

</html>