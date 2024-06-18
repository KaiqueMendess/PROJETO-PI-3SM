<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#06b6d4">
  <link rel="stylesheet" href="sys.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <title>Painel de administração</title>
</head>
<body>
<?php
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Conexão com o MongoDB e consulta ao banco de dados
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client as MongoClient;

$mongoClient = new MongoClient("mongodb://localhost:27017");
$database = $mongoClient->pimeioambiente;
$usersCollection = $database->users;

// Obtém o _id do usuário da sessão
$userId = $_SESSION['user_id'];

// Tentar converter $userId para ObjectId do MongoDB
try {
    $userId = new MongoDB\BSON\ObjectId($userId);
} catch (InvalidArgumentException $e) {
    // Se houver erro na conversão, redirecionar para o login
    header('Location: ./login.php');
    exit();
}

// Consultar o usuário pelo _id
$user = $usersCollection->findOne(['_id' => $userId]);

if (!$user) {
    // Se o usuário não for encontrado, redirecionar para o login
    header('Location: ./login.php');
    exit();
}
?>

<div id="content">
  <header>
    <div class="busca">
      <form action="">
        <input type="text" placeholder="Pesquisar">
        <button type="submit" title="Buscar">
          <i class="bi bi-search"></i>
        </button>
      </form>
      <button title="Notificações"><i class="bi bi-bell"></i></button>
      <div class="perfil">
        <img src="img/perfil/<?php echo $user['avatar']; ?>" alt="Sua foto de perfil">
        <p><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
      </div>
    </div>
    <div class="saudacao">
      <div class="perfil">
        <img src="https://images.emojiterra.com/google/noto-emoji/unicode-15/animated/1f44b.gif" alt="Sua foto de perfil">
        <span></span>
        <p>Olá, <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></p>
      </div>

      <div class="acoes">
        <button>Novo</button>
        <button>Enviar</button>
        <button>Compartilhar</button>
      </div>
    </div>
  </header>
  <aside>
    <div class="logo">
      <i class="bi bi-speedometer2"></i>
      <h1>Painel</h1>
    </div>
    <ul class="menu">
      <li class="selecionado">
        <a href="#"><i class="bi bi-house"></i> Início</a>
      </li>
      <li>
        <a href="#"><i class="bi bi-person"></i> Perfil</a>
      </li>
      <li>
        <a href="addsensor.php"><i class="bi bi-chat"></i>Adicionar Sensor</a>
      </li>
      <li>
        <a href="#"><i class="bi bi-chat"></i>Monitoramento de AR</a>
      </li>
      <li>
        <a href="#"><i class="bi bi-clock-history"></i>Monitoramento do Solo</a>
      </li>
      <li>
        <a href="#"><i class="bi bi-journals"></i>Monitoramento De Temperatura</a>
      </li>
      <li>
        <a href="#"><i class="bi bi-people"></i>Relatórios</a>
      </li>
    </ul>
    <ul class="menu">
      <li>
        <a href="#"><i class="bi bi-gear"></i> Ajustes</a>
      </li>
      <li>
        <a href="#"><i class="bi bi-info-circle"></i> Ajuda</a>
      </li>
      <li>
        <a href="#"><i class="bi bi-shield-check"></i> Privacidade</a>
      </li>
      <li>
        <a href="../PHP/logout.php"><i class="bi bi-shield-check"></i> Sair</a>
      </li>
    </ul>
    <footer>
      <p id="texto-footer">Clean Earth, <?php echo date('Y'); ?></p>
      <div class="links">
      </div>
    </footer>
  </aside>
  <main>
  <h1>Adicionar Novo Sensor</h1>
  <form action="salvar_sensor.php" method="POST">
    <label for="nome">Nome do Sensor:</label>
    <input type="text" id="nome" name="nome" required>
    <button type="submit">Adicionar Sensor</button>
  </form>


    <section class="anuncios">
      <h2>Anúncios</h2>
      <div class="card">
        <div>
          <h4>Manutenção do site</h4>
          <p>O Site passa por manutenções rotineiras todos os dias em horários que não afetem o seu uso.</p>
        </div>
        <div>
          <h4>Novidades</h4>
          <p>Agora foi implementada a função de perfis editáveis. Não deixe de conferir!</p>
        </div>
        <div>
          <h4>Política de privacidade atualizada</h4>
          <p>Nossa política de privacidade foi atualizada, não deixe de dar uma olhada mais tarde!</p>
        </div>
      </div>
    </section>
  </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
  const footer = document.getElementById("texto-footer");
  footer.innerText = `Clear Earth, ${new Date().getFullYear()}`;

 // Array para armazenar objetos de gráfico
