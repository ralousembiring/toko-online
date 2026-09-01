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
// AMBIL ID PRODUK
// ========================================

$id = (int) ($_GET["id"] ?? 0);


if ($id <= 0) {

    die("ID produk tidak valid.");

}


// ========================================
// AMBIL DATA PRODUK
// ========================================

$stmt = $pdo->prepare(
    "SELECT * FROM produk WHERE id = ?"
);

$stmt->execute([$id]);

$produk = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$produk) {

    die("Produk tidak ditemukan.");

}


// ========================================
// VARIABEL
// ========================================

$pesan = "";

$berhasil = false;


// ========================================
// PROSES UPDATE
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $nama = trim(
        $_POST["nama"] ?? ""
    );


    $harga = (float) (
        $_POST["harga"] ?? 0
    );


    $stok = (int) (
        $_POST["stok"] ?? 0
    );


    $deskripsi = trim(
        $_POST["deskripsi"] ?? ""
    );


    // ====================================
    // VALIDASI
    // ====================================

    if ($nama === "") {

        $pesan =
            "Nama produk wajib diisi.";

    }

    elseif ($harga <= 0) {

        $pesan =
            "Harga harus lebih dari 0.";

    }

    elseif ($stok < 0) {

        $pesan =
            "Stok tidak boleh negatif.";

    }

    else {


        // =================================
        // GAMBAR LAMA
        // =================================

        $gambar =
            $produk["gambar"] ?? null;


        // =================================
        // CEK GAMBAR BARU
        // =================================

        if (
            isset($_FILES["gambar"]) &&
            $_FILES["gambar"]["error"]
                === UPLOAD_ERR_OK
        ) {


            // Format yang diperbolehkan

            $allowed = [

                "image/jpeg" => "jpg",

                "image/png" => "png",

                "image/webp" => "webp"

            ];


            // Cek MIME

            $mime = mime_content_type(
                $_FILES["gambar"]["tmp_name"]
            );


            if (!isset($allowed[$mime])) {

                $pesan =
                    "Format gambar harus JPG, PNG, atau WEBP.";

            }


            // Cek ukuran

            elseif (
                $_FILES["gambar"]["size"]
                > 2 * 1024 * 1024
            ) {

                $pesan =
                    "Ukuran gambar maksimal 2 MB.";

            }


            else {


                // =========================
                // NAMA FILE BARU
                // =========================

                $extension =
                    $allowed[$mime];


                $filename =
                    uniqid(
                        "produk_",
                        true
                    )
                    . "."
                    . $extension;


                $destination =
                    "../../uploads/"
                    . $filename;


                // =========================
                // UPLOAD
                // =========================

                if (
                    move_uploaded_file(
                        $_FILES["gambar"]["tmp_name"],
                        $destination
                    )
                ) {


                    // =====================
                    // HAPUS GAMBAR LAMA
                    // =====================

                    if (
                        !empty($produk["gambar"])
                    ) {


                        $oldFile =
                            "../../uploads/"
                            . $produk["gambar"];


                        if (
                            file_exists($oldFile)
                        ) {

                            unlink($oldFile);

                        }

                    }


                    $gambar =
                        $filename;

                }

                else {

                    $pesan =
                        "Gagal mengupload gambar.";

                }

            }

        }


        // =================================
        // UPDATE DATABASE
        // =================================

        if ($pesan === "") {


            $sql = "

                UPDATE produk

                SET

                    nama = ?,

                    harga = ?,

                    stok = ?,

                    deskripsi = ?,

                    gambar = ?

                WHERE id = ?

            ";


            $stmtUpdate =
                $pdo->prepare($sql);


            $stmtUpdate->execute([

                $nama,

                $harga,

                $stok,

                $deskripsi,

                $gambar,

                $id

            ]);


            // Update data yang ditampilkan

            $produk["nama"] =
                $nama;


            $produk["harga"] =
                $harga;


            $produk["stok"] =
                $stok;


            $produk["deskripsi"] =
                $deskripsi;


            $produk["gambar"] =
                $gambar;


            $berhasil = true;

        }

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
        Edit Produk - Admin
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

            max-width: 700px;

            margin: 40px auto;

            padding: 30px;

            background: white;

            border-radius: 10px;

            box-shadow:
                0 3px 10px
                rgba(0,0,0,0.08);

        }


        h1 {

            margin-top: 0;

        }


        .form-group {

            margin-bottom: 20px;

        }


        label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;

        }


        input,
        textarea {

            width: 100%;

            padding: 12px;

            border: 1px solid #ccc;

            border-radius: 6px;

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


        .current-image {

            margin-top: 10px;

            margin-bottom: 15px;

        }


        .current-image img {

            width: 180px;

            height: 180px;

            object-fit: cover;

            border-radius: 8px;

            border: 1px solid #ddd;

        }


        .no-image {

            width: 180px;

            height: 180px;

            background: #e5e7eb;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 8px;

            font-size: 50px;

        }


        .info {

            color: #6b7280;

            font-size: 13px;

            margin-top: 7px;

        }


        .success {

            background: #dcfce7;

            color: #166534;

            padding: 15px;

            border-radius: 6px;

            margin-bottom: 20px;

        }


        .error {

            background: #fee2e2;

            color: #991b1b;

            padding: 15px;

            border-radius: 6px;

            margin-bottom: 20px;

        }


        .btn {

            display: inline-block;

            padding: 12px 20px;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            text-decoration: none;

            font-size: 15px;

        }


        .btn-primary {

            background: #2563eb;

            color: white;

        }


        .btn-secondary {

            background: #6b7280;

            color: white;

            margin-left: 8px;

        }

    </style>

</head>


<body>


<!-- ========================================
     NAVBAR
======================================== -->

<div class="navbar">

    <h2>
        ⚙️ Admin Toko Online
    </h2>


    <a href="index.php">
        ← Kembali ke Produk
    </a>

</div>



<!-- ========================================
     FORM EDIT
======================================== -->

<div class="container">


    <h1>
        ✏️ Edit Produk
    </h1>


    <?php if ($berhasil): ?>

        <div class="success">

            Produk berhasil diperbarui! 🎉

        </div>

    <?php endif; ?>


    <?php if ($pesan !== ""): ?>

        <div class="error">

            <?= htmlspecialchars($pesan) ?>

        </div>

    <?php endif; ?>



    <form
        method="POST"
        enctype="multipart/form-data"
    >


        <!-- NAMA -->

        <div class="form-group">

            <label for="nama">

                Nama Produk

            </label>


            <input
                type="text"
                id="nama"
                name="nama"
                value="<?= htmlspecialchars(
                    $produk["nama"]
                ) ?>"
                required
            >

        </div>



        <!-- HARGA -->

        <div class="form-group">

            <label for="harga">

                Harga

            </label>


            <input
                type="number"
                id="harga"
                name="harga"
                value="<?= $produk["harga"] ?>"
                min="1"
                required
            >

        </div>



        <!-- STOK -->

        <div class="form-group">

            <label for="stok">

                Stok

            </label>


            <input
                type="number"
                id="stok"
                name="stok"
                value="<?= $produk["stok"] ?>"
                min="0"
                required
            >

        </div>



        <!-- DESKRIPSI -->

        <div class="form-group">

            <label for="deskripsi">

                Deskripsi

            </label>


            <textarea
                id="deskripsi"
                name="deskripsi"
            ><?= htmlspecialchars(
                $produk["deskripsi"]
            ) ?></textarea>

        </div>



        <!-- GAMBAR LAMA -->

        <div class="form-group">

            <label>
                Gambar Saat Ini
            </label>


            <div class="current-image">


                <?php if (
                    !empty($produk["gambar"])
                ): ?>


                    <img
                        src="../../uploads/<?= htmlspecialchars(
                            $produk["gambar"]
                        ) ?>"
                        alt="Gambar produk"
                    >


                <?php else: ?>


                    <div class="no-image">

                        🌸

                    </div>


                <?php endif; ?>


            </div>

        </div>



        <!-- GAMBAR BARU -->

        <div class="form-group">

            <label for="gambar">

                Ganti Gambar

            </label>


            <input
                type="file"
                id="gambar"
                name="gambar"
                accept="image/jpeg,image/png,image/webp"
            >


            <div class="info">

                Kosongkan jika tidak ingin
                mengganti gambar.

                <br>

                Format JPG, PNG, WEBP.
                Maksimal 2 MB.

            </div>

        </div>



        <!-- BUTTON -->

        <button
            type="submit"
            class="btn btn-primary"
        >

            💾 Simpan Perubahan

        </button>


        <a
            href="index.php"
            class="btn btn-secondary"
        >

            Batal

        </a>


    </form>


</div>


</body>

</html>