<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Cadastro</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <img src="img/logo.png" alt="Logo" class="logo">
            <form id="registerForm" action="PHP/registro.php" method="POST">
                <input type="text" name="name" id="name" placeholder="Nome" required>
                <input type="email" name="email" id="email" placeholder="Email" required>
                <span id="registerError" class="error-message"></span>
                <input type="password" name="password" id="password" placeholder="Senha" required>
                <span id="passwordError" class="error-message"></span>
                <button type="submit">Cadastrar-se</button>
                <button type="button" onclick="redirectToLogin()"> Voltar Para Login</button>
            </form>
        </div>
    </div>
    <script>
        function redirectToLogin() {
            window.location.href = 'index.php';
        }
    </script>
</body>
</html>
