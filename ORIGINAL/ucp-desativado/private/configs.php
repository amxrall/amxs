<?php require_once('../private/configs.php'); $siteVinculed = 1;

###########################################################
##                   Configurações                       ##
###########################################################
//$panel_url = 'https://playlineage2.com/ucp'; // Digite exatamente o URL onde se encontra este painel (exemplo: www.l2server.com/ucp)
$panel_url = 'http://www.l2agape.com/ucp'; // Digite exatamente o URL onde se encontra este painel (exemplo: www.l2server.com/ucp)
$themeColor = 'black'; // Qual a tonalidade de cor predominante na template? (Escolha: default, black, blue, red, green ou purple)
$defaultLang = 'PT'; // Idioma padrão do painel (Escolha entre: PT, EN ou ES) - O painel conta com um sistema inteligente que detecta o idioma do navegador do usuário e exibe tudo naquele idioma, mas caso não consigamos detectar ou caso o navegador esteja num idioma diferente dos três citados anteriormente, o idioma setado aqui será o exibido
$gmt = '-3'; // Se os scripts do painel estiverem num horário adiantado ou atrasado, altere o GMT. Exemplo: -1 (-1 hora), +3 (+3 horas), etc


###########################################################
##              Controle de Cores layut ucp              ##
###########################################################

$ucpColor1 = '1'; // escolha Qual a tonalidade de cor fixa no menu lateral esquerda ? 
//(Escolha o numero de referença: 1=preto 2=laranja 3=verde 4=azul 5=vermelho 6=amarelo 7=roza 8=roxo 9=branco 10=marrom 11=cinza)

$ucpf5 = 0; // Você deseja que o laouyt da ucp mude as cores ao atualizar a pagina  de forma aleatoria? (1 = Sim | 0 = Não)")



########################
$ucpColor[0] = '1';#########
$ucpColor[1] = '2';#############
$ucpColor[2] = '3';###################
$ucpColor[3] = '4';###NAO MECHER AQUI#
$ucpColor[4] = '5';###################
$ucpColor[5] = '6';###############
$ucpColor[6] = '7';#############
$ucpColor[7] = '8';#############
$ucpColor[8] = '9';#############
$ucpColor[9] = '10';#############
$ucpColor[10] = '11';#############
$numero1 = rand(0,10);#######
########################

$avatar[0] = 'dark_female';
$avatar[1] = 'dwarf_female';
$avatar[2] = 'elf_male';
$avatar[3] = 'human_male_fighter';
$avatar[4] = 'orc_female_fighter';
$avatar[5] = 'dark_male';
$avatar[6] = 'elf_female';
$avatar[7] = 'kamael_female';
$avatar[8] = 'dwarf_male';
$avatar[9] = 'human_female_fighter';
$avatar[10] = 'orc_male_fighter';
$avatar[11] = 'kamael_female';
$avatar[12] = 'human_female_mage';
$avatar[13] = 'human_male_mage';
$avatar[14] = 'orc_male_mage';
$avatar[15] = 'orc_female_mage';
$avatar[16] = 'unknow';
$numero = rand(0,16);

