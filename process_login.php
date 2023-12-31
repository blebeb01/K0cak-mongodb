<?php
session_start();
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client = new MongoDB\Client("mongodb+srv://naufalm09:pass01@memewebcluster.dbtty1q.mongodb.net/?retryWrites=true&w=majority");
    $collection = $client->meme_sharing->users;

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Find user by username
    $user = $collection->findOne(['username' => $username]);

    if ($user && password_verify($password, $user['password'])) {
        // Login successful, set session variables
        $_SESSION['username'] = $username;
        $_SESSION['name'] = $user['name'];

        // Redirect to home page (index.php)
        header('Location: index.php');
        exit;
    } else {
        // Login failed
        echo "Invalid username or password. Please try again.";
    }
}
?>
