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

// Consultar sensores do usuário
$sensores = $sensoresCollection->find(['user_id' => $userId]);

// Função para obter o tema atual da sessão ou do cookie se disponível
function getCurrentTheme() {
    // Verificar primeiro na sessão
    if (isset($_SESSION['theme'])) {
        return $_SESSION['theme'];
    }
    // Verificar se há um cookie de tema
    elseif (isset($_COOKIE['site_theme'])) {
        return $_COOKIE['site_theme'];
    } else {
        return 'default'; // Tema padrão se nenhum estiver definido
    }
}

// Função para obter a cor de destaque atual da sessão ou do cookie se disponível
function getCurrentColor() {
    // Verificar primeiro na sessão
    if (isset($_SESSION['color'])) {
        return $_SESSION['color'];
    }
    // Verificar se há um cookie de cor de destaque
    elseif (isset($_COOKIE['highlight_color'])) {
        return $_COOKIE['highlight_color'];
    } else {
        return '#06b6d4'; // Cor padrão se nenhuma estiver definida
    }
}

$currentTheme = getCurrentTheme();
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
    <title>Painel de administração</title>
</head>

<body class="<?php echo $currentTheme === 'dark' ? 'dark-mode' : ''; ?>">
    <div id="content">
        <!-- Cabeçalho -->
        <header>
            <!-- Barra de busca -->
            <div class="busca">
                <form action="">
                    <input type="text" placeholder="Pesquisar">
                    <button type="submit" title="Buscar">
                        <i class="bi bi-search"></i>
                    </button>
                </form>
                <!-- Botão de notificações -->
                <button title="Notificações" onclick="mostrarNotificacoes()">
                    <i class="bi bi-bell"></i>
                    <span class="badge">3</span>
                </button>
                <!-- Perfil do usuário -->
                <div class="perfil">
                    <img src="<?php echo $user['avatar']; ?>" alt="Sua foto de perfil">
                    <p><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <!-- Saudação e ações -->
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
                <!-- Links do menu -->
                <li class="selecionado">
                    <a href="home.php"><i class="bi bi-house"></i> Início</a>
                </li>
                <li>
                    <a href="perfil.php"><i class="bi bi-person"></i> Perfil</a>
                </li>
                <li>
                    <a href="monitoramento_ar.php"><i class="bi bi-chat"></i>Monitoramento de AR</a>
                </li>
                <li>
                    <a href="monitoramento_solo.php"><i class="bi bi-clock-history"></i>Monitoramento do Solo</a>
                </li>
                <li>
                    <a href="monitoramento_temperatura.php"><i class="bi bi-journals"></i>Monitoramento De Temperatura</a>
                </li>
                <li>
                    <a href="relatorio.php"><i class="bi bi-people"></i>Relatórios</a>
                </li>
            </ul>
            <!-- Links adicionais do menu -->
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
            <!-- Rodapé da barra lateral -->
            <footer>
                <p id="texto-footer">Clean Earth, <?php echo date('Y'); ?></p>
                <div class="links"></div>
            </footer>
        </aside>
        <!-- Conteúdo principal -->
        <main>
            <!-- Seção de Projetos (Gráficos de sensores) -->
            <section class="projetos">
                <div class="container">
                    <?php foreach ($sensores as $sensor): ?>
                        <div class="grafico <?php echo $sensor['tipo_planta']; ?>">
                            <h2><?php echo ucfirst($sensor['nome_sensor']); ?></h2>
                            <h2><?php echo ucfirst($sensor['tipo_planta']); ?></h2>
                            <div class="chart-container">
                                <canvas id="umidade_<?php echo $sensor['_id']; ?>"></canvas>
                                <canvas id="temperatura_<?php echo $sensor['_id']; ?>"></canvas>
                                <canvas id="co2_<?php echo $sensor['_id']; ?>"></canvas>
                            </div>
                            <ul>
                                <li><strong>Descrição:</strong> <?php echo $sensor['descricao']; ?></li>
                                <li><strong>Umidade do Solo:</strong> <?php echo $sensor['umidade_solo']; ?>%</li>
                                <li><strong>Temperatura:</strong> <?php echo $sensor['temperatura']; ?>°C</li>
                                <li><strong>CO2:</strong> <?php echo $sensor['co2']; ?> ppm</li>
                            </ul>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                var umidadeCtx = document.getElementById('umidade_<?php echo $sensor['_id']; ?>').getContext('2d');
                                var temperaturaCtx = document.getElementById('temperatura_<?php echo $sensor['_id']; ?>').getContext('2d');
                                var co2Ctx = document.getElementById('co2_<?php echo $sensor['_id']; ?>').getContext('2d');

                                // Gráfico de Umidade do Solo
                                var umidadeChart = new Chart(umidadeCtx, {
                                    type: 'pie',
                                    data: {
                                        labels: ['Umidade do Solo', ''],
                                        datasets: [{
                                            data: [<?php echo $sensor['umidade_solo']; ?>, 100 - <?php echo $sensor['umidade_solo']; ?>],
                                            backgroundColor: ['#007bff', '#f0f0f0'],
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
                                                    return currentValue.toFixed(2) + '%';
                                                }
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'Umidade do Solo',
                                        }
                                    }
                                });

                                // Gráfico de Temperatura
                                var temperaturaChart = new Chart(temperaturaCtx, {
                                    type: 'pie',
                                    data: {
                                        labels: ['Temperatura', ''],
                                        datasets: [{
                                            data: [<?php echo $sensor['temperatura']; ?>, 100 - <?php echo $sensor['temperatura']; ?>],
                                            backgroundColor: ['#ff0000', '#f0f0f0'],
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

                                // Gráfico de CO2
                                var co2Chart = new Chart(co2Ctx, {
                                    type: 'pie',
                                    data: {
                                        labels: ['CO2', ''],
                                        datasets: [{
                                            data: [<?php echo $sensor['co2']; ?>, 100 - <?php echo $sensor['co2']; ?>],
                                            backgroundColor: ['#ffc107', '#f0f0f0'],
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
                                                    return currentValue.toFixed(2) + ' ppm';
                                                }
                                            }
                                        },
                                        title: {
                                            display: true,
                                            text: 'CO2',
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

    <!-- Script do Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Script para alternar o modo escuro -->
    <script>
        function toggleDarkMode() {
            var body = document.body;
            var isDarkMode = body.classList.toggle('dark-mode');

            // Salvar a preferência do usuário no cookie
            document.cookie = "site_theme=" + (isDarkMode ? "dark" : "light") + "; path=/; expires=Fri, 31 Dec 9999 23:59:59 GMT";
        }
    </script>
</body>

</html>
