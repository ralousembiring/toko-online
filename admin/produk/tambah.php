<?php

session_start();

require_once "../../config/database.php";


// ========================================
// CEK LOGIN ADMIN
// ========================================

if (!isset($_SESSION["admin_id"])) {

    header("Location: ../login.php");

    exit();

}


// ========================================
// PROSES TAMBAH PRODUK
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nama = trim($_POST["nama"] ?? "");

    $harga = (float) ($_POST["harga"] ?? 0);

    $stok = (int) ($_POST["stok"] ?? 0);

    $deskripsi = trim(
        $_POST["deskripsi"] ?? ""
    );


    // ====================================
    // VALIDASI
    // ====================================

    if ($nama === "") {

        die("Nama produk wajib diisi.");

    }


    if ($harga <= 0) {

        die("Harga produk tidak valid.");

    }


    if ($stok < 0) {

        die("Stok tidak valid.");

    }


    // ====================================
    // PROSES GAMBAR
    // ====================================

    $gambar = null;


    if (
        isset($_FILES["gambar"]) &&
        $_FILES["gambar"]["error"] === UPLOAD_ERR_OK
    ) {


        $allowed = [
            "image/jpeg" => "jpg",
            "image/png"  => "png",
            "image/webp" => "webp"
        ];


        // Cek tipe file
        $mime = mime_content_type(
            $_FILES["gambar"]["tmp_name"]
        );


        if (!isset($allowed[$mime])) {

            die(
                "Format gambar harus JPG, PNG, atau WEBP."
            );

        }


        // Cek ukuran maksimal 2 MB
        if (
            $_FILES["gambar"]["size"]
            > 2 * 1024 * 1024
        ) {

            die(
                "Ukuran gambar maksimal 2 MB."
            );

        }


        // Buat nama file unik
        $extension = $allowed[$mime];


        $filename =
            uniqid("produk_", true)
            . "."
            . $extension;


        // Lokasi penyimpanan
        $destination =
            "../../uploads/"
            . $filename;


        // Pindahkan file
        if (
            !move_uploaded_file(
                $_FILES["gambar"]["tmp_name"],
                $destination
            )
        ) {

            die(
                "Gagal mengupload gambar."
            );

        }


        $gambar = $filename;

    }


    // ====================================
    // SIMPAN KE DATABASE
    // ====================================

    $stmt = $pdo->prepare("
        INSERT INTO produk
        (
            nama,
            harga,
            stok,
            deskripsi,
            gambar
        )
        VALUES (?, ?, ?, ?, ?)
    ");


    $stmt->execute([
        $nama,
        $harga,
        $stok,
        $deskripsi,
        $gambar
    ]);


    // ====================================
    // KEMBALI KE DAFTAR PRODUK
    // ====================================

    header(
        "Location: index.php"
    );

    exit();

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
        Tambah Produk
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


        .container {

            max-width: 700px;

            margin: 40px auto;

            padding: 20px;

        }


        .box {

            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px
                rgba(0,0,0,0.06);

        }


        h1 {

            margin-top: 0;

        }


        label {

            display: block;

            margin-top: 18px;

            margin-bottom: 7px;

            font-weight: bold;

        }


        input,
        textarea {

            width: 100%;

            padding: 11px;

            border: 1px solid #d1d5db;

            border-radius: 7px;

            font-size: 15px;

        }


        textarea {

            min-height: 120px;

            resize: vertical;

        }


        input[type="file"] {

            padding: 9px;

            background: #f9fafb;

        }


        .buttons {

            margin-top: 25px;

            display: flex;

            gap: 10px;

        }


        button,
        .back {

            padding: 11px 18px;

            border-radius: 7px;

            border: none;

            cursor: pointer;

            text-decoration: none;

            font-size: 15px;

        }


        button {

            background: #2563eb;

            color: white;

        }


        .back {

            background: #6b7280;

            color: white;

        }


        .info {

            margin-top: 8px;

            color: #6b7280;

            font-size: 13px;

        }

    </style>

</head>


<body>


<div class="container">


    <div class="box">


        <h1>
            ➕ Tambah Produk
        </h1>


        <form
            method="POST"
            enctype="multipart/form-data"
        >


            <!-- NAMA -->

            <label>
                Nama Produk
            </label>

            <input
                type="text"
                name="nama"
                placeholder="Contoh: Bunga Mawar"
                required
            >


            <!-- HARGA -->

            <label>
                Harga
            </label>

            <input
                type="number"
                name="harga"
                placeholder="50000"
                min="1"
                required
            >


            <!-- STOK -->

            <label>
                Stok
            </label>

            <input
                type="number"
                name="stok"
                placeholder="10"
                min="0"
                required
            >


            <!-- DESKRIPSI -->

            <label>
                Deskripsi
            </label>

            <textarea
                name="deskripsi"
                placeholder="Deskripsi produk..."
            ></textarea>


            <!-- GAMBAR -->

            <label>
                Gambar Produk
            </label>

            <input
                type="file"
                name="gambar"
                accept="image/jpeg,image/png,image/webp"
            >


            <div class="info">

                Format:
                JPG, PNG, atau WEBP.
                Maksimal 2 MB.

            </div>


            <!-- BUTTON -->

            <div class="buttons">

                <a
                    href="index.php"
                    class="back"
                >
                    ← Kembali
                </a>


                <button type="submit">

                    💾 Simpan Produk

                </button>

            </div>


        </form>


    </div>


</div>


</body>

</html>