<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Carrega o autoload do Composer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use MongoDB\Client as MongoClient;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load(); // Carrega as variáveis de ambiente do arquivo .env

// Conexão com o MongoDB
$mongoClient = new MongoClient("mongodb://localhost:27017");
$database = $mongoClient->pimeioambiente;
$passwordResetsCollection = $database->password_resets;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(50)); // Gera um token de 100 caracteres hexadecimais

    // Insere o token de redefinição no MongoDB
    $insertResult = $passwordResetsCollection->insertOne([
        'email' => $email,
        'token' => $token,
        'created_at' => new MongoDB\BSON\UTCDateTime() // Adiciona a data/hora de criação
    ]);

    if ($insertResult->getInsertedCount() == 1) {
        $mail = new PHPMailer(true); // Inicializa o PHPMailer com exceções habilitadas

        try {
            // Configuração do servidor de e-mail (Outlook / Hotmail)
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com'; // Host SMTP do Outlook (Hotmail)
            $mail->SMTPAuth = true;
            $mail->Username = $_ENV['MAIL_USERNAME']; // Seu usuário de e-mail do Outlook (Hotmail)
            $mail->Password = $_ENV['MAIL_PASSWORD']; // Sua senha de e-mail do Outlook (Hotmail)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
            $mail->Port = 587; // Porta do servidor SMTP do Outlook (Hotmail)

            // Remetente e destinatário
            $mail->setFrom($_ENV['MAIL_USERNAME'], 'Seu Nome'); // Usuário de e-mail como remetente
            $mail->addAddress($email); // E-mail do destinatário

            // Conteúdo do e-mail
            $mail->isHTML(true);
            $subject = '=?UTF-8?B?' . base64_encode('Redefinição de Senha') . '?='; // Título codificado em UTF-8
            $resetLink = "http://localhost/pimeioambiente/PHP/recuperar-senha.php?token=$token";
            $mail->Subject = $subject;
            $mail->Body    = "<h3>Recuperação de Senha</h3>
                              <p>Olá,</p>
                              <p>Você solicitou a redefinição de senha. Clique no link abaixo para iniciar o processo de redefinição:</p>
                              <p><a href='$resetLink'>Redefinir Senha</a></p>
                              <p>Se você não solicitou esta redefinição, ignore este e-mail.</p>";

            // Envia o e-mail
            $mail->send();
            echo 'Link de redefinição enviado para seu e-mail.';
        } catch (Exception $e) {
            echo "Erro ao enviar o e-mail. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Erro ao gerar link de redefinição.";
    }
}
?>
