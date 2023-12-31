<?php
session_start();
require 'vendor/autoload.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client = new MongoDB\Client("mongodb+srv://naufalm09:pass01@memewebcluster.dbtty1q.mongodb.net/?retryWrites=true&w=majority");
    $collection = $client->meme_sharing->memes;

    $uploadedImage = $_FILES['image']['tmp_name'];
    $imageData = base64_encode(file_get_contents($uploadedImage));
    $imageType = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);

    $description = $_POST['description'];
    $author = $_SESSION['username'];

    // Insert meme data into the 'memes' collection
    $memeData = [
        'image' => 'data:image/' . $imageType . ';base64,' . $imageData,
        'description' => $description,
        'author' => $author,
        'time' => date("Y-m-d H:i:s"),
    ];

    $collection->insertOne($memeData);
    header('Location: index.php');
        exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Meme</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        header {
            background-color: #343a40;
            padding: 10px 0;
        }

        header ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            text-align: right;
        }

        header li {
            display: inline-block;
            margin-left: 20px;
        }

        header a {
            text-decoration: none;
            color: white;
        }

        main {
            margin: 50px auto;
            width: 50%;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-top: 20px;
        }

        label {
            margin-top: 10px;
            display: block;
        }

        textarea {
            width: 100%;
            height: 100px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="login.php">Login</a></li>
                <li><a href="#">Upload</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <h2>Upload Meme</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label for="image">Select Image:</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*" required>

            <label for="description">Description:</label>
            <textarea class="form-control" id="description" name="description" required></textarea>

            <input type="submit" class="btn btn-primary mt-3" value="Upload">
        </form>
    </main>

    <!-- Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
