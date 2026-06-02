<?php
// === CONFIGURAÇÕES MERCADO PAGO ===
define('MERCADO_PAGO_ACCESS_TOKEN', 'APP_USR-1513932505552841-062620-0bc23713990449a1b86b1aaade81e872-2514302249'); 

// === CONFIGURAÇÃO DO BANCO DE DADOS ===
define('DB_HOST', '190.102.40.16');
define('DB_USER', 'root');
define('DB_PASS', '5612144as');
define('DB_NAME', 'lineage2');

// === CONEXÃO COM BANCO ===
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Erro ao conectar com o banco: ' . $e->getMessage());
}

// === FUNÇÃO PARA LOGAR ERROS EM ARQUIVO ===
function logMercadoPagoError($message) {
    $logFile = __DIR__ . '/mp_errors.log';
    $date = date('Y-m-d H:i:s');
    $messageFormatted = "[{$date}] {$message}\n";
    file_put_contents($logFile, $messageFormatted, FILE_APPEND);
}