###########################################################
##              Controle de funcionalidades              ##
###########################################################
// Quais funcionalidades estão disponíveis para os jogadores? (1 = Disponível | 0 = Indisponível)
$funct['regist'] = 1; // Se cadastrar através do painel
$funct['forgot'] = 1; // Recuperar conta através do painel
$funct['donate'] = 1; // Fazer doações/adquirir moedas
$funct['trnsf1'] = 1; // Transferir moedas online para um personagem in-game - Possibilita converter seu saldo para coins/ticket in-game
$funct['trnsf2'] = 1; // Transferir moedas online para outra conta
$funct['trnsf3'] = 0; // Transferir moedas de um personagem in-game para saldo online - Possibilita acrescentar saldo removendo coins/ticket in-game
$funct['servic'] = 1; // Serviços (todos os serviços)
$funct['shopon'] = 1; // Shop
$funct['gamst1'] = 1; // Game Stats - Top PvP
$funct['gamst2'] = 1; // Game Stats - Top PK
$funct['gamst3'] = 1; // Game Stats - Top Clan
$funct['gamst4'] = 0; // Game Stats - Top Online
$funct['gamst5'] = 1; // Game Stats - Grand Olympiad
$funct['gamst6'] = 1; // Game Stats - Boss Status
$funct['gamst7'] = 0; // Game Stats - Castle & Siege
$funct['gamst8'] = 1; // Game Stats - Top Level
$funct['gamst9'] = 0; // Game Stats - Top Adena
$funct['gams10'] = 0; // Game Stats - Boss Jewels Control
$funct['galle1'] = 0; // Galeria - Enviar Screenshot
$funct['galle2'] = 0; // Galeria - Enviar Vídeo
$funct['galle3'] = 0; // Galeria - Visualizar
$funct['config'] = 1; // Configurações (alterar dados da conta)


###########################################################
##                Cadastro e Recuperação                 ##
###########################################################
// Caso indisponibilize acima as opções de "Se cadastrar através do painel" ou "Recuperar conta através do painel", você pode inserir abaixo links externos para que os jogadores possam se cadastrar ou recuperar suas contas em uma página externa (caso deixe em branco, as opções irão sumir)
$link_regist = ""; // Link da página externa de cadastro
$link_forgot = ""; // Link da página externa de recuperar


###########################################################
##                        Serviços                       ##
###########################################################

// Vamos definir aqui quais serviços estão disponíveis para os personagens e quantas moedas online eles custam...

# Character Nickname (altera nome)
$service['actv']['changename'] = 1; // Está disponível? (1 = Sim | 0 = Não)
$service['cost']['changename'] = 30; // Qual o custo?

# Sex Change (altera gênero/sexo)
$service['actv']['sexchange'] = 1; // Está disponível? (1 = Sim | 0 = Não)
$service['cost']['sexchange'] = 30; // Qual o custo?

# Unstuck (move para coordenadas seguras)
$service['actv']['unstuck'] = 1; // Está disponível? (1 = Sim | 0 = Não)
$service['cost']['unstuck'] = 0; // Qual o custo?

# Change Base Class (altera base class)
$service['actv']['basechange'] = 0; // Está disponível? (1 = Sim | 0 = Não)
$service['cost']['basechange'] = 21; // Qual o custo?

// Outras configurações:

$addBaseSkills = 0; // Ao alterar a base class é necessário adicionar as skills daquela classe? (1 = Sim | 0 = Não) - Em muitas revisões o próprio servidor faz essa função. Se a sua é uma delas, deixe 0.

// Locs X, Y e Z utilizados no serviço de Unstuck
$unstuck_loc_x = '83257'; // Padrão: 83257
$unstuck_loc_y = '149058'; // Padrão: 149058
$unstuck_loc_z = '-3400'; // Padrão: -3400


###########################################################
##                  Aquisição de Saldo                   ##
###########################################################
$coinName = 'Ticket Donater'; // Nome da moeda online que representa o saldo (usada apenas no painel de usuário)
$coinName_mini = 'Ticket Donater'; // Nome resumido da moeda
$coinQntV = 1; // QuaCoin of Luckl a quantidade comercializada? Você definirá o valor dessa quantidade logo abaixo. (ex: se definir 10 aqui e nas configurações dos "Modulos de doação" abaixo definir 1.00 como valor, o usuário poderá adquirir 10 por R$ 1,00, 20 por R$ 2,00, etc)

// Bonus em porcentagem ao adquirir moeda online em altas quantidades (Exemplo: a cada 100 moedas compradas, ganha 10%, ou seja, paga pelas 100, mas recebe 110)
$bonusActived = 1; // Deseja habilitar a bonificação por compra em quantidade? (1 = Sim | 0 = Não)

// Você pode inserir até 3 bonificações! Caso não queira usar alguma, basta setar os valores como '0' que será desconsiderada.

