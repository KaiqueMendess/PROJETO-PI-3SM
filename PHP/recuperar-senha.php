<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="cadastro.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <img src="img/logo.png" alt="Logo" class="logo">
            <form id="resetPasswordForm" action="atualizar-senha.php" method="POST">
                <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>" required>
                <input type="password" name="new_password" id="password" placeholder="Nova Senha" required>
                <button type="submit">Redefinir Senha</button>
            </form>
        </div>
    </div>
</body>
</html>
