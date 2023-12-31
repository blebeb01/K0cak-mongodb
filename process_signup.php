<?php
require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client = new MongoDB\Client("mongodb+srv://naufalm09:pass01@memewebcluster.dbtty1q.mongodb.net/?retryWrites=true&w=majority");
    $collection = $client->meme_sharing->users;

    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the username is already taken
    $existingUser = $collection->findOne(['username' => $username]);

    if ($existingUser) {
        // Username already taken
        echo "Username is already taken. Please choose another one.";
    } else {
        // Insert user data into the 'users' collection
        $userData = [
            'name' => $name,
            'username' => $username,
            'password' => $password,
        ];

        $collection->insertOne($userData);
        header('Location: login.php');
        exit;
    }
}
?>