// Bonificação 1:
$buyCoins['bonus_count'][1] = '100'; // A partir de qual quantidade o bônus abaixo é dado?
$buyCoins['bonus_percent'][1] = '15'; // Qual a porcentagem de bonificação?

// Bonificação 2:
$buyCoins['bonus_count'][2] = '200'; // A partir de qual quantidade o bônus abaixo é dado?
$buyCoins['bonus_percent'][2] = '25'; // Qual a porcentagem de bonificação?

// Bonificação 3:
$buyCoins['bonus_count'][3] = '500'; // A partir de qual quantidade o bônus abaixo é dado?
$buyCoins['bonus_percent'][3] = '40'; // Qual a porcentagem de bonificação?

// Exclusão de fatura
$delFatura = 1; // O usuário pode excluir uma fatura? (1 = Sim | 0 = Não) - OBS: Uma fatura nunca é excluída, ela é ocultada, mas sempre permanecerá no banco de dados.


###########################################################
##         Transferência por coin/ticket in-game         ##
###########################################################
// Caso esteja habilitada a funcionalidade "Transferir moedas online para um personagem in-game", o jogador poderá converter seu saldo online para moedas in-game! Precisamos definir algumas informações...
$coinGame = 'Ticket Donater'; // Nome da moeda donate in-game (geralmente Coin, Ticket ou Gold)
$coinID = 9511; // ID da moeda


###########################################################
##                   Modulos de doação                   ##
###########################################################

$autoDelivery = 1; // Você deseja que a entrega do saldo seja feita de forma automática? (1 = Sim | 0 = Não) (se optar de forma manual, as doações pagas ficarão com status "Paga". Você terá que ir até o painel admin e concluí-las clicando no botão "Entregar". Quando concluir, o saldo será adicionado e o status passará a ser "Entregue")
$donateEmail = 'williamsfontinelle@gmail.com'; // Email que receberá os comprovantes de pagamento para as transações bancárias e módulos de confirmação manual

// PAGSEGURO CONFIGS:
$PagSeguro['actived'] = 0; // Opção ativa? (1 = Sim / 0 = Não)
$PagSeguro['email'] = 'taina.fsm2@gmail.com'; // Email da conta que receberá as doações
$PagSeguro['token'] = '8ee6994c-fdfd-4dcc-ad51-55104bf8260ccda113e24616a29462cd601eb697afa9639d-1f77-4723-93c3-a42811d1505d'; // Token gerado no PagSeguro
$PagSeguro['token_sandbox'] = 'DBD5D19D3C16406485B426FFC3130CC3'; // Token gerado no ambiente de testes do PagSeguro
$PagSeguro['testando'] = 0; // Está testando o sistema através do PagSeguro Sandbox? (1 = Sim | 0 = Não)
$PagSeguro['coin_price'] = '1.00'; // Valor da quantidade comercializada (em Reais)

// PAYPAL CONFIGS:
$PayPal['actived'] = 0; // Opção ativa? (1 = Sim / 0 = Não)
$PayPal['business_email'] = 'williamsfontinelle@gmail.com'; // Email da conta que receberá as doações
$PayPal['USD']['coin_price'] = '1.00'; // Valor da quantidade comercializada (em Dolar)
$PayPal['BRL']['coin_price'] = '1.00'; // Valor da quantidade comercializada (em Reais)
$PayPal['EUR']['coin_price'] = '1.00'; // Valor da quantidade comercializada (em Euros)
$PayPal['testando'] = 0; // Está testando o sistema através do PayPal Sandbox? (1 = Sim | 0 = Não)

