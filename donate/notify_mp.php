<?php
// Versão Final com Logs de Auditoria
require_once 'inc/db.php';
require_once 'inc/config.php'; 

// Função simples para gravar o log
function gravarLog($mensagem) {
    $arquivo = 'historico_doacoes.txt';
    $texto = "[" . date('d/m/Y H:i:s') . "] " . $mensagem . PHP_EOL;
    file_put_contents($arquivo, $texto, FILE_APPEND);
}

// Captura os dados recebidos
$type = $_GET['topic'] ?? $_GET['type'] ?? '';
$dataID = $_GET['id'] ?? $_GET['data_id'] ?? '';

// Só processa se for notificação de pagamento
if (empty($dataID) || empty(MP_ACCESS_TOKEN)) {
    http_response_code(400);
    exit;
}

// Verifica o status no Mercado Pago
$ch = curl_init("https://api.mercadopago.com/v1/payments/" . $dataID);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . MP_ACCESS_TOKEN]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode != 200) {
    http_response_code(200);
    exit;
}

$payment = json_decode($response, true);

if (isset($payment['status']) && $payment['status'] == 'approved') {
    $externalRef = $payment['external_reference']; // Nosso ID do pedido
    $mpTransactionId = $payment['id']; // ID da transação no Mercado Pago

    try {
        $pdo->beginTransaction();

        // 1. Verifica se o pedido existe e AINDA NÃO FOI PAGO (Status 1)
        $stmt = $pdo->prepare("SELECT * FROM site_donations WHERE protocolo = ? AND status = 1");
        $stmt->execute([$externalRef]);
        $order = $stmt->fetch();

        if ($order) {
            // 2. Atualiza status para PAGO (2)
            $upd = $pdo->prepare("UPDATE site_donations SET status = 2 WHERE protocolo = ?");
            $upd->execute([$externalRef]);

            // 3. Entrega os Coins
            $balanceSql = "INSERT INTO site_balance (account, saldo) VALUES (?, ?) 
                           ON DUPLICATE KEY UPDATE saldo = saldo + ?";
            $balStmt = $pdo->prepare($balanceSql);
            $balStmt->execute([$order['account'], $order['quant_coins'], $order['quant_coins']]);

            $pdo->commit();

            // LOG DE SUCESSO (Auditoria)
            $logMsg = "SUCESSO | Protocolo: #$externalRef | Conta: {$order['account']} | Coins: {$order['quant_coins']} | Valor: R$ {$order['valor']} | MP_ID: $mpTransactionId";
            gravarLog($logMsg);

        } else {
            // Pedido já estava pago ou não existe
            $pdo->rollBack();
            
            // LOG DE AVISO (Para você saber se o MP está mandando repetido)
            // Descomente a linha abaixo se quiser registrar as repetições também
            // gravarLog("AVISO | Notificação duplicada ou pedido inexistente | Protocolo: #$externalRef");
        }
        
        // Responde OK para o Mercado Pago
        http_response_code(200);
        echo "OK";

    } catch (Exception $e) {
        $pdo->rollBack();
        gravarLog("ERRO CRÍTICO | Falha ao processar Protocolo #$externalRef | Erro: " . $e->getMessage());
        http_response_code(500);
    }
} else {
    http_response_code(200);
}
?>