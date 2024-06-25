<?php
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
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
    <style>
        /* Estilos específicos para a página de política de privacidade */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .container h2 {
            color: #06b6d4;
            margin-bottom: 20px;
        }

        .politica {
            line-height: 1.6;
        }

        .politica h3 {
            color: #333;
            margin-bottom: 10px;
        }

        .politica p {
            color: #555;
            margin-bottom: 15px;
        }
    </style>
    <title>Política de Privacidade - Painel de Administração</title>
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
                    <!-- Exibir informações do perfil do usuário logado -->
                    <?php
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

                    if ($user) {
                        echo '<img src="' . $user['avatar'] . '" alt="Sua foto de perfil">';
                        echo '<p>' . htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') . '</p>';
                    } else {
                        // Se o usuário não for encontrado, redirecionar para o login
                        header('Location: ./login.php');
                        exit();
                    }
                    ?>
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
                <!-- Links do menu -->
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
                    <a href="relatorio.php"><i class="bi bi-people"></i> Relatórios</a>
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
                <li class="selecionado">
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

        <main>
            <!-- Seção de Política de Privacidade -->
            <section class="politica">
                <div class="container">
                    <h2>Política de Privacidade</h2>
                    <p>Nossa política de privacidade visa proporcionar a você, usuário do nosso painel de administração, informações claras e transparentes sobre como seus dados pessoais são coletados, armazenados e utilizados.</p>

                    <h3>Coleta de Dados</h3>
                    <p>Os dados pessoais que coletamos incluem nome, email, avatar e informações relacionadas aos sensores e monitoramentos realizados.</p>

                    <h3>Uso dos Dados</h3>
                    <p>Os dados são utilizados para melhorar a experiência do usuário, oferecer suporte técnico, desenvolver novas funcionalidades e realizar análises estatísticas.</p>

                    <h3>Compartilhamento de Dados</h3>
                    <p>Não compartilhamos seus dados pessoais com terceiros sem o seu consentimento expresso, exceto quando necessário para cumprir obrigações legais.</p>

                    <h3>Segurança dos Dados</h3>
                    <p>Implementamos medidas de segurança técnicas e organizacionais para proteger seus dados pessoais contra acesso não autorizado, uso indevido ou divulgação.</p>

                    <h3>Alterações na Política de Privacidade</h3>
                    <p>Reservamo-nos o direito de atualizar esta política periodicamente. Recomendamos que você revise esta página regularmente para estar informado sobre como estamos protegendo suas informações.</p>
                </div>
            </section>
        </main>
    </div>

    <!-- Script do Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Script para alternar o modo escuro -->
    <script>
        function toggleDarkMode() {
            document.body.classList.toggle('dark-mode');
        }

        function atualizarDadosSensores() {
            fetch('../PHP/gerar_dados.php')
                .then(response => response.text())
                .then(data => {
                    console.log(data); // Exibir mensagem de sucesso no console (opcional)
                    location.reload(); // Recarregar a página para refletir as mudanças
                })
                .catch(error => {
                    console.error('Erro ao atualizar dados dos sensores:', error);
                });
        }
    </script>
</body>

</html>
