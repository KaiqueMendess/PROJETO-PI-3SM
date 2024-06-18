<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Exibir Sensores</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500&display=swap">
</head>
<body>
  <h1>Sensores Registrados</h1>

  <?php
  require_once __DIR__ . '/../vendor/autoload.php';
  use MongoDB\Client as MongoClient;

  $mongoClient = new MongoClient("mongodb://localhost:27017");
  $database = $mongoClient->pimeioambiente;
  $sensoresCollection = $database->sensores;

  // Consulta todos os sensores
  $sensores = $sensoresCollection->find();

  foreach ($sensores as $sensor) {
    echo "<div class='grafico'>";
    echo "<h2>Monitoramento em Tempo Real: " . htmlspecialchars($sensor['nome']) . "</h2>";
    echo "<canvas id='canvas" . $sensor['_id'] . "' width='400' height='200'></canvas>";
    echo "</div>";
  }
  ?>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      <?php
      foreach ($sensores as $sensor) {
        echo "const ctx" . $sensor['_id'] . " = document.getElementById('canvas" . $sensor['_id'] . "').getContext('2d');";
        echo "const chart" . $sensor['_id'] . " = new Chart(ctx" . $sensor['_id'] . ", {";
        echo "type: 'line',";
        echo "data: {";
        echo "labels: [],";
        echo "datasets: [";
        echo "{";
        echo "label: 'Sensor de " . htmlspecialchars($sensor['nome']) . "',";
        echo "data: [],";
        echo "fill: false,";
        echo "borderColor: 'rgb(75, 192, 192)',";
        echo "tension: 0.1";
        echo "}";
        echo "]";
        echo "},";
        echo "options: {";
        echo "responsive: true,";
        echo "maintainAspectRatio: false,";
        echo "scales: {";
        echo "x: {";
        echo "type: 'time',";
        echo "time: {";
        echo "unit: 'minute'";
        echo "}";
        echo "},";
        echo "y: {";
        echo "beginAtZero: true";
        echo "}";
        echo "}";
        echo "}";
        echo "});";

        // Consulta ao PHP para cada tipo de monitoramento do sensor
        foreach ($sensor['monitoramentos'] as $monitoramento) {
          echo "fetchDadosSensor('" . $sensor['_id'] . "', '" . $monitoramento['tipo'] . "');";
        }
      }
      ?>

      // Função para buscar dados do sensor do PHP e atualizar o gráfico
      async function fetchDadosSensor(sensorId, tipo) {
        const response = await fetch(`dadosSensor.php?sensorId=${sensorId}&tipo=${tipo}`);
        const dados = await response.json();

        const chart = window['chart' + sensorId]; // Obtém o objeto Chart.js pelo ID do sensor
        dados.forEach(dado => {
          chart.data.labels.push(dado.timestamp);
          chart.data.datasets[0].data.push(dado.valor);
        });
        chart.update();
      }
    });
  </script>
</body>
</html>
