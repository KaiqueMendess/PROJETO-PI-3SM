<?php
require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client as MongoClient;

$client = new MongoClient("mongodb://localhost:27017");
$database = $client->pimeioambiente;
$collection = $database->users;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // URL da foto de perfil padrão
    $defaultProfilePicture = 'img/default_profile_picture.jpg';

    // Verificar se o email já está cadastrado
    $user = $collection->findOne(['email' => $email]);

    if ($user) {
        echo "Email já cadastrado!";
    } else {
        // Inserir novo usuário no MongoDB
        $result = $collection->insertOne([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'avatar' => $defaultProfilePicture // Definindo a foto de perfil padrão
        ]);

        if ($result->getInsertedCount() == 1) {
            echo "Cadastro realizado com sucesso!";
        } else {
            echo "Erro ao cadastrar!";
        }
    }
}
?>
