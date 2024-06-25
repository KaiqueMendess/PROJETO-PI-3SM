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
$usersCollection = $database->users;
$sensoresCollection = $database->sensores;

$userId = $_SESSION['user_id'];

try {
    $userId = new MongoDB\BSON\ObjectId($userId);
} catch (InvalidArgumentException $e) {
    header('Location: ./login.php');
    exit();
}

$user = $usersCollection->findOne(['_id' => $userId]);

if (!$user) {
    header('Location: ./login.php');
    exit();
}

$sensores = $sensoresCollection->find(['user_id' => $userId]);
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
    <title>Painel de Temperatura</title>
</head>

<body>
    <div id="content">
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
                    <img src="https://images.emojiterra.com/google/noto-emoji/unicode-15/animated/1f44b.gif" alt="Sua foto de perfil">
                    <span></span>
                    <p>Olá, <?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
        </header>
        <aside>
            <div class="logo">
                <i class="bi bi-speedometer2"></i>
                <h1>Painel</h1>
            </div>
            <ul class="menu">
                <li>
                    <a href="home.php"><i class="bi bi-house"></i> Início</a>
                </li>
                <li>
                    <a href="perfil.php"><i class="bi bi-person"></i> Perfil</a>
                </li>
                <li>
                    <a href="monitoramento_ar.php"><i class="bi bi-chat"></i> Monitoramento de AR</a>
                </li>
                <li>
                    <a href="monitoramento_solo.php"><i class="bi bi-clock-history"></i> Monitoramento do Solo</a>
                </li>
                <li class="selecionado">
                    <a href="monitoramento_temperatura.php"><i class="bi bi-journals"></i> Monitoramento de Temperatura</a>
                </li>
                <li>
                    <a href="relatorio.php"><i class="bi bi-people"></i> Relatórios</a>
                </li>
            </ul>
            <ul class="menu">
            <li>
                    <a href="adicionar_sensor.php"><i class="bi bi-plus-circle"></i> Adicionar Sensor</a>
                </li>
                <li>
                    <a href="ajustes.php"><i class="bi bi-gear"></i> Ajustes</a>
                </li>
                <li>
                    <a href="ajuda.php"><i class="bi bi-info-circle"></i> Ajuda</a>
                </li>
                <li>
                    <a href="privacidade.php"><i class="bi bi-shield-check"></i> Privacidade</a>
                </li>
                <li>
                    <a href="../PHP/logout.php"><i class="bi bi-shield-check"></i> Sair</a>
                </li>
            </ul>
            <footer>
                <p id="texto-footer">Clean Earth, <?php echo date('Y'); ?></p>
                <div class="links"></div>
            </footer>
        </aside>
        <main>
            <section class="projetos">
                <div class="container">
                    <?php foreach ($sensores as $sensor): ?>
                        <div class="grafico <?php echo $sensor['tipo_planta']; ?>">
                            <h2><?php echo ucfirst($sensor['nome_sensor']); ?></h2>
                            <h2><?php echo ucfirst($sensor['tipo_planta']); ?></h2>
                            <div class="chart-container">
                                <canvas id="temperatura_<?php echo $sensor['_id']; ?>"></canvas>
                            </div>
                            <ul>
                                <li><strong>Descrição:</strong> <?php echo $sensor['descricao']; ?></li>
                                <li><strong>Temperatura:</strong> <?php echo $sensor['temperatura']; ?>°C</li>
                            </ul>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var temperaturaCtx = document.getElementById('temperatura_<?php echo $sensor['_id']; ?>').getContext('2d');

                                var temperaturaChart = new Chart(temperaturaCtx, {
                                    type: 'pie',
                                    data: {
                                        labels: ['Temperatura', ''],
                                        datasets: [{
                                            data: [<?php echo $sensor['temperatura']; ?>, 100 - <?php echo $sensor['temperatura']; ?>],
                                            backgroundColor: ['#FF0000', '#f0f0f0'],
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
                                                label: function (tooltipItem, data) {
                                                    var dataset = data.datasets[tooltipItem.datasetIndex];
                                                    var currentValue = dataset.data[tooltipItem.index];
                                                    return currentValue.toFixed(2) + '°C';
                                                }
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'Temperatura',
                                        }
                                    }
                                });
                            });
                        </script>
                    <?php endforeach; ?>
                </div>
            </section>

             <!-- Seção de Anúncios -->
             <section class="anuncios">
                <div class="card">
                    <h2>Ultimas Atualizações</h2>
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
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }
    </script>
</body>

</html>
