<?php
require_once 'inc/db.php';
require_once 'inc/auth.php';
requireLogin();

$orderId = filter_input(INPUT_GET, 'order_id', FILTER_SANITIZE_NUMBER_INT);
$account = $_SESSION['user_login'];

// Verifica se o pedido existe e pertence ao usuário
$stmt = $pdo->prepare("SELECT * FROM site_donations WHERE protocolo = ? AND account = ? AND status = 1");
$stmt->execute([$orderId, $account]);
$order = $stmt->fetch();

if (!$order) {
    die("Pedido inválido ou já pago.");
}

// Integração API Mercado Pago via cURL
$url = "https://api.mercadopago.com/checkout/preferences";

$data = [
    "items" => [
        [
            "title" => "" . SITE_NAME . " - " . $order['quant_coins'] . " TKT",
            "quantity" => 1,
            "currency_id" => "BRL",
            "unit_price" => (float)$order['valor']
        ]
    ],
    "payer" => [
        "email" => "user@test.com" // O MP exige um email, pode pedir no cadastro ou usar dummy
    ],
    "back_urls" => [
        "success" => BASE_URL . "/success.php",
        "failure" => BASE_URL . "/fail.php",
        "pending" => BASE_URL . "/dashboard.php"
    ],
    "auto_return" => "approved",
    "external_reference" => $orderId, // IMPORTANTE: Vincula o ID do banco ao MP
    "notification_url" => BASE_URL . "/notify_mp.php" // Webhook
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . MP_ACCESS_TOKEN
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

$mpResponse = json_decode($response, true);

if (isset($mpResponse['init_point'])) {
    header("Location: " . $mpResponse['init_point']);
    exit;
} else {
    echo "Erro ao conectar com Mercado Pago.";
    print_r($mpResponse);
}
?>