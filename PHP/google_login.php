<?php
require_once __DIR__ . '/../vendor/autoload.php';

$client = new Google_Client();
$client->setClientId('1021941130204-qm8kgmtm6f70l0gm1bt85t21eg4rv20v.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/pimeioambiente/php/google_callback.php');
$client->addScope("email");
$client->addScope("profile");

$auth_url = $client->createAuthUrl();
header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
?>
