<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: ./php/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SISTEMA DE MONITORAMENTO</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="profile">
                <div class="profile-picture"></div>
                <p>Fulano</p>
            </div>
            <nav>
                <ul>
                    <li><a href="home.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="monitoramento.php"><i class="fas fa-chart-line"></i> Monitoramento</a></li>
                    <li><a href="relatorio.php"><i class="fas fa-file-alt"></i> Relatórios</a></li>
                    <li><a href="sobre.php"><i class="fas fa-info-circle"></i> Sobre</a></li>
                </ul>
            </nav>
            <form action="./php/logout.php" method="post">
                <button class="logout"><i class="fas fa-sign-out-alt"></i> Sair</button>
            </form>
        </div>
        <div class="main-content">
            <h1>Monitoramento</h1>
            <div class="charts">
                <div class="chart" id="chart1"></div>
                <div class="chart" id="chart2"></div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js"></script>
</body>
</html>
