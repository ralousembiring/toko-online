<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../config/database.php";
// Pastikan pelanggan sudah login
if (!isset($_SESSION["pelanggan_id"])) {
    header("Location: login.php");
    exit;
}

$pelanggan_id = $_SESSION["pelanggan_id"];

// Ambil data pelanggan
$stmt = $pdo->prepare("
    SELECT nama
    FROM pelanggan
    WHERE id = ?
");

$stmt->execute([$pelanggan_id]);

$pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);


// Kirim pesan
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $pesan = trim($_POST["pesan"] ?? "");

    if ($pesan !== "") {

        $stmt = $pdo->prepare("
            INSERT INTO chat_messages
            (pelanggan_id, pengirim, pesan)
            VALUES (?, 'pelanggan', ?)
        ");

        $stmt->execute([
            $pelanggan_id,
            $pesan
        ]);

        header("Location: chat.php");
        exit;
    }
}


// Ambil semua pesan pelanggan
$stmt = $pdo->prepare("
    SELECT *
    FROM chat_messages
    WHERE pelanggan_id = ?
    ORDER BY created_at ASC, id ASC
");

$stmt->execute([$pelanggan_id]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Chat Admin</title>

    <style>

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 500px;
            max-width: 90%;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: #222;
            color: white;
            padding: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .chat-box {
            height: 450px;
            overflow-y: auto;
            padding: 20px;
            background: #f7f7f7;
        }

        .message {
            margin-bottom: 15px;
            display: flex;
        }

        .message.pelanggan {
            justify-content: flex-end;
        }

        .message.admin {
            justify-content: flex-start;
        }

        .bubble {
            max-width: 70%;
            padding: 10px 15px;
            border-radius: 15px;
        }

        .pelanggan .bubble {
            background: #222;
            color: white;
        }

        .admin .bubble {
            background: #ddd;
            color: #222;
        }

        .time {
            font-size: 11px;
            margin-top: 5px;
            opacity: 0.7;
        }

        .form {
            display: flex;
            padding: 15px;
            border-top: 1px solid #ddd;
            gap: 10px;
        }

        .form input {
            flex: 1;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 20px;
        }

        .form button {
            padding: 12px 20px;
            border: none;
            border-radius: 20px;
            background: #222;
            color: white;
            cursor: pointer;
        }

        .form button:hover {
            background: #444;
        }

    </style>

</head>

<body>

<div class="container">

    <div class="header">

        <h2>💬 Chat dengan Admin</h2>

        <p>
            Halo, <?= htmlspecialchars($pelanggan["nama"]) ?>
        </p>

    </div>


    <div class="chat-box" id="chatBox">

        <?php if (empty($messages)): ?>

            <p style="text-align:center;color:#888;">
                Belum ada pesan.
            </p>

        <?php else: ?>

            <?php foreach ($messages as $message): ?>

                <div class="message <?= $message["pengirim"] ?>">

                    <div class="bubble">

                        <?= nl2br(htmlspecialchars($message["pesan"])) ?>

                        <div class="time">

                            <?= htmlspecialchars($message["created_at"]) ?>

                        </div>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>


    <form method="POST" class="form">

        <input
            type="text"
            name="pesan"
            placeholder="Tulis pesan..."
            autocomplete="off"
            required
        >

        <button type="submit">
            Kirim
        </button>

    </form>

</div>


<script>

    const chatBox = document.getElementById("chatBox");

    chatBox.scrollTop = chatBox.scrollHeight;

</script>

</body>

</html>