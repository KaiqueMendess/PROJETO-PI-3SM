<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Para usar o MongoDB e PHPMailer

use MongoDB\Client as MongoClient;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Conexão com o MongoDB
    $mongoClient = new MongoClient($_ENV['MONGO_URL']);
    $database = $mongoClient->pimeioambiente;
    $passwordResetsCollection = $database->password_resets;
    $usersCollection = $database->users;

    // Busca o e-mail associado ao token
    $passwordReset = $passwordResetsCollection->findOne(['token' => $token]);

    if ($passwordReset) {
        $email = $passwordReset['email'];

        // Atualiza a senha do usuário
        $updateResult = $usersCollection->updateOne(
            ['email' => $email],
            ['$set' => ['password' => $new_password]]
        );

        if ($updateResult->getModifiedCount() == 1) {
            // Remove o token de redefinição
            $passwordResetsCollection->deleteOne(['token' => $token]);
            echo "Senha redefinida com sucesso.";
        } else {
            echo "Erro ao redefinir a senha.";
        }
    } else {
        echo "Token inválido.";
    }
}
?>
