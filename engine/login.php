<?php

if (!$indexing) {
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$lkey = isset($_POST['lkey']) ? vCode($_POST['lkey']) : '';

if (!isset($_SESSION['lkey']) || empty($lkey) || $lkey !== $_SESSION['lkey']) {
    fim('Sessão expirada. Atualize a página e tente novamente.');
}

if (empty($_POST['ucp_login']) || empty($_POST['ucp_passw'])) {
    fim($LANG[12058]);
}

$user_login = vCode($_POST['ucp_login']);
$user_passw = trim($_POST['ucp_passw']);

if ($captcha_cp_on == 1) {
    $captcha = !empty($_POST['captcha']) ? vCode($_POST['captcha']) : '';

    require('captcha/securimage.php');
    $securimage = new Securimage();

    if ($securimage->check($captcha) == false) {
        fim($LANG[11979]);
    }
}

require_once('private/classes/classAccess.php');

$login = Access::login($user_login, $user_passw);

if ($login) {
    @Access::registerAccess($user_login);

    $_SESSION['acc'] = $user_login;
    $_SESSION['ses'] = md5($_SERVER['HTTP_USER_AGENT'] . $uniqueKey . 'logged');

    fim('', 'OK', './');
} else {
    fim($LANG[11990]);
}