<?php
require_once 'inc/db.php';
require_once 'inc/auth.php'; // Para redirecionar usuário
require_once 'inc/config.php';

// O PayPal moderno redireciona para return_url, onde validamos e capturamos
if (isset($_GET['token']) && isset($_GET['protocolo'])) {
    
    $protocolo = $_GET['protocolo'];
    $orderIdPP = $_GET['token'];

    // 1. Obter Token novamente para capturar
    $apiBase = PP_SANDBOX ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
    $ch = curl_init($apiBase . "/v1/oauth2/token");
    curl_setopt($ch, CURLOPT_USERPWD, PP_CLIENT_ID.":".PP_SECRET);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $resToken = json_decode(curl_exec($ch));
    $accessToken = $resToken->access_token;
    curl_close($ch);

    // 2. Capturar Pagamento
    $ch = curl_init($apiBase . "/v2/checkout/orders/$orderIdPP/capture");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer $accessToken"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $captureRes = json_decode(curl_exec($ch));
    curl_close($ch);

    if (isset($captureRes->status) && $captureRes->status == 'COMPLETED') {
        // Sucesso, liberar coins
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("SELECT * FROM site_donations WHERE protocolo = ? AND status = 1");
            $stmt->execute([$protocolo]);
            $order = $stmt->fetch();

            if ($order) {
                // Update Status
                $pdo->prepare("UPDATE site_donations SET status = 2 WHERE protocolo = ?")->execute([$protocolo]);
                
                // Add Coins
                $balanceSql = "INSERT INTO site_balance (account, saldo) VALUES (?, ?) 
                               ON DUPLICATE KEY UPDATE saldo = saldo + ?";
                $pdo->prepare($balanceSql)->execute([$order['account'], $order['quant_coins'], $order['quant_coins']]);
                
                $pdo->commit();
                header("Location: success.php");
                exit;
            } else {
                 $pdo->rollBack(); // Já pago ou invalido
                 header("Location: dashboard.php");
            }

        } catch(Exception $e) {
            $pdo->rollBack();
            die("Erro ao processar.");
        }
    } else {
        header("Location: fail.php");
    }
} else {
    header("Location: index.php");
}
?>