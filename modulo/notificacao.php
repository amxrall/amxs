<?php
require 'config.php';

// Recebe o conteúdo da notificação
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Loga a notificação recebida (para debug)
file_put_contents(__DIR__ . '/webhook.log', date('Y-m-d H:i:s') . " - RECEBIDO: " . $input . PHP_EOL, FILE_APPEND);

if (isset($data['data']['id'])) {
    $payment_id = $data['data']['id'];

    // Consulta o pagamento no Mercado Pago para obter status atualizado
    $ch = curl_init("https://api.mercadopago.com/v1/payments/$payment_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . MERCADO_PAGO_ACCESS_TOKEN
    ]);
    $response = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_errno) {
        file_put_contents(__DIR__ . '/webhook.log', date('Y-m-d H:i:s') . " - CURL ERROR: $curl_error\n", FILE_APPEND);
        http_response_code(500);
        exit;
    }

    $payment = json_decode($response, true);
    if (!$payment) {
        file_put_contents(__DIR__ . '/webhook.log', date('Y-m-d H:i:s') . " - JSON inválido da resposta da API Mercado Pago: $response\n", FILE_APPEND);
        http_response_code(500);
        exit;
    }

    if (isset($payment['status'])) {
        $status = $payment['status'];
        $external_reference = $payment['external_reference'] ?? '';
        $amount = floatval($payment['transaction_amount'] ?? 0);

        try {
            // Atualiza o status no banco
            $stmt = $pdo->prepare("UPDATE donate_history SET status = ? WHERE payment_id = ?");
            $stmt->execute([$status, $payment_id]);

            if ($status === 'approved' && $external_reference !== '') {
                // Atualiza saldo do usuário
                $stmtSaldo = $pdo->prepare("UPDATE site_balance SET saldo = saldo + ? WHERE account = ?");
                $stmtSaldo->execute([$amount, $external_reference]);

                file_put_contents(__DIR__ . '/log_pagamentos.txt', date('Y-m-d H:i:s') . " - Pagamento aprovado (ID: $payment_id, Login: $external_reference, Valor: $amount)\n", FILE_APPEND);
            } else {
                file_put_contents(__DIR__ . '/log_pagamentos.txt', date('Y-m-d H:i:s') . " - Pagamento status: $status (ID: $payment_id)\n", FILE_APPEND);
            }

            http_response_code(200);
            exit;
        } catch (PDOException $e) {
            file_put_contents(__DIR__ . '/webhook.log', date('Y-m-d H:i:s') . " - ERRO BD: " . $e->getMessage() . "\n", FILE_APPEND);
            http_response_code(500);
            exit;
        }
    } else {
        // Resposta inválida da API Mercado Pago
        file_put_contents(__DIR__ . '/webhook.log', date('Y-m-d H:i:s') . " - Falha ao obter status do pagamento: $payment_id\n", FILE_APPEND);
        http_response_code(400);
        exit;
    }
} else {
    // Dados inválidos recebidos
    file_put_contents(__DIR__ . '/webhook.log', date('Y-m-d H:i:s') . " - Notificação inválida: $input\n", FILE_APPEND);
    http_response_code(400);
    exit;
}
