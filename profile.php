<?php
session_start();
require 'vendor/autoload.php';

// Fungsi untuk mengganti nama pengguna di database
function changeName($newName) {
    global $collection, $loggedInUsername;

    // Gunakan operator `$set` untuk melakukan operasi update
    $result = $collection->updateOne(
        ['username' => $loggedInUsername],
        ['$set' => ['name' => $newName]]
    );

    return $result->getModifiedCount() > 0;
}

// Fungsi untuk menghapus akun pengguna dari database
function deleteAccount() {
    global $collection, $loggedInUsername;

    // Implementasikan logika penghapusan akun dari database di sini
    $result = $collection->deleteOne(['username' => $loggedInUsername]);

    return $result->getDeletedCount() > 0;
}

// Kode MongoDB
$client = new MongoDB\Client("mongodb+srv://naufalm09:pass01@memewebcluster.dbtty1q.mongodb.net/?retryWrites=true&w=majority");
$collection = $client->meme_sharing->users;

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Ambil informasi pengguna dari sesi
$loggedInUsername = $_SESSION['username'];

// Handle POST request untuk mengganti nama
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newName'])) {
    $newName = $_POST['newName'];
    if (changeName($newName)) {
        // Perbarui nama di sesi setelah berhasil diubah di database
        $_SESSION['name'] = $newName;

        echo "Nama berhasil diubah.";
        // Redirect untuk mencegah resubmission form saat refresh
        header("Location: profile.php");
        exit;
    } else {
        echo "Gagal mengganti nama.";
    }
}

// Handle POST request untuk menghapus akun
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deleteAccount'])) {
    if (deleteAccount()) {
        // Hancurkan sesi
        session_destroy();

        // Redirect ke halaman home setelah menghapus akun
        header("Location: index.php");
        exit;
    } else {
        echo "Gagal menghapus akun.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <!-- Style -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="profile.php">Profile</a></li>
                <?php
                if (isset($_SESSION['username'])) {
                    echo '<li class="nav-item dropdown">';
                    echo '<a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
                    echo $_SESSION['username'];
                    echo '</a>';
                    echo '<div class="dropdown-menu" aria-labelledby="navbarDropdown">';
                    echo '<a class="dropdown-item" href="logout.php?logout=true">Logout</a>';
                    echo '</div>';
                    echo '</li>';
                } else {
                    echo '<li><a href="login.php">Login</a></li>';
                }
                ?>
                <li><a href="upload.php">Upload</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <h2 class="mb-4">User Profile</h2>
        <div class="profile-info">
            <p><strong>Name:</strong> <?php echo $_SESSION['name']; ?></p>
            <p><strong>Username:</strong> <?php echo $_SESSION['username']; ?></p>
        </div>

        <span class="btn-link mt-4" style="cursor: pointer;" onclick="toggleChangeNameForm()">Change name</span>

        <div class="change-name-form mt-4" id="changeNameForm" style="display: none;">
            <h4>Change Your Name</h4>
            <form method="post" action="">
                <div class="form-group">
                    <label for="newName">New Name:</label>
                    <input type="text" class="form-control" id="newName" name="newName" required>
                </div>
                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>

        <div class="delete-account mt-4">
            <span class="btn-link" style="cursor: pointer; color: red;" onclick="confirmDelete()">Delete Account</span>
        </div>

        <div id="deleteAccountConfirmation" style="display: none;">
            <h4>Are you sure you want to delete your account?</h4>
            <form method="post" action="">
                <button type="submit" class="btn btn-danger" name="deleteAccount">Yes, delete it</button>
                <button type="button" class="btn btn-secondary" onclick="cancelDelete()">Cancel</button>
            </form>
        </div>
    </main>

    <!-- Bootstrap JS dan Popper.js -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <script>
        function toggleChangeNameForm() {
            var changeNameForm = document.getElementById('changeNameForm');
            changeNameForm.style.display = (changeNameForm.style.display === 'none') ? 'block' : 'none';
        }

        function confirmDelete() {
            var deleteConfirmation = document.getElementById('deleteAccountConfirmation');
            deleteConfirmation.style.display = 'block';
        }

        function cancelDelete() {
            var deleteConfirmation = document.getElementById('deleteAccountConfirmation');
            deleteConfirmation.style.display = 'none';
        }
    </script>
</body>
</html>
