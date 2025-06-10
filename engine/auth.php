<?php
require_once __DIR__ . '/../config/settings.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin() {
    if (!isset($_SESSION['user'])) {
        header("Location: " . BASE_URL . "login.php");
        exit;
    }
}

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

function getClientId() {
    return $_SESSION['client'] ?? null;
}

function getRoleId() {
    return $_SESSION['user']['role_id'] ?? null;
}

function hasPermission($module, $submodule, $action) {
    $client = getClientId();
    $roleId = getRoleId();

    if (!$client || !$roleId) return false;

    $pdo = new PDO("sqlite:../clients/$client/database.sqlite");

    $stmt = $pdo->prepare("
        SELECT 1 FROM permissions 
        WHERE role_id = ? 
          AND (module = ? OR module = '*') 
          AND (submodule = ? OR submodule = '*') 
          AND (can_$action = 1)
        LIMIT 1
    ");
    $stmt->execute([$roleId, $module, $submodule]);

    return (bool) $stmt->fetchColumn();
}
