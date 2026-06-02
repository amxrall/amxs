<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>DIAGNÓSTICO DO SISTEMA</h1>";

// 1. Teste de Arquivos
echo "<strong>1. Verificando arquivos:</strong><br>";
$files = ['inc/config.php', 'inc/db.php', 'inc/auth.php', 'inc/header.php'];
foreach ($files as $f) {
    if (file_exists($f)) {
        echo "✅ $f encontrado.<br>";
    } else {
        echo "❌ ERRO CRÍTICO: $f não existe!<br>";
        die();
    }
}
echo "<hr>";

// 2. Teste de Configuração
echo "<strong>2. Verificando Configurações (inc/config.php):</strong><br>";
require 'inc/config.php';
if (defined('COIN_PRICE')) {
    echo "✅ COIN_PRICE: " . COIN_PRICE . "<br>";
} else {
    echo "❌ ERRO: COIN_PRICE não está definido em config.php!<br>";
}
if (defined('DB_HOST')) {
    echo "✅ DB_HOST: " . DB_HOST . "<br>";
} else {
    echo "❌ ERRO: DB_HOST não está definido!<br>";
}
echo "<hr>";

// 3. Teste de Banco de Dados
echo "<strong>3. Teste de Conexão MySQL:</strong><br>";
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ Conexão com Banco de Dados: SUCESSO!<br>";
} catch (PDOException $e) {
    echo "❌ ERRO DE CONEXÃO: " . $e->getMessage() . "<br>";
    echo "<i>Dica: Se o erro for 'Host not allowed', veja a solução que enviei anteriormente.</i>";
    die();
}
echo "<hr>";

// 4. Teste de Sessão
echo "<strong>4. Teste de Sessão:</strong><br>";
session_start();
if (isset($_SESSION['user_login'])) {
    echo "✅ Usuário Logado: " . $_SESSION['user_login'] . "<br>";
} else {
    echo "⚠️ Usuário NÃO logado (Isso é normal se você não logou ainda, mas o buy.php exige login).<br>";
}

echo "<hr><h1>SE TUDO ESTIVER VERDE ACIMA, O PROBLEMA É NO CÓDIGO HTML DO BUY.PHP</h1>";
?>