let graficos = [];

// Função para adicionar um novo sensor
function adicionarSensor(nome, tipo) {
  // Criar elemento de gráfico para o novo sensor
  const novoGrafico = document.createElement('div');
  novoGrafico.classList.add('grafico');

  // Criar título do sensor
  const titulo = document.createElement('h3');
  titulo.textContent = nome;
  novoGrafico.appendChild(titulo);

  // Criar canvas para o gráfico
  const canvas = document.createElement('canvas');
  canvas.width = 200;
  canvas.height = 200;
  novoGrafico.appendChild(canvas);

  // Adicionar novo gráfico ao container
  const graficosContainer = document.getElementById('graficosContainer');
  graficosContainer.appendChild(novoGrafico);

  // Inicializar gráfico com Chart.js
  const ctx = canvas.getContext('2d');
  const chart = new Chart(ctx, {
    type: 'pie', // Tipo de gráfico inicial (pode ser ajustado conforme necessário)
    data: {
      labels: [nome, 'Outros'],
      datasets: [{
        data: [0, 100],
        backgroundColor: ['#007bff', '#dee2e6'],
        borderWidth: 1,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      legend: {
        display: false,
      },
      tooltips: {
        callbacks: {
          label: function(tooltipItem, data) {
            return `${data.labels[tooltipItem.index]}: ${data.datasets[0].data[tooltipItem.index]}`;
          }
        }
      },
      title: {
        display: true,
        text: nome, // Título do gráfico será o nome do sensor
      }
    }
  });

  // Adicionar o gráfico ao array de graficos
  graficos.push({
    nome: nome,
    tipo: tipo,
    chart: chart
  });

  // Atualizar o seletor de sensores
  atualizarSeletorSensores();

  // Retornar o objeto de gráfico para poder atualizar os dados depois
  return chart;
}

// Função para atualizar o seletor de sensores
function atualizarSeletorSensores() {
  const selectSensor = document.getElementById('selectSensor');
  // Limpar as opções existentes
  selectSensor.innerHTML = '';

  // Adicionar uma opção para cada sensor no array de graficos
  graficos.forEach((grafico, index) => {
    const option = document.createElement('option');
    option.value = index;
    option.textContent = grafico.nome;
    selectSensor.appendChild(option);
  });

  // Atualizar o gráfico com o sensor selecionado
  selectSensor.addEventListener('change', function() {
    const index = parseInt(this.value);
    const sensorSelecionado = graficos[index];

    // Esconder todos os gráficos
    graficos.forEach(grafico => {
      grafico.chart.canvas.parentNode.style.display = 'none';
    });

    // Mostrar apenas o gráfico do sensor selecionado
    sensorSelecionado.chart.canvas.parentNode.style.display = 'block';
  });
}

// Adicionar evento de submit para o formulário de adicionar sensor
const formAdicionarSensor = document.getElementById('formAdicionarSensor');
formAdicionarSensor.addEventListener('submit', function(event) {
  event.preventDefault();

  const nomeSensor = document.getElementById('nomeSensor').value;
  const tipoSensor = document.getElementById('tipoSensor').value;

  // Enviar dados do sensor para o backend (AJAX)
  const formData = new FormData();
  formData.append('nome', nomeSensor);
  formData.append('tipo', tipoSensor);

  fetch('salvar_sensor.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    // Adicionar o novo sensor ao sistema
    const novoGrafico = adicionarSensor(nomeSensor, tipoSensor);

    // Lógica para gerar dados aleatórios (simulados) e atualizar o gráfico
    function gerarDadosAleatorios() {
      return {
        umidadeSolo: Math.floor(Math.random() * 100), // Umidade do solo (0-100%)
        temperatura: Math.floor(Math.random() * 40), // Temperatura (0-40°C)
        co2: Math.floor(Math.random() * 500), // CO2 (0-500 ppm)
      };
    }

    // Atualizar o gráfico com os dados aleatórios gerados
    function atualizarGrafico(chart, dados) {
      chart.data.datasets[0].data = [dados, 100 - dados];
      chart.options.title.text = `${nomeSensor}: ${dados}`; // Atualiza o título do gráfico com o novo dado
      chart.update(); // Atualiza o gráfico
    }

    // Simular geração de dados aleatórios e atualizar os gráficos a cada 2 segundos
    setInterval(function() {
      const dados = gerarDadosAleatorios()[tipoSensor];
      atualizarGrafico(novoGrafico, dados);
    }, 2000); // 2000 milissegundos = 2 segundos
  })
  .catch(error => {
    console.error('Erro ao salvar o sensor:', error);
  });
});

</script>
</body>
</html>

