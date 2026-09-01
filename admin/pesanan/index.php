<?php

session_start();


// ========================================
// CEK LOGIN
// ========================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: ../login.php");

    exit();

}


require_once "../../config/database.php";


// ========================================
// AMBIL DATA PESANAN
// ========================================

$stmt = $pdo->query("
    SELECT *
    FROM pesanan
    ORDER BY id DESC
");

$pesanan = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        Pesanan - Admin
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


        .navbar {

            background: #1f2937;

            color: white;

            padding: 18px 30px;

            display: flex;

            justify-content: space-between;

            align-items: center;

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

            margin-bottom: 25px;

        }


        .header h1 {

            margin: 0;

        }


        .box {

            background: white;

            padding: 25px;

            border-radius: 10px;

            box-shadow:
                0 3px 10px rgba(0,0,0,0.06);

            overflow-x: auto;

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


        .status {

            display: inline-block;

            padding: 6px 10px;

            border-radius: 20px;

            font-size: 13px;

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


        .empty {

            text-align: center;

            padding: 40px;

            color: #666;

        }

    </style>

</head>


<body>


    <div class="navbar">

        <strong>
            🛒 Admin Toko Online
        </strong>


        <a href="../index.php">
            ← Dashboard
        </a>

    </div>



    <div class="container">


        <div class="header">

            <h1>
                🧾 Daftar Pesanan
            </h1>

        </div>



        <div class="box">


            <?php if (count($pesanan) > 0): ?>


                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Pelanggan
                            </th>

                            <th>
                                No. HP
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                 Tanggal
                            </th>

                             <th>
                                 Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach (
                            $pesanan
                            as $item
                        ): ?>


                            <?php

                            $statusClass =
                                strtolower(
                                    $item["status"]
                                );

                            $statusClass =
                                str_replace(
                                    " ",
                                    "",
                                    $statusClass
                                );

                            ?>


                            <tr>

                                <td>
                                    #<?= $item["id"] ?>
                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $item["nama_pelanggan"]
                                    ) ?>

                                </td>


                                <td>

                                    <?= htmlspecialchars(
                                        $item["no_hp"]
                                    ) ?>

                                </td>


                                <td>

                                    Rp<?= number_format(
                                        $item["total_harga"],
                                        0,
                                        ',',
                                        '.'
                                    ) ?>

                                </td>


                                <td>

                                    <span
                                        class="status <?= $statusClass ?>"
                                    >

                                        <?= htmlspecialchars(
                                            $item["status"]
                                        ) ?>

                                    </span>

                                </td>


                                <td>

                                    <?= date(
                                        "d-m-Y H:i",
                                        strtotime(
                                            $item["tanggal_pesanan"]
                                        )
                                    ) ?>

                                </td>
                                <td>

    <a
        href="detail.php?id=<?= $item['id'] ?>"
        style="
            background:#2563eb;
            color:white;
            padding:8px 12px;
            border-radius:5px;
            text-decoration:none;
        "
    >
        👁 Detail
    </a>

</td>

                            </tr>


                        <?php endforeach; ?>


                    </tbody>

                </table>


            <?php else: ?>


                <div class="empty">

                    <h3>
                        📭 Belum ada pesanan
                    </h3>

                    <p>
                        Pesanan pelanggan akan muncul di sini.
                    </p>

                </div>


            <?php endif; ?>


        </div>


    </div>


</body>

</html>