<?php

session_start();
require_once 'helper/auth_helper.php';
require_once __DIR__ . '/../config/config.php';
$id = $_POST['id'];

$ch = curl_init(API_BASE_URL . "/notifications/$id/read");

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer " . $_SESSION['token']
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo(
    $ch,
    CURLINFO_HTTP_CODE
);
curl_close($ch);
checkUnauthorized($httpCode);

echo $response;