<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401); // Não autorizado
    exit();
}

require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client as MongoClient;

$mongoClient = new MongoClient("mongodb://localhost:27017");
$database = $mongoClient->pimeioambiente;
$sensoresCollection = $database->sensores;

$userId = $_SESSION['user_id'];

try {
    $userId = new MongoDB\BSON\ObjectId($userId);
} catch (InvalidArgumentException $e) {
    http_response_code(400); // Requisição inválida
    exit();
}

$sensores = $sensoresCollection->find(['user_id' => $userId]);

$sensoresArray = iterator_to_array($sensores); // Converte o cursor para um array

header('Content-Type: application/json');
echo json_encode($sensoresArray);
?>
