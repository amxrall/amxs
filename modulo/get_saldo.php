<?php
require 'config.php';
header('Content-Type: application/json');

$login = $_GET['login'] ?? '';

if (!$login) {
    echo json_encode(['error' => 'Login não informado']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT saldo FROM site_balance WHERE account = ?");
    $stmt->execute([$login]);
    $saldo = $stmt->fetchColumn();

    if ($saldo === false) {
        echo json_encode(['error' => 'Usuário não encontrado']);
    } else {
        echo json_encode(['saldo' => $saldo]);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao consultar saldo']);
}
