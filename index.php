<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página de Login</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <img src="img/logo.png" alt="Logo" class="logo">
            <form id="loginForm" action="PHP/login.php" method="POST">
                <input type="text" name="email" id="Email" placeholder="Email" required>
                <span id="loginError" class="error-message"></span>
                <input type="password" name="password" id="password" placeholder="Senha" required>
                <span id="passwordError" class="error-message"></span>
                <a href="PHP/google_login.php" class="btn-google">
                    <img src="img/google.png" alt="Google Logo" class="google-logo"> Login Com o Google
                </a>
                <button type="submit" class="btn-cadastro">Login</button>
                <a href="cadastro.php" class="btn-cadastro">Cadastrar-se</a>
                <a href="recuperacao-de-senha.html" class="btn-senha">Recuperar Senha</a>
            </form>
        </div>
    </div>
</body>
</html>
