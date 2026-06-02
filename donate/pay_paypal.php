<?php
require_once 'inc/db.php';
require_once 'inc/auth.php';
requireLogin();

$orderId = filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT);
$account = $_SESSION['user_login'];

$stmt = $pdo->prepare("SELECT * FROM site_donations WHERE protocolo = ? AND account = ? AND status = 1");
$stmt->execute([$orderId, $account]);
$order = $stmt->fetch();

if (!$order) die("Pedido inválido.");

// 1. Obter Token
$apiBase = PP_SANDBOX ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

$ch = curl_init($apiBase . "/v1/oauth2/token");
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, PP_CLIENT_ID.":".PP_SECRET);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
$result = curl_exec($ch);
$json = json_decode($result);
$accessToken = $json->access_token;
curl_close($ch);

// 2. Criar Ordem
$orderData = [
    "intent" => "CAPTURE",
    "purchase_units" => [[
        "reference_id" => $orderId,
        "amount" => [
            "currency_code" => "BRL",
            "value" => number_format($order['valor'], 2, '.', '')
        ]
    ]],
    "application_context" => [
        "return_url" => BASE_URL . "/notify_paypal.php?success=true&protocolo=".$orderId,
        "cancel_url" => BASE_URL . "/dashboard.php"
    ]
];

$ch = curl_init($apiBase . "/v2/checkout/orders");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $accessToken"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
$response = curl_exec($ch);
$orderRes = json_decode($response);
curl_close($ch);

// 3. Redirecionar
if (isset($orderRes->links)) {
    foreach ($orderRes->links as $link) {
        if ($link->rel == 'approve') {
            header("Location: " . $link->href);
            exit;
        }
    }
}
echo "Erro ao criar pedido PayPal.";
?>