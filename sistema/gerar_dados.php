<?php
session_start();

require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client as MongoClient;

$mongoClient = new MongoClient("mongodb://localhost:27017");
$database = $mongoClient->pimeioambiente;
$sensoresCollection = $database->sensores;

function gerarDadosAleatorios() {
    return [
        'umidade_solo' => mt_rand(30, 70),
        'temperatura' => mt_rand(18, 28),
        'co2' => mt_rand(200, 600)
    ];
}

$sensores = $sensoresCollection->find();
foreach ($sensores as $sensor) {
    $dadosAleatorios = gerarDadosAleatorios();
    $sensoresCollection->updateOne(
        ['_id' => $sensor['_id']],
        ['$set' => $dadosAleatorios]
    );
}

echo "Dados dos sensores atualizados com sucesso.";
?>
