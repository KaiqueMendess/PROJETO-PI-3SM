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
        <button onclick="toggleDarkMode()">Alternar Modo Escuro</button>
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
    <section class="projetos">
      <div class="container">
        <div class="grafico umidade">
          <h2>Umidade do Solo</h2>
          <canvas id="umidadeChart" width="200" height="200"></canvas>
        </div>
        <div class="grafico temperatura">
          <h2>Temperatura</h2>
          <canvas id="temperaturaChart" width="200" height="200"></canvas>
        </div>
        <div class="grafico co2">
          <h2>CO2</h2>
          <canvas id="co2Chart" width="200" height="200"></canvas>
        </div>
      </div>
    </section>
    <section class="anuncios">
      <break>
      <div class="card">
      <h2>Anúncios</h2>
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

  // Função para gerar dados aleatórios
  function gerarDadosAleatorios() {
    return {
      umidadeSolo: Math.floor(Math.random() * 100), // Umidade do solo (0-100%)
      temperatura: Math.floor(Math.random() * 40), // Temperatura (0-40°C)
      co2: Math.floor(Math.random() * 500), // CO2 (0-500 ppm)
    };
  }

  // Inicializar os gráficos com Chart.js
  const umidadeCtx = document.getElementById('umidadeChart').getContext('2d');
  const temperaturaCtx = document.getElementById('temperaturaChart').getContext('2d');
  const co2Ctx = document.getElementById('co2Chart').getContext('2d');

  const umidadeChart = new Chart(umidadeCtx, {
    type: 'pie',
    data: {
      labels: ['Umidade do Solo', 'Outros'],
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
            return `${data.labels[tooltipItem.index]}: ${data.datasets[0].data[tooltipItem.index]}%`;
          }
        }
      },
      title: {
        display: true,
        text: 'Umidade do Solo',
      }
    }
  });

  const temperaturaChart = new Chart(temperaturaCtx, {
    type: 'pie',
    data: {
      labels: ['Temperatura', 'Outros'],
      datasets: [{
        data: [0, 100],
        backgroundColor: ['#28a745', '#dee2e6'],
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
            return `${data.labels[tooltipItem.index]}: ${data.datasets[0].data[tooltipItem.index]}°C`;
          }
        }
      },
      title: {
        display: true,
        text: 'Temperatura',
      }
    }
  });

  const co2Chart = new Chart(co2Ctx, {
    type: 'pie',
    data: {
      labels: ['CO2', 'Outros'],
      datasets: [{
        data: [0, 100],
        backgroundColor: ['#dc3545', '#dee22e6'],
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
            return `${data.labels[tooltipItem.index]}: ${data.datasets[0].data[tooltipItem.index]} ppm`;
          }
        }
      },
      title: {
        display: true,
        text: 'CO2',
      }
    }
  });
  // Função para alternar entre modo claro e escuro
function toggleDarkMode() {
  var body = document.body;
  body.classList.toggle("dark-mode");

  // Salvar a preferência do usuário (opcional - usando localStorage)
  var isDarkMode = body.classList.contains("dark-mode");
  localStorage.setItem("dark-mode", isDarkMode.toString());
}

// Carregar a preferência do modo escuro ao carregar a página
document.addEventListener("DOMContentLoaded", function() {
  var isDarkMode = localStorage.getItem("dark-mode") === "true";

  if (isDarkMode) {
    document.body.classList.add("dark-mode");
  }
});

  // Função para atualizar os gráficos
  function atualizarGraficos() {
    const dados = gerarDadosAleatorios(); // Gera dados fictícios, substitua pela sua lógica real

    // Atualizar dados dos gráficos de pizza
    umidadeChart.data.datasets[0].data = [dados.umidadeSolo, 100 - dados.umidadeSolo];
    umidadeChart.options.title.text = `Umidade do Solo: ${dados.umidadeSolo}%`;
    umidadeChart.update();

    temperaturaChart.data.datasets[0].data = [dados.temperatura, 100 - dados.temperatura];
    temperaturaChart.options.title.text = `Temperatura: ${dados.temperatura}°C`;
    temperaturaChart.update();

    co2Chart.data.datasets[0].data = [dados.co2, 100 - dados.co2];
    co2Chart.options.title.text = `CO2: ${dados.co2} ppm`;
    co2Chart.update();
  }

  // Chamar a função de atualização inicial
  atualizarGraficos();

  // Atualizar a cada 2 segundos
  setInterval(atualizarGraficos, 2000); // 2000 milissegundos = 2 segundos


</script>
</body>
</html>

