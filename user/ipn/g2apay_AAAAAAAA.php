<?php

error_reporting(0);
ini_set('error_reporting', 0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('default_charset', 'UTF-8');
date_default_timezone_set('America/Sao_Paulo');
header('HTTP/1.1 200 OK');

function saveLog($text, $dump=0) {
	$xpldName = explode('.', basename( __FILE__ ));
	$secretName = explode('_', $xpldName[0]);
	$f = fopen("logs/".$secretName[0]."_".date('m-Y')."__".md5($secretName[1]).".txt","a+");
	$t = date('d/m/Y H:i').": ".$text.($dump==1 ? " - Dump: ".strtr(print_r($_POST, true), array('  ' => ' ')) : "")."\r\n";
	fwrite($f, $t, strlen($t));
	fclose($f);
}

function vCode($content) {
	return addslashes(htmlentities(trim($content), ENT_QUOTES, 'ISO-8859-1'));
}

if(
empty($_POST['type']) || 
empty($_POST['transactionId']) ||
empty($_POST['userOrderId']) ||
empty($_POST['amount']) ||
empty($_POST['currency']) ||
empty($_POST['status']) ||
empty($_POST['hash'])
) {
	saveLog("RAW POSTs incompletos!", 1);
	exit;
}

if(file_exists('../private/configs.php')) {
	require('../private/configs.php');
} else {
	saveLog("Require configs!", 0);
}

if(file_exists('../../private/configs.php') && (!isset($host) || !isset($dbnm) || !isset($user) || !isset($pass))) {
	require('../../private/configs.php');
}

$type = vCode($_POST['type']);
$tid = vCode($_POST['transactionId']);
$ref = vCode($_POST['userOrderId']);
$price = vCode($_POST['amount']);
$curr = vCode($_POST['currency']);
$status = vCode($_POST['status']);
$hash = vCode($_POST['hash']);

if($hash != vCode(hash('sha256', trim($_POST['transactionId']).trim($_POST['userOrderId']).trim($_POST['amount']).$G2APay['api_secret']))) {
	saveLog("API hashs incompatíveis!", 1);
	exit;
}

if($type != "payment") {
	saveLog("Type inválido!", 1);
	exit;
}

if(file_exists('../private/classes/DB.php')) {
	require('../private/classes/DB.php');
} else {
	require('../../private/classes/DB.php');
}

new DB($conMethod, $host, $user, $pass, $dbnm, $port);

$d = DB::Executa("SELECT TOP 1 * FROM site_donations WHERE protocolo = '".$ref."'", "SITE");
if(count($d) == 0) {
	saveLog("Protocolo inexistente!", 1);
	exit;
}

$account = trim($d[0]['account']);
$coinsEntregar = intval(trim($d[0]['quant_coins']) + trim($d[0]['coins_bonus']));
$coinsEntregues = intval(trim($d[0]['coins_entregues']));
$personagem = trim($d[0]['personagem']);
$valor = trim($d[0]['valor']);
$currentStatus = intval(trim($d[0]['status']));
$currency = trim($d[0]['currency']);

$status = strtolower($status);
switch($status) {
	case "complete": $finalStatus = 3; break; // Pago
	case "canceled": $finalStatus = 5; break; // Cancelada
	default: $finalStatus = 1; break; // Pendente
}

if($currentStatus == 4 && $finalStatus == 3) {
	$finalStatus = 4;
}

if($currentStatus == 2 && $finalStatus != 3) {
	$finalStatus = 2;
}

$updateOrder = DB::Executa("UPDATE site_donations SET ultima_alteracao = '".time()."', transaction_code = '".$tid."', status = '".$finalStatus."', status_real = '".$status."' WHERE protocolo = '".$ref."'", "SITE");
if(!$updateOrder) {
	saveLog("Não foi possível atualizar o status da transação! #1", 1);
	exit;
}

if($autoDelivery != 1) {
	saveLog("Transação recebida e processada com sucesso! #1", 1);
	exit;
}

if($coinsEntregues != $coinsEntregar && $finalStatus == 3) {
	
	if(number_format($price, 2, '.', '') < number_format($valor, 2, '.', '')) {
		saveLog("O valor pago é inferior ao valor registrado!", 1);
		exit;
	}
	
	if($curr != $currency) {
		saveLog("Currency incorreto!", 1);
		exit;
	}
	
	$updateOrder = DB::Executa("UPDATE site_donations SET coins_entregues = '".$coinsEntregar."', status = '4' WHERE protocolo = '".$ref."'", "SITE");
	if(!$updateOrder) {
		saveLog("Não foi possível atualizar o status da transação! #2", 1);
		exit;
	}

	$checkExists = DB::Executa("SELECT TOP 1 * FROM site_balance WHERE account = '".$account."'", "SITE");
	if(count($checkExists) > 0) {
		$addBalance = DB::Executa("UPDATE site_balance SET saldo = (saldo+".$coinsEntregar.") WHERE account = '".$account."'", "SITE");
	} else {
		$addBalance = DB::Executa("INSERT INTO site_balance (account, saldo) VALUES ('".$account."', '".$coinsEntregar."')", "SITE");
	}
	
	if($addBalance) {
		saveLog("Transação recebida e saldo entregue com sucesso!", 1);
		exit;
	} else {
		saveLog("Não foi possível concluir e entregar o saldo da transação!", 1);
		exit;
	}

}

saveLog("Transação recebida e processada com sucesso! #2", 1);

