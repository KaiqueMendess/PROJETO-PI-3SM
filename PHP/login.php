<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php'; // Certifique-se de que o autoloader do Composer está sendo incluído

use MongoDB\Client as MongoClient;

// Conexão com o banco de dados MongoDB
$client = new MongoClient("mongodb://localhost:27017");
$database = $client->pimeioambiente; // Nome do banco de dados
$collection = $database->users; // Nome da coleção

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $collection->findOne(['email' => $email]);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['_id']; // MongoDB usa '_id' ao invés de 'id'
        header('Location: ../sistema/home.php');
        exit();
    } else {
        echo "Email ou senha incorretos!";
    }
}
?>
