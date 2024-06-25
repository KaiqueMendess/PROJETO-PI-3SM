<?php
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client as MongoClient;

$mongoClient = new MongoClient("mongodb://localhost:27017");
$database = $mongoClient->pimeioambiente;
$sensoresCollection = $database->sensores;

$sensorId = $_GET['id'];
$sensor = $sensoresCollection->findOne(['_id' => new MongoDB\BSON\ObjectId($sensorId)]);

echo json_encode($sensor);
?>
