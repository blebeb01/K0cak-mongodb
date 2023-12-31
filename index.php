<?php
session_start();
require 'vendor/autoload.php';

$client = new MongoDB\Client("mongodb+srv://naufalm09:pass01@memewebcluster.dbtty1q.mongodb.net/?retryWrites=true&w=majority");

$collection = $client->meme_sharing->memes;

// Mengambil meme dari collection
$memes = $collection->find([], ['sort' => ['time' => -1]]);
// Fungsi untuk menghapus gambar
function deleteMeme($memeId) {
    global $collection;
    $result = $collection->deleteOne(['_id' => new MongoDB\BSON\ObjectId($memeId)]);
    return $result->getDeletedCount();
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $memeIdToDelete = $_POST['delete'];
    $deletedCount = deleteMeme($memeIdToDelete);
    if ($deletedCount > 0) {
        echo "Meme deleted successfully.";
        // Redirect to prevent form resubmission on page refresh
        header("Location: index.php");
        exit;
    } else {
        echo "Failed to delete meme.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meme Sharing</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Style -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="#">Home</a></li>
                
                <?php
                if (isset($_SESSION['username'])) {
                    // Jika sudah login, tampilkan dropdown menu dengan nama pengguna
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    echo $_SESSION['username'];
                    echo '</a>';
                    echo '<div class="dropdown-menu" aria-labelledby="navbarDropdown">';
                    echo '<a class="dropdown-item" href="profile.php">Profile</a>';
                    echo '<a class="dropdown-item" href="logout.php?logout=true">Logout</a>';
                    echo '</div>';
                    echo '</li>';
                } else {
                    // Jika belum login, tampilkan tombol login
                    echo '<li><a href="login.php">Login</a></li>';
                }
                ?>
                
                <li><a href="upload.php">Upload</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2 class="mb-4">K0cak</h2>

        <?php foreach ($memes as $meme): ?>
            <div class="meme-card">
                <img src="<?php echo $meme['image']; ?>" alt="Meme">

                <?php
                // Tampilkan tombol 'option' dan dropdown hanya jika author sama dengan pengguna yang sudah login
                if (isset($_SESSION['username']) && $meme['author'] === $_SESSION['username']) {
                    echo '<div class="post-dropdown">';
                    echo '<button class="btn btn-secondary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    echo 'Option';
                    echo '</button>';
                    echo '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
                    echo '<form method="post">';
                    echo '<input type="hidden" name="delete" value="' . $meme['_id'] . '">';
                    echo '<button type="submit" class="dropdown-item" onclick="return confirm(\'Are you sure?\')">Delete</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }
                ?>
                
                <div class="card-body">
                    <p class="card-text">Uploaded by: <?php echo $meme['author']; ?></p>
                    <!-- <p class="card-text">Time: <?php echo $meme['time']; ?></p> -->
                    <p class="card-text"><?php echo $meme['description']; ?></p>
                    <p class="card-text time"><?php echo $meme['time']; ?></p>
                </div>

                
            </div>
        <?php endforeach; ?>
    </main>

    <!-- Bootstrap JS dan Popper.js -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
