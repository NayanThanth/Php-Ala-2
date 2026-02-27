<?php
session_start();

/* Database Connection */
$conn = mysqli_connect("localhost", "root", "", "image_gallery");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

/* Upload Logic */
if (isset($_POST['submit'])) {

    $image_name = $_POST['image_name'];
    $file = $_FILES['image'];

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = array('jpg', 'jpeg', 'png', 'gif');

    if (in_array($fileExt, $allowed)) {

        if ($fileError === 0) {

            if ($fileSize < 5 * 1024 * 1024) { // 5MB limit

                $newFileName = time() . "_" . $fileName;
                $uploadPath = "uploads/" . $newFileName;

                move_uploaded_file($fileTmpName, $uploadPath);

                $sql = "INSERT INTO images (image_name, file) 
                        VALUES ('$image_name', '$newFileName')";
                mysqli_query($conn, $sql);

                $_SESSION['message'] = "Image uploaded successfully!";
                header("Location: gallery.php");
                exit();

            } else {
                $_SESSION['message'] = "File size must be under 5MB.";
            }

        } else {
            $_SESSION['message'] = "Error uploading file.";
        }

    } else {
        $_SESSION['message'] = "Only JPG, JPEG, PNG, GIF allowed.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Image Upload and Gallery</title>
</head>
<body>

<h2>Upload Image</h2>

<form method="POST" enctype="multipart/form-data">
    Image Name:
    <input type="text" name="image_name" required><br><br>

    Select Image:
    <input type="file" name="image" required><br><br>

    <input type="submit" name="submit" value="Upload">
</form>

<?php
if (isset($_SESSION['message'])) {
    echo "<p>" . $_SESSION['message'] . "</p>";
    unset($_SESSION['message']);
}
?>

<hr>
<h2>Image Gallery</h2>

<?php
$result = mysqli_query($conn, "SELECT * FROM images");

while ($row = mysqli_fetch_assoc($result)) {
    echo "<div style='display:inline-block; margin:10px; text-align:center;'>";
    echo "<img src='uploads/".$row['file']."' width='200' height='200'><br>";
    echo "<p>".$row['image_name']."</p>";
    echo "</div>";
}
?>

</body>
</html>