<?php
// Configurações Gerais
define('SITE_NAME', 'L2 Cyclone - Donate');
define('BASE_URL', 'https://www.l2cyclone.com.br/donate'); // Alterar para sua URL real

// Economia
define('COIN_PRICE', 1.00); // Preço de 1 Coin em R$ (BRL)
define('CURRENCY', 'BRL');

// Mercado Pago (Credenciais de Produção ou Teste)
define('MP_ACCESS_TOKEN', 'APP_USR-4736799387552331-082209-0fcd60d0c324c4b03b74e88a5e8a5763-1054363226');

// PayPal (Credenciais Client ID e Secret)
define('PP_CLIENT_ID', 'SEU_CLIENT_ID_AQUI');
define('PP_SECRET', 'SEU_SECRET_AQUI');
define('PP_SANDBOX', true); // Mude para false em produção

// Banco de Dados
define('DB_HOST', '178.132.198.233');
define('DB_USER', 'l2cyclone');
define('DB_PASS', 'l2cyclone2026');
define('DB_NAME', 'l2cyclone');

// Configurações de Transferência para o Jogo
define('GAME_ITEM_ID', 9511); // ID do item no jogo (Ex: 57 = Adena, 4037 = Coin of Luck)
define('GAME_ITEM_NAME', 'Ticket Donater'); // Nome do item para mostrar na tela
define('ALLOW_ONLINE', false); // true = pode transferir com char online | false = obriga estar offline

?>