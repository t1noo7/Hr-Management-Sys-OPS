<?php

require_once('../process/dbh.php');
$toast = "";

if (isset($_POST['submit'])) {

    $title = $_POST['title'];

    $file = $_FILES['image'];
    $filename = time() . "_" . basename($file['name']);
    $targetDir = "../uploads/banners/";
    $savePath = $targetDir . $filename;

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    list($w, $h) = getimagesize($file['tmp_name']);
    $maxW = 1200;

    if ($w > $maxW) {
        $ratio = $maxW / $w;
        $newW = $maxW;
        $newH = $h * $ratio;

        $src = imagecreatefromstring(file_get_contents($file['tmp_name']));
        $dst = imagecreatetruecolor($newW, $newH);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $w, $h);
        imagejpeg($dst, $savePath, 90);

        imagedestroy($src);
        imagedestroy($dst);
    } else {
        move_uploaded_file($file['tmp_name'], $savePath);
    }

    $dbPath = "uploads/banners/" . $filename;
    mysqli_query($conn, "INSERT INTO banners (banner_title, image_url) VALUES ('$title', '$dbPath')");

    $toast = "Banner đã được thêm thành công!";
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Add Banner</title>
    <style>
        .btnBack {
            position: absolute;
            right: 20px;
            top: 20px;
            background: #007bff;
            color: #fff;
            padding: 10px 18px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }

        .btnBack:hover {
            background: #0056c7;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.25);
        }

        .preview {
            width: 300px;
            margin-top: 15px;
            border: 1px solid #ccc;
            display: none;
        }

        /* Toast */
        #toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 6px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transition: 0.4s;
            pointer-events: none;
        }

        #toast.show {
            opacity: 1;
        }
    </style>
</head>

<body>

    <a href="banner_list.php" class="btnBack">← Back</a>

    <?php if ($toast != ""): ?>
        <div id="toast"><?= $toast ?></div>
        <script>
            setTimeout(() => {
                document.getElementById("toast").classList.add("show");
            }, 100);
            setTimeout(() => {
                document.getElementById("toast").classList.remove("show");
            }, 3000);
        </script>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" style="width:400px;margin:80px auto;">
        <h3>Add Banner</h3>

        <label>Title</label><br>
        <input type="text" name="title" style="width:100%"><br><br>

        <label>Banner Image</label><br>
        <input type="file" name="image" accept="image/*" id="imgInput"><br>

        <img id="preview" class="preview">

        <br><br>

        <button type="submit" name="submit">Add</button>
    </form>

    <script>
        document.getElementById('imgInput').addEventListener('change', function (e) {
            const img = document.getElementById('preview');
            img.src = URL.createObjectURL(e.target.files[0]);
            img.style.display = 'block';
        });
    </script>

</body>

</html>