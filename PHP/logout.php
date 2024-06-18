<?php
session_start(); // Inicia a sessão se não estiver iniciada ainda
session_destroy(); // Destrói todas as informações registradas da sessão atual
header('Location: ../index.php'); // Redireciona o usuário para a página de login
?>
