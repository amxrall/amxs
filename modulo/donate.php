<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'config.php';

$message = '';
$messageClass = '';
$qr_code_base64 = '';
$payment_id = '';
$login = '';

function logDonate($msg) {
    $logFile = __DIR__ . '/donate.log';
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$date] $msg\n", FILE_APPEND);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $valor = floatval($_POST['valor'] ?? 0);

    logDonate("Requisição iniciada - login: $login, valor: $valor");

    if (empty($login)) {
        $message = "Por favor, informe seu login.";
        $messageClass = 'text-danger';
        logDonate("Erro: login vazio");
    } elseif ($valor < 1) {
        $message = "Valor mínimo é R$1,00.";
        $messageClass = 'text-danger';
        logDonate("Erro: valor insuficiente $valor");
    } else {
        $data = [
            "transaction_amount" => $valor,
            "description" => "Doação para servidor Lineage",
            "payment_method_id" => "pix",
            "payer" => [
                "email" => $login . "@emailfake.com"
            ],
            "notification_url" => "https://www.l2dragonsoul.com.br/modulo/donate/notificacao.php",
            "external_reference" => $login
        ];

        $idempotencyKey = md5($login . $valor . microtime(true));

        $ch = curl_init("https://api.mercadopago.com/v1/payments");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer " . MERCADO_PAGO_ACCESS_TOKEN,
            "X-Idempotency-Key: $idempotencyKey"
        ]);
        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            $message = "Erro na conexão: $error";
            $messageClass = 'text-danger';
            logDonate("CURL ERROR: $error");
        } else {
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $payment = json_decode($response);

            if ($httpCode !== 201) {
                $errorMessage = $payment->message ?? 'Resposta inesperada da API.';
                $message = "Erro ao gerar pagamento PIX: HTTP $httpCode - $errorMessage";
                $messageClass = 'text-danger';
                logDonate("HTTP $httpCode - $errorMessage | RESPONSE: $response");
            } elseif (!isset($payment->point_of_interaction->transaction_data->qr_code_base64)) {
                $message = "Erro ao gerar pagamento PIX. QR Code não recebido.";
                $messageClass = 'text-danger';
                logDonate("QR Code ausente na resposta: $response");
            } else {
                $qr_code_base64 = $payment->point_of_interaction->transaction_data->qr_code_base64;
                $payment_id = $payment->id;

                try {
                    $stmt = $pdo->prepare("INSERT INTO donate_history (login, payment_id, amount, status, date) VALUES (?, ?, ?, 'pending', NOW())");
                    $stmt->execute([$login, $payment_id, $valor]);
                    $message = "Pagamento PIX gerado com sucesso! Escaneie o QR Code abaixo para pagar.";
                    $messageClass = 'text-success';
                    logDonate("Pagamento criado com sucesso - payment_id: $payment_id, login: $login, valor: $valor");
                } catch (PDOException $e) {
                    $message = "Erro ao salvar no banco de dados.";
                    $messageClass = 'text-danger';
                    logDonate("Erro ao inserir no banco: " . $e->getMessage());
                }
            }
        }
        curl_close($ch);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<link rel="stylesheet" href="css/style.css">
    <meta charset="UTF-8" />
    <title>Donate - DragonSoul Lineage 2</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .text-danger { color: red; }
        .text-success { color: green; }
        .text-center { text-align: center; }
        .Input { width: 300px; padding: 8px; }
        button { padding: 10px 15px; }
    </style>
</head>
<body>
    <h2>Doação</h2>
    <form method="POST" action="">
        <label for="login">Login do Jogador:</label><br>
        <input type="text" name="login" id="login" class="Input" placeholder="Seu login" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>"><br><br>

        <label for="valor">Valor da Doação (mínimo R$1,00):</label><br>
        <input type="number" name="valor" id="valor" class="Input" step="0.01" min="1" placeholder="Ex: 10.00" required value="<?= htmlspecialchars($_POST['valor'] ?? '') ?>"><br><br>

        <button type="submit">Gerar QR Code PIX</button>
    </form>

    <?php if ($message): ?>
        <div class="text-center <?= $messageClass ?>" style="margin-top: 20px;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if ($payment_id): ?>
        <div class="text-center" style="margin-top: 10px;">
            <strong>Payment ID (para testes webhook):</strong>
            <p style="font-family: monospace; background: #eee; padding: 10px; display: inline-block;"><?= htmlspecialchars($payment_id) ?></p>
        </div>
    <?php endif; ?>

    <?php if ($qr_code_base64): ?>
        <div class="text-center" style="margin-top: 15px;">
            <img src="data:image/png;base64,<?= $qr_code_base64 ?>" alt="QR Code PIX" style="width: 200px; height: 200px;" />
            <p id="statusMessage">Aguardando confirmação do pagamento...</p>
            <p id="saldoAtualizado" style="font-weight: bold;"></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($payment_id) && !empty($login)): ?>
    <script>
        const paymentId = '<?= $payment_id ?>';
        const login = '<?= $login ?>';
        const statusMessage = document.getElementById('statusMessage');
        const saldoElement = document.getElementById('saldoAtualizado');

        function fetchSaldo() {
            fetch('get_saldo.php?login=' + encodeURIComponent(login))
                .then(res => res.json())
                .then(data => {
                    if (data.saldo !== undefined) {
                        saldoElement.textContent = 'Saldo atualizado: ' + data.saldo + ' coins';
                    } else {
                        saldoElement.textContent = 'Erro ao consultar saldo: ' + (data.error || 'desconhecido');
                    }
                });
        }

        function checkStatus() {
            fetch('verifica_status.php?payment_id=' + encodeURIComponent(paymentId))
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'approved') {
                        statusMessage.textContent = 'Pagamento aprovado! Seus coins foram entregues. Obrigado!';
                        fetchSaldo();
                        clearInterval(interval);
                    } else if (data.status === 'pending') {
                        statusMessage.textContent = 'Pagamento ainda pendente...';
                    } else {
                        statusMessage.textContent = 'Status do pagamento: ' + data.status;
                    }
                })
                .catch(() => {
                    statusMessage.textContent = 'Erro ao verificar status do pagamento.';
                });
        }

        const interval = setInterval(checkStatus, 10000);
        checkStatus();
    </script>
    <?php endif; ?>
</body>
</html>
