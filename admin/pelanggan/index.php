<?php
session_start();

require_once "../../config/database.php";

// Cek login admin
if (!isset($_SESSION["admin_id"])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data pelanggan
$query = "SELECT id, nama, email, no_hp, alamat, created_at, poin
          FROM pelanggan
          ORDER BY id DESC";

$result = $pdo->query($query);
$pelanggan = $result->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pelanggan - Admin</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
        }

        .back-btn {
            text-decoration: none;
            background: #6c5ce7;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
            overflow-x: auto;
        }

        .info {
            margin-bottom: 20px;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th {
            background: #f1f2f6;
            text-align: left;
            padding: 14px;
            font-size: 14px;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:hover {
            background: #fafafa;
        }

        .poin {
            font-weight: bold;
            color: #6c5ce7;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #777;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="header">
        <h1>👥 Data Pelanggan</h1>

        <a href="../index.php" class="back-btn">
            ← Kembali ke Dashboard
        </a>
    </div>

    <div class="card">

        <div class="info">
            Total pelanggan:
            <strong><?= count($pelanggan); ?></strong>
        </div>

        <?php if (count($pelanggan) > 0): ?>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>Alamat</th>
                        <th>Poin</th>
                        <th>Terdaftar</th>
                    </tr>
                </thead>

                <tbody>

                <?php foreach ($pelanggan as $p): ?>

                    <tr>
                        <td>
                            <?= htmlspecialchars($p["id"]); ?>
                        </td>

                        <td>
                            <strong>
                                <?= htmlspecialchars($p["nama"]); ?>
                            </strong>
                        </td>

                        <td>
                            <?= htmlspecialchars($p["email"]); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($p["no_hp"] ?? "-"); ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($p["alamat"] ?? "-"); ?>
                        </td>

                        <td class="poin">
                            <?= htmlspecialchars($p["poin"]); ?> poin
                        </td>

                        <td>
                            <?= date("d-m-Y", strtotime($p["created_at"])); ?>
                        </td>
                    </tr>

                <?php endforeach; ?>

                </tbody>
            </table>

        <?php else: ?>

            <div class="empty">
                Belum ada pelanggan yang terdaftar.
            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>