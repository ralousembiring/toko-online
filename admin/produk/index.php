<?php

session_start();

if (!isset($_SESSION["admin_id"])) {

    header("Location: login.php");

    exit();

}

require_once "../../config/database.php";

$query = "SELECT * FROM produk ORDER BY id DESC";

$result = $pdo->query($query);

$produk = $result->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Kelola Produk - Admin</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6fa;
        }

        .navbar {
            background: #222;
            color: white;
            padding: 18px 30px;

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
        }

        .container {
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 6px;
            text-decoration: none;
            color: white;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
        }

        .btn-edit {
            background: #f59e0b;
        }

        .btn-delete {
            background: #dc2626;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        th,
        td {
            padding: 15px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #222;
            color: white;
        }

        tr:hover {
            background: #f8f8f8;
        }

        .empty {
            text-align: center;
            padding: 30px;
        }

    </style>

</head>

<body>


    <!-- NAVBAR -->

    <div class="navbar">

        <h2>
            ⚙️ Admin Toko Online
        </h2>

        <a href="../index.php">
            ← Kembali ke Dashboard
        </a>

    </div>


    <!-- CONTENT -->

    <div class="container">

        <div class="header">

            <h1>
                📦 Kelola Produk
            </h1>

            <a
                href="tambah.php"
                class="btn btn-primary"
            >
                + Tambah Produk
            </a>

        </div>


        <?php if (count($produk) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Nama Produk</th>

                        <th>Harga</th>

                        <th>Stok</th>

                        <th>Deskripsi</th>

                        <th>Aksi</th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($produk as $item): ?>

                        <tr>

                            <td>
                                <?= $item["id"] ?>
                            </td>


                            <td>
                                <?= htmlspecialchars($item["nama"]) ?>
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


                            <td>
                                <?= htmlspecialchars($item["deskripsi"]) ?>
                            </td>


                            <td>

                                <a
                                    href="edit.php?id=<?= $item["id"] ?>"
                                    class="btn btn-edit"
                                >
                                    ✏️ Edit
                                </a>


                                <a
                                    href="hapus.php?id=<?= $item["id"] ?>"
                                    class="btn btn-delete"
                                    onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                >
                                    🗑️ Hapus
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>


        <?php else: ?>

            <div class="empty">

                <h3>
                    Belum ada produk.
                </h3>

                <p>
                    Silakan tambahkan produk baru.
                </p>

            </div>

        <?php endif; ?>

    </div>

</body>

</html>