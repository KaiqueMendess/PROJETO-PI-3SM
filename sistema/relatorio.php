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
$sensoresCollection = $database->sensores;

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

// Consultar sensores do usuário e converter para array
$sensores = iterator_to_array($sensoresCollection->find(['user_id' => $userId]));

// Preparar dados para o gráfico usando Chart.js
$labels = [];
$umidadeSolo = [];
$temperatura = [];
$co2 = [];

// Iterar sobre os sensores e coletar dados
foreach ($sensores as $sensor) {
    $labels[] = ucfirst($sensor['nome_sensor']);
    $umidadeSolo[] = $sensor['umidade_solo'];
    $temperatura[] = $sensor['temperatura'];
    $co2[] = $sensor['co2'];
}

// Convertendo dados para formato JSON que o Chart.js aceita
$labelsJSON = json_encode($labels);
$umidadeSoloJSON = json_encode($umidadeSolo);
$temperaturaJSON = json_encode($temperatura);
$co2JSON = json_encode($co2);

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#06b6d4">
    <link rel="stylesheet" href="sys.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Relatórios</title>
</head>

<body>
    <div id="content">
        <!-- Cabeçalho -->
        <header>
            <div class="busca">
                <form action="">
                    <input type="text" placeholder="Pesquisar">
                    <button type="submit" title="Buscar">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                <button title="Notificações" onclick="mostrarNotificacoes()">
                    <i class="bi bi-bell"></i>
                    <span class="badge">3</span>
                </button>
                <div class="perfil">
                    <img src="<?php echo $user['avatar']; ?>" alt="Sua foto de perfil">
                    <p><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <div class="saudacao">
                <div class="perfil">
                    <img src="https://images.emojiterra.com/google/noto-emoji/unicode-15/animated/1f44b.gif"
                        alt="Sua foto de perfil">
                    <span></span>
                    <p>Olá, <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

            </div>
        </header>
        <!-- Barra lateral (Menu de navegação) -->
        <aside>
            <div class="logo">
                <i class="bi bi-speedometer2"></i>
                <h1>Painel</h1>
            </div>
            <ul class="menu">
                <li><a href="home.php"><i class="bi bi-house"></i> Início</a></li>
                <li><a href="perfil.php"><i class="bi bi-person"></i> Perfil</a></li>
                <li><a href="monitoramento_ar.php"><i class="bi bi-chat"></i>Monitoramento de AR</a></li>
                <li><a href="monitoramento_solo.php"><i class="bi bi-clock-history"></i>Monitoramento do Solo</a></li>
                <li><a href="monitoramento_temperatura.php"><i class="bi bi-journals"></i>Monitoramento De Temperatura</a></li>
                <li class="selecionado"><a href="relatorio.php"><i class="bi bi-people"></i>Relatórios</a></li>
            </ul>
            <ul class="menu">
                <li><a href="adicionar_sensor.php"><i class="bi bi-plus-circle"></i> Adicionar Sensor</a></li>
                <li><a href="ajuste.php"><i class="bi bi-gear"></i> Ajustes</a></li>
                <li><a href="ajuda.php"><i class="bi bi-info-circle"></i> Ajuda</a></li>
                <li><a href="privacidade.php"><i class="bi bi-shield-check"></i> Privacidade</a></li>
                <li><a href="../PHP/logout.php"><i class="bi bi-shield-check"></i> Sair</a></li>
            </ul>
            <footer>
                <p id="texto-footer">Clean Earth, <?php echo date('Y'); ?></p>
                <div class="links"></div>
            </footer>
        </aside>
        <!-- Conteúdo principal -->
        <main>
            <section class="relatorios">
                <div class="container">
                    <h2>Relatórios</h2>
                    <!-- Gráfico de Barras -->
                    <canvas id="graficoRelatorios"></canvas>
                    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            var ctx = document.getElementById('graficoRelatorios').getContext('2d');
                            var myChart = new Chart(ctx, {
                                type: 'bar',
                                data: {
                                    labels: <?php echo $labelsJSON; ?>,
                                    datasets: [{
                                            label: 'Umidade do Solo (%)',
                                            data: <?php echo $umidadeSoloJSON; ?>,
                                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                            borderColor: 'rgba(54, 162, 235, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'Temperatura (°C)',
                                            data: <?php echo $temperaturaJSON; ?>,
                                            backgroundColor: 'rgba(255, 0, 0, 0.2)',
                                            borderColor: 'rgba(255, 99, 132, 1)',
                                            borderWidth: 1
                                        },
                                        {
                                            label: 'CO2 (ppm)',
                                            data: <?php echo $co2JSON; ?>,
                                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                            borderColor: 'rgba(75, 192, 192, 1)',
                                            borderWidth: 1
                                        }
                                    ]
                                },
                                options: {
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        });
                    </script>

                    <!-- Tutorial de Uso -->
                    <div class="tutorial">
                        <h3>Como Interpretar os Gráficos</h3>
                        <p>Nesta seção, você encontrará informações detalhadas sobre cada sensor monitorado. Aqui está como interpretar as métricas apresentadas:</p>
                        <ul>
                            <li><strong>Umidade do Solo (%):</strong> Representa a umidade atual do solo, indicando condições ideais para o crescimento das plantas.</li>
                            <li><strong>Temperatura (°C):</strong> Mostra a temperatura atual do ambiente próximo ao sensor, importante para monitorar variações que podem afetar o crescimento das plantas.</li>
                            <li><strong>CO2 (ppm):</strong> Indica a concentração de dióxido de carbono no ar ao redor do sensor, influenciando diretamente a fotossíntese das plantas.</li>
                        </ul>
                        <p>Use estas informações para ajustar as condições ambientais conforme necessário para garantir um ambiente ideal para o crescimento das plantas monitoradas.</p>
                    </div>
                    <!-- Fim do Tutorial de Uso -->
                </div>
            </section>

            <section class="anuncios">
                <div class="card">
                    <h2>Últimas Atualizações</h2>
                    <div>
                        <h4>Manutenção do site</h4>
                        <p>O site passa por manutenções rotineiras todos os dias em horários que não afetem o seu uso.</p>
                    </div>
                    <div>
                        <h4>Novidades</h4>
                        <p>Agora foi implementada a função de perfis editáveis. Não deixe de conferir!</p>
                    </div>
                    <div>
                        <h4>Política de privacidade atualizada</h4>
                        <p>Nossa política de privacidade foi atualizada, não deixe de dar uma olhada mais tarde!</p>
                    </div>
                     <!-- Exemplo de Relatório -->
                     <?php foreach ($sensores as $sensor): ?>
                        <div class="relatorio">
                            <h3><?php echo ucfirst($sensor['nome_sensor']); ?></h3>
                            <p><strong>Planta:</strong> <?php echo $sensor['tipo_planta']; ?></p>
                            <p><strong>Descrição:</strong> <?php echo $sensor['descricao']; ?></p>
                            <p><strong>Umidade do Solo:</strong> <?php echo $sensor['umidade_solo']; ?>%</p>
                            <p><strong>Temperatura:</strong> <?php echo $sensor['temperatura']; ?>°C</p>
                            <p><strong>CO2:</strong> <?php echo $sensor['co2']; ?> ppm</p>
                        </div>
                    <?php endforeach; ?>
                    <!-- Fim do Exemplo de Relatório -->
                </div>
            </section>
        </main>
    </div>
</body>

</html>
