<?php
require_once __DIR__ . '/../../config/config.php';
function checkUnauthorized($httpCode)
{
    if ($httpCode == 401) {

        session_destroy();

        header("Location: " . API_BASE_URL . "/private/auth.php?expired=1");
        exit();
    }
}