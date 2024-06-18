<?php
require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client as MongoClient;
use MongoDB\BSON\ObjectId;

// Parâmetros da requisição GET
$action = $_GET['action'] ?? null;

$mongoClient = new MongoClient("mongodb://localhost:27017");
$database = $mongoClient->pimeioambiente;
$sensoresCollection = $database->sensores;

if ($action === 'getSensores') {
    // Obter todos os sensores
    $sensores = $sensoresCollection->find()->toArray();
    header('Content-Type: application/json');
    echo json_encode($sensores);
    exit();
}

$sensorId = $_GET['sensorId'] ?? null;
$tipo = $_GET['tipo'] ?? null;

if (!$sensorId || !$tipo) {
    http_response_code(400);
    die('Parâmetros inválidos.');
}

// Consulta ao MongoDB para encontrar o sensor pelo ID
$sensor = $sensoresCollection->findOne(['_id' => new ObjectId($sensorId)]);

if (!$sensor) {
    http_response_code(404);
    die('Sensor não encontrado.');
}

// Busca os dados do monitoramento pelo tipo especificado
$monitoramento = null;
foreach ($sensor['monitoramentos'] as $mon) {
    if ($mon['tipo'] === $tipo) {
        $monitoramento = $mon;
        break;
    }
}

if (!$monitoramento) {
    http_response_code(404);
    die('Tipo de monitoramento não encontrado para o sensor.');
}

// Prepara os dados para retorno como JSON
$dados = [];
foreach ($monitoramento['dados'] as $dado) {
    $dados[] = [
        'timestamp' => $dado['timestamp']->toDateTime()->format('Y-m-d H:i:s'), // Assumindo que timestamp é um BSON date
        'valor' => $dado['valor'],
    ];
}

// Retorna os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($dados);
?>
