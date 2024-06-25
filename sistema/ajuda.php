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
        /* Estilos adicionais específicos para a página de ajuda, se necessário */
    </style>
    <title>Ajuda - Painel de Administração</title>
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
                <li class="selecionado">
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
    <!-- Seção de Ajuda -->
    <section class="ajuda">
        <div class="container">
            <h2>Ajuda</h2>
            <!-- Explicação das abas -->
            <div class="explicacao">
                <h3>Início (home.php)</h3>
                <p>Aqui você encontrará um resumo dos principais dados e gráficos dos seus sensores de umidade do solo, temperatura e CO2.</p>

                <h3>Perfil (perfil.php)</h3>
                <p>Nesta seção, você pode visualizar e atualizar suas informações de perfil, como nome, email e avatar.</p>

                <h3>Monitoramento de AR (monitoramento_ar.php)</h3>
                <p>Este painel exibe dados e gráficos relacionados ao monitoramento da qualidade do ar, incluindo informações detalhadas sobre.</p>

                <h3>Monitoramento do Solo (monitoramento_solo.php)</h3>
                <p>Aqui você pode visualizar dados detalhados sobre a umidade do solo de seus sensores.</p>

                <h3>Monitoramento de Temperatura (monitoramento_temperatura.php)</h3>
                <p>Nesta aba, você encontrará gráficos e dados relacionados à temperatura ambiente, ajudando na gestão climática das suas áreas monitoradas.</p>

                <h3>Relatórios (relatorio.php)</h3>
                <p>Esta seção oferece relatórios detalhados sobre os dados coletados pelos seus sensores, permitindo análises mais aprofundadas e tomadas de decisão informadas.</p>
            </div>
        </div>
    </section>

            <!-- Seção de Anúncios -->
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