// MERCADOPAGO CONFIGS:
$MercadoPago['actived'] = 1; // Opción activa? (1 = Sí / 0 = No)
$MercadoPagoPix['actived'] = 1; // Opción activa? (1 = Sí / 0 = No)
$MercadoPago['client_id'] = '8583418366013110'; // "CLIENT_ID" presente en la página https://www.mercadopago.com/mlb/account/credentials?type=basic
$MercadoPago['client_secret'] = 'CDhRdrnYrMHd15bjFoCzKBKl0jPF0y2i'; // "CLIENT_SECRET" presente en la página https://www.mercadopago.com/mlb/account/credentials?type=basic
$MercadoPago['access_token'] = 'APP_USR-8583418366013110-111709-9cbe929a817d2a37a151d406f8ae6396-2033218263'; // "Acess Token" presente en la página https://www.mercadopago.com/mlb/account/credentials?type=basic
$MercadoPago['currency'] = 'BRL'; // Código da moneda
$MercadoPago['symbol'] = 'R$'; // Simbolo da Moeda
$MercadoPago['coin_price'] = '1.00'; // Valor da moeda em Reais
$MercadoPago['testando'] = 0; // ¿Está probando el sistema a través de MercadoPago Sandbox? (1 = Sim | 0 = Não)

// PAYGOL CONFIGS:
$PayGol['service_id'] = '___SERVICE_ID___'; // "Service ID" obtido na página https://secure.paygol.com/dashboard/notifications
$PayGol['secret_key'] = '___SECRET_KEY___'; // "Secret key" obtido na página https://secure.paygol.com/dashboard/notifications
$PayGol['USD']['actived'] = 0; // Dolar ativo? (1 = Sim / 0 = Não)
$PayGol['USD']['coin_price'] = '0.25'; // Valor da quantidade comercializada (em Dolar)
$PayGol['BRL']['actived'] = 0; // Real ativo? (1 = Sim / 0 = Não)
$PayGol['BRL']['coin_price'] = '1.00'; // Valor da quantidade comercializada (em Reais)
$PayGol['EUR']['actived'] = 0; // Euro ativo? (1 = Sim / 0 = Não)
$PayGol['EUR']['coin_price'] = '0.25'; // Valor da quantidade comercializada (em Euros)

// PAYOP CONFIGS:
$PayOp['actived'] = 0; // Opção ativa? (1 = Sim / 0 = Não)
$PayOp['public_key'] = '--'; // PayOp Public Key
$PayOp['secret_key'] = '--'; // PayOp Secret Key
$PayOp['JWTToken'] = '--'; // JWT Token obtido em https://payop.com/en/profile/settings/jwt-token
$PayOp['currency'] = 'USD'; // Código da moeda
$PayOp['symbol'] = '$'; // Símbolo da moeda
$PayOp['coin_price'] = '0.50'; // Valor da quantidade comercializada (em Reais)
$PayOp['autoDelivery'] = 1; // Você deseja que a entrega do saldo seja feita de forma automática? (1 = Sim | 0 = Não) (se optar de forma manual, as doações pagas ficarão com status "Paga". Você terá que ir até o painel admin e concluí-las clicando no botão "Entregar". Quando concluir, o saldo será adicionado e o status passará a ser "Entregue")

// PICPAY:
$PicPay['actived'] = 0; // Opção ativa? (1 = Sim / 0 = Não)
$PicPay['currency'] = 'BRL'; // Código da moeda
$PicPay['coin_price'] = '1.00'; // Valor da quantidade comercializada
$PicPay['name'] = 'efireX';

// TRANSACAO BANCARIA:
$Banking['actived'] = 0; // Opção ativa? (1 = Sim / 0 = Não)
$Banking['currency'] = 'BRL'; // Código da moeda
$Banking['coin_price'] = '1.00'; // Valor da quantidade comercializada
$Banking['bank_dados'] = '
<b>Caixa econômica - Poupança</b><br />
<b>Agência: 3840</b><br />
<b>Conta: 00006913-0</b><br />
<b>Operação: 013</b><br />
<b>Nome: Angelo Carlos Polvora Moreira</b><br />
<b>___________________________________</b><br />
<br> 
<b>PIX: 73988952160</b><br />
<b>Nome: Angelo Carlos Polvora Moreira</b><br />
';
