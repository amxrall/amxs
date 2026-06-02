<?php
require 'config.php';
header('Content-Type: application/json');

$payment_id = $_GET['payment_id'] ?? '';

if (!$payment_id) {
    echo json_encode(['error' => 'payment_id não informado']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT status, login FROM donate_history WHERE payment_id = ?");
    $stmt->execute([$payment_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['status' => 'not_found']);
        exit;
    }

    $status = $row['status'];
    $login = $row['login'];

    if ($status === 'approved') {
        // Pega saldo atualizado do usuário
        $stmtSaldo = $pdo->prepare("SELECT saldo FROM site_balance WHERE account = ?");
        $stmtSaldo->execute([$login]);
        $saldo = $stmtSaldo->fetchColumn();

        echo json_encode([
            'status' => 'approved',
            'saldo' => $saldo,
            'login' => $login
        ]);
        exit;
    }

    // Para outros status (pending, rejected, etc)
    echo json_encode(['status' => $status]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao consultar o banco']);
}