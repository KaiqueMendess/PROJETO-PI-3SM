<?php
require_once __DIR__ . '/../vendor/autoload.php';
use MongoDB\Client as MongoClient;
use Google\Client as GoogleClient;
use Dotenv\Dotenv;

// Inicia a sessão
session_start();

// Carrega as variáveis de ambiente do arquivo .env
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$googleClientId = $_ENV['GOOGLE_CLIENT_ID'];
$googleClientSecret = $_ENV['GOOGLE_CLIENT_SECRET'];

// Configuração do Google Client
$client = new GoogleClient();
$client->setClientId($googleClientId);
$client->setClientSecret($googleClientSecret);
$client->setRedirectUri('http://localhost/pimeioambiente/php/google_callback.php');
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['access_token'])) {
        die('Failed to obtain access token.');
    }
    $client->setAccessToken($token['access_token']);

    // Obtenha as informações do usuário autenticado
    $oauth2 = new Google_Service_Oauth2($client);
    $userInfo = $oauth2->userinfo->get();

    // Conexão com o MongoDB
    $mongoClient = new MongoClient("mongodb://localhost:27017");
    $database = $mongoClient->pimeioambiente;
    $usersCollection = $database->users;

    $email = $userInfo->email;
    $name = $userInfo->name;

    // Verifica se o usuário já está cadastrado
    $user = $usersCollection->findOne(['email' => $email]);

    if (!$user) {
        // Usuário não cadastrado, insere no banco de dados
        $insertResult = $usersCollection->insertOne([
            'email' => $email,
            'password' => '', // Ou armazene outro dado relevante
            'name' => $name
        ]);
        $userId = $insertResult->getInsertedId();
    } else {
        $userId = $user['_id'];
    }

    // Armazena o ID do usuário na sessão
    $_SESSION['user_id'] = (string) $userId;

    header('Location: ../sistema/home.php');
    exit();
} else {
    die('Authorization code not received.');
}
?>
