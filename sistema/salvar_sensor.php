<?php
session_start();

if (!isset($_SESSION['user_id'])) {
  header('Location: ../index.php');
  exit();
}

require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client as MongoClient;

$mongoClient = new MongoClient("mongodb://localhost:27017");
$database = $mongoClient->pimeioambiente;
$sensoresCollection = $database->sensores;

$nome = $_POST['nome'];

$novoSensor = [
  'nome' => $nome,
  'monitoramentos' => [
    ['tipo' => 'Solo', 'dados' => []],
    ['tipo' => 'Ar', 'dados' => []],
    ['tipo' => 'Temperatura', 'dados' => []]
  ]
];

$insertOneResult = $sensoresCollection->insertOne($novoSensor);

if ($insertOneResult->getInsertedCount() === 1) {
  echo "Sensor adicionado com sucesso!";
} else {
  echo "Erro ao adicionar sensor!";
}
?>
