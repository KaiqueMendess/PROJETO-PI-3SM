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

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Processar os dados do formulário
    $tipoPlanta = $_POST['tipo_planta'];
    $nomeSensor = $_POST['nome_sensor'];
    $descricao = $_POST['descricao'];
    $umidadeSolo = $_POST['umidade_solo'];
    $temperatura = $_POST['temperatura'];
    $co2 = $_POST['co2'];

    // Inserir o novo sensor no banco de dados
    $insertResult = $sensoresCollection->insertOne([
        'user_id' => $userId,
        'tipo_planta' => $tipoPlanta,
        'nome_sensor' => $nomeSensor,
        'descricao' => $descricao,
        'umidade_solo' => $umidadeSolo,
        'temperatura' => $temperatura,
        'co2' => $co2,
    ]);

    if ($insertResult->getInsertedCount() > 0) {
        // Sensor adicionado com sucesso
        $sensorAdicionado = true;
    } else {
        // Falha ao adicionar sensor
        $erroAdicao = true;
    }
}
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
    <title>Adicionar Sensor</title>
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
                <button title="Notificações"><i class="bi bi-bell"></i></button>
                <div class="perfil">
                    <img src="<?php echo htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="Sua foto de perfil">
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
                <li>
                    <a href="monitoramento_temperatura.php"><i class="bi bi-journals"></i> Monitoramento de Temperatura</a>
                </li>
                <li>
                    <a href="#"><i class="bi bi-people"></i> Relatórios</a>
                </li>
            </ul>
            <ul class="menu">
                <li class="selecionado">
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
            <section class="adicionar-sensor">
                <h2>Adicionar Novo Sensor</h2>
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="campo">
                        <label for="tipo_planta">Tipo de Planta:</label>
                        <select id="tipo_planta" name="tipo_planta" required>
                            <option value="rosa">Rosa (Umidade: 40-60%, Temperatura: 18-25°C, CO2: 300-500 ppm)</option>
                            <option value="orquidea">Orquídea (Umidade: 50-70%, Temperatura: 20-28°C, CO2: 350-550 ppm)</option>
                            <option value="samambaia">Samambaia (Umidade: 60-80%, Temperatura: 22-30°C, CO2: 400-600 ppm)</option>
                            <option value="cacto">Cacto (Umidade: 10-30%, Temperatura: 20-35°C, CO2: 200-400 ppm)</option>
                            <option value="hera">Hera (Umidade: 50-70%, Temperatura: 18-25°C, CO2: 300-500 ppm)</option>
                            <option value="bonsai">Bonsai (Umidade: 40-60%, Temperatura: 18-25°C, CO2: 300-500 ppm)</option>
                        </select>
                    </div>
                    <div class="campo">
                        <label for="nome_sensor">Nome do Sensor:</label>
                        <input type="text" id="nome_sensor" name="nome_sensor" required>
                    </div>
                    <div class="campo">
                        <label for="descricao">Descrição:</label>
                        <textarea id="descricao" name="descricao" rows="3" required></textarea>
                    </div>
                    <div class="campo">
                        <label for="umidade_solo">Umidade do Solo (%):</label>
                        <input type="number" id="umidade_solo" name="umidade_solo" min="0" max="100" required>
                    </div>
                    <div class="campo">
                        <label for="temperatura">Temperatura (°C):</label>
                        <input type="number" id="temperatura" name="temperatura" min="-50" max="50" required>
                    </div>
                    <div class="campo">
                        <label for="co2">CO2 (ppm):</label>
                        <input type="number" id="co2" name="co2" min="0" max="1000" required>
                    </div>
                    <button type="submit">Adicionar Sensor</button>
                </form>

                <?php if (isset($sensorAdicionado) && $sensorAdicionado): ?>
                    <div class="mensagem-sucesso">
                        <p>Sensor adicionado com sucesso!</p>
                    </div>
                    <div class="simulador">
                        <h3>Simulador de Comportamento do Sensor</h3>
                        <p>Visualize como o sensor se comportaria nas condições ideais para a planta selecionada.</p>
                        <!-- Aqui você pode implementar um simulador visual -->
                    </div>
                    <div class="sensor-adicionado">
                        <h3>Detalhes do Sensor Adicionado</h3>
                        <ul>
                            <li><strong>Tipo de Planta:</strong> <?php echo htmlspecialchars($tipoPlanta, ENT_QUOTES, 'UTF-8'); ?></li>
                            <li><strong>Nome do Sensor:</strong> <?php echo htmlspecialchars($nomeSensor, ENT_QUOTES, 'UTF-8'); ?></li>
                            <li><strong>Descrição:</strong> <?php echo htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8'); ?></li>
                            <li><strong>Umidade do Solo (%):</strong> <?php echo htmlspecialchars($umidadeSolo, ENT_QUOTES, 'UTF-8'); ?></li>
                            <li><strong>Temperatura (°C):</strong> <?php echo htmlspecialchars($temperatura, ENT_QUOTES, 'UTF-8'); ?></li>
                            <li><strong>CO2 (ppm):</strong> <?php echo htmlspecialchars($co2, ENT_QUOTES, 'UTF-8'); ?></li>
                        </ul>
                    </div>
                <?php elseif (isset($erroAdicao) && $erroAdicao): ?>
                    <div class="mensagem-erro">
                        <p>Ocorreu um erro ao adicionar o sensor. Por favor, tente novamente mais tarde.</p>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const footer = document.getElementById("texto-footer");
        footer.innerText = `Clear Earth, ${new Date().getFullYear()}`;

        // Função para alternar entre modo claro e escuro
        function toggleDarkMode() {
            var body = document.body;
            body.classList.toggle("dark-mode");

            // Salvar a preferência do usuário (opcional - usando localStorage)
            var isDarkMode = body.classList.contains("dark-mode");
            localStorage.setItem("dark-mode", isDarkMode.toString());
        }

        // Carregar a preferência do modo escuro ao carregar a página
        document.addEventListener("DOMContentLoaded", function () {
            var isDarkMode = localStorage.getItem("dark-mode") === "true";

            if (isDarkMode) {
                document.body.classList.add("dark-mode");
            }
        });
    </script>
</body>

</html>
