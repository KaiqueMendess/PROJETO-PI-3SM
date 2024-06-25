<?php
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}

// Processar dados do formulário se enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar e salvar as preferências visuais
    if (isset($_POST['theme'])) {
        // Ajustar o tema selecionado
        $theme = $_POST['theme'];
        $_SESSION['theme'] = $theme;
        // Salvar também como cookie para persistência
        setcookie('site_theme', $theme, time() + (86400 * 30), '/'); // cookie válido por 30 dias
    }

    if (isset($_POST['color'])) {
        // Ajustar a cor de destaque selecionada
        $color = $_POST['color'];
        $_SESSION['color'] = $color;
        // Salvar também como cookie para persistência
        setcookie('highlight_color', $color, time() + (86400 * 30), '/'); // cookie válido por 30 dias
    }

    // Redirecionar de volta para a página de ajustes ou outra página conforme necessário
    header('Location: ajustes.php');
    exit();
}

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
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="<?php echo getCurrentColor(); ?>">
    <link rel="stylesheet" href="sys.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Estilos gerais */
        body {
            font-family: Arial, sans-serif;
            background-color: <?php echo getCurrentTheme() === 'dark' ? '#222' : '#f0f0f0'; ?>;
            color: <?php echo getCurrentTheme() === 'dark' ? '#fff' : '#333'; ?>;
            margin: 0;
            padding: 0;
            transition: background-color 0.3s ease, color 0.3s ease;
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
            color: <?php echo getCurrentColor(); ?>;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input[type="checkbox"] {
            margin-right: 10px;
        }

        .form-group button {
            padding: 10px 20px;
            background-color: <?php echo getCurrentColor(); ?>;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-group button:hover {
            background-color: #0599b3;
        }

        /* Estilos específicos para o modo escuro */
        body.dark-mode {
            background-color: #222;
            color: #fff;
        }

        body.dark-mode .container {
            background-color: #444;
            color: #fff;
        }

        body.dark-mode .form-group button {
            background-color: #444;
        }
    </style>
    <title>Ajustes - Painel de Administração</title>
</head>

<body class="<?php echo getCurrentTheme() === 'dark' ? 'dark-mode' : ''; ?>">
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
                <li class="selecionado">
                <a href="#"><i class="bi bi-gear"></i> Ajustes</a>
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
                <main>
        <!-- Seção de Ajustes -->
        <section class="ajustes">
            <div class="container">
                <h2>Ajustes</h2>
                <!-- Formulário de Ajustes -->
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <div class="form-group">
                        <label for="theme">Tema</label>
                        <select name="theme" id="theme">
                            <option value="default" <?php echo getCurrentTheme() === 'default' ? 'selected' : ''; ?>>Padrão</option>
                            <option value="dark" <?php echo getCurrentTheme() === 'dark' ? 'selected' : ''; ?>>Escuro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="color">Cor de Destaque</label>
                        <input type="color" id="color" name="color" value="<?php echo getCurrentColor(); ?>">
                    </div>
                    <div class="form-group">
                        <button type="submit">Salvar</button>
                    </div>
                </form>
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