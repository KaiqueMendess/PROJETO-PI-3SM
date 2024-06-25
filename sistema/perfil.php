<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#06b6d4">
    <link rel="stylesheet" href="sys.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Editar Perfil</title>
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

    // Processar o formulário de atualização do perfil
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $email = $user['email']; // E-mail não é editável
        $password = $_POST['password'];
        $avatar = $user['avatar'];

        // Verificar se uma nova foto de perfil foi enviada
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $avatar = 'img/perfil/' . basename($_FILES['avatar']['name']);
            move_uploaded_file($_FILES['avatar']['tmp_name'], $avatar);
        }

        // Atualizar as informações do usuário
        $updateData = ['name' => $name, 'avatar' => $avatar];
        if (!empty($password)) {
            // Atualizar a senha apenas se for fornecida uma nova senha
            $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $usersCollection->updateOne(
            ['_id' => $userId],
            ['$set' => $updateData]
        );

        // Redirecionar para a página de perfil com uma mensagem de sucesso
        header('Location: perfil.php?success=1');
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
                    <img src="<?php echo htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="Sua foto de perfil">
                    <p><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <br>
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
                <li class="selecionado">
                    <a href="perfil.php"><i class="bi bi-person"></i> Perfil</a>
                </li>
                <li>
                    <a href="monitoramento_ar.php"><i class="bi bi-chat"></i> Monitoramento de AR</a>
                </li>
                <li>
                    <a href="monitoramento_solo.php"><i class="bi bi-clock-history"></i> Monitoramento do Solo</a>
                </li>
                <li>
                    <a href="monitoramento_temperatura.php"><i class="bi bi-journals"></i> Monitoramento De Temperatura</a>
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
            <section class="perfil-editar">
                <h2>Editar Perfil</h2>
                <?php if (isset($_GET['success'])) : ?>
                    <p class="sucesso">Perfil atualizado com sucesso!</p>
                <?php endif; ?>
                <form action="perfil.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Nome</label>
                        <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="avatar">Foto de Perfil</label>
                        <input type="file" name="avatar" id="avatar" accept="image/*">
                        <img src="<?php echo htmlspecialchars($user['avatar'], ENT_QUOTES, 'UTF-8'); ?>" alt="Sua foto de perfil" class="preview">
                    </div>
                    <div class="form-group">
                        <label for="password">Nova Senha</label>
                        <input type="password" name="password" id="password">
                        <p class="info">Deixe em branco se não quiser mudar a senha.</p>
                    </div>
                    <div class="form-group">
                        <button type="submit">Salvar Alterações</button>
                    </div>
                </form>
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
        const footer = document.getElementById("texto-footer");
        footer.innerText = `Clean Earth, ${new Date().getFullYear()}`;

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